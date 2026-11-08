
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';
require_once '../../app/models/OrderModel.php';
require_once '../../app/models/CustomerModel.php';

$customerModel = new CustomerModel($pdo);
$orderModel = new OrderModel();

$loggedInCustomer = $_SESSION['customer'] ?? null;

if (!is_array($loggedInCustomer)) {
    $loggedInCustomer = null;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


// Fetch active products
$products = $pdo->query("
    SELECT p.*,
        (
            SELECT COALESCE(SUM(ib.quantity_remaining), 0)
            FROM inventory_batches ib
            WHERE ib.product_id = p.product_id
              AND ib.status = 'Available'
        ) AS current_stock
    FROM products p
    WHERE p.status = 'Active'
    ORDER BY p.product_name ASC
")->fetchAll(PDO::FETCH_ASSOC);


$error_message = "";

// Use submitted values after a validation error.
// Otherwise, prefill details from the logged-in customer.
$customer_name = trim(
    $_POST['customer_name']
    ?? ($loggedInCustomer['customer_name'] ?? '')
);

$contact_number = trim(
    $_POST['contact_number']
    ?? ($loggedInCustomer['contact_number'] ?? '')
);

$email = trim(
    $_POST['email']
    ?? ($loggedInCustomer['email'] ?? '')
);

$delivery_address = trim(
    $_POST['delivery_address']
    ?? ($loggedInCustomer['address'] ?? '')
);

$remarks = trim($_POST['remarks'] ?? '');

$product_ids = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];

$selected_product_id = (int) ($_GET['product_id'] ?? 0);

// Add the clicked product to the session cart immediately.
if ($selected_product_id > 0) {
    foreach ($products as $product) {
        $productId = (int) $product['product_id'];

        if ($productId === $selected_product_id) {
            if (!isset($_SESSION['cart'][$productId])) {
                $_SESSION['cart'][$productId] = 1;
            }

            break;
        }
    }
}

// Always display the saved session cart.
$displayCart = $_SESSION['cart'];

// Keep only active products and valid positive quantities.
$displayCart = array_filter(
    $displayCart,
    function ($quantity) {
        return filter_var($quantity, FILTER_VALIDATE_INT) !== false
            && (int) $quantity > 0;
    }
);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
        // REMOVE ONE PRODUCT FROM THE SAVED CART
    if (isset($_POST['remove_product_id'])) {

        $removeId = filter_var(
            $_POST['remove_product_id'],
            FILTER_VALIDATE_INT
        );

        if ($removeId !== false && $removeId > 0) {
            unset($_SESSION['cart'][$removeId]);
        }

        // Redirect without product_id so the removed item
        // is not automatically added again.
        header('Location: order.php');
        exit;
    }

    // ADD TO CART WITHOUT SUBMITTING AN ORDER
    if (($_POST['cart_action'] ?? '') === 'continue_shopping') {

        $cartProductIds = $_POST['product_id'] ?? [];
        $cartQuantities = $_POST['quantity'] ?? [];
        $newCart = [];
        $cartError = '';

        if (!is_array($cartProductIds) || !is_array($cartQuantities)) {
            $cartError = 'Invalid cart data. Please try again.';
        } else {
            foreach ($cartProductIds as $index => $rawProductId) {

                $productId = filter_var(
                    $rawProductId,
                    FILTER_VALIDATE_INT
                );

                $rawQuantity = trim(
                    (string) ($cartQuantities[$index] ?? '')
                );

                if (
                    $productId === false ||
                    $productId < 1 ||
                    !preg_match('/^[1-9][0-9]*$/', $rawQuantity)
                ) {
                    $cartError = 'Please enter a valid product and quantity.';
                    break;
                }

                $quantity = filter_var(
                    $rawQuantity,
                    FILTER_VALIDATE_INT
                );

                if ($quantity === false || $quantity < 1) {
                    $cartError = 'Please enter a valid whole-number quantity.';
                    break;
                }

                $checkProduct = $pdo->prepare("
                    SELECT
                        p.product_id,
                        COALESCE(
                            SUM(
                                CASE
                                    WHEN ib.status = 'Available'
                                    THEN ib.quantity_remaining
                                    ELSE 0
                                END
                            ),
                            0
                        ) AS current_stock
                    FROM products p
                    LEFT JOIN inventory_batches ib
                        ON ib.product_id = p.product_id
                    WHERE p.product_id = ?
                      AND p.status = 'Active'
                    GROUP BY p.product_id
                    LIMIT 1
                ");

                $checkProduct->execute([$productId]);

                $productCheck = $checkProduct->fetch(PDO::FETCH_ASSOC);

                if (!$productCheck) {
                    $cartError = 'One of the selected products is unavailable.';
                    break;
                }

                if ($quantity > (int) $productCheck['current_stock']) {
                    $cartError = 'The requested quantity exceeds available stock.';
                    break;
                }

                // Prevent duplicate product entries.
                if (isset($newCart[$productId])) {
                    $cartError = 'A product appears more than once in the cart.';
                    break;
                }

                $newCart[$productId] = $quantity;
            }
        }

        if ($cartError !== '') {
            $error_message = $cartError;

        } elseif (empty($newCart)) {
            $error_message = 'Please select at least one product.';

        } else {
            // Save the complete cart, including previous selections.
            $_SESSION['cart'] = $newCart;

            header('Location: index.php?cart=updated#catalog');
            exit;
        }

    } else {

        // SUBMIT ORDER: KEEP CUSTOMER VALIDATION AND ORDER CREATION

        if ($customer_name === '') {

            $error_message = "Please enter your full name or business name.";

        } elseif ($contact_number === '') {

            $error_message = "Please enter your contact number.";

        } elseif ($delivery_address === '') {

            $error_message = "Please enter your delivery address.";

        } else {

            $has_items = false;

            foreach ($product_ids as $index => $product_id) {
                $quantity = intval($quantities[$index] ?? 0);

                if (intval($product_id) > 0 && $quantity > 0) {
                    $has_items = true;
                    break;
                }
            }

            if (!$has_items) {

                $error_message =
                    "Please select at least one product with a valid quantity.";

            } else {

                try {

                    /*
                     * CUSTOMER MANAGEMENT
                     */

                    if ($loggedInCustomer !== null) {

                        $customer_id =
                            (int) $loggedInCustomer['customer_id'];

                        $updateCustomer = $pdo->prepare(
                            "UPDATE customers
                             SET customer_name = ?,
                                 contact_number = ?,
                                 email = ?,
                                 address = ?
                             WHERE customer_id = ?"
                        );

                        $updateCustomer->execute([
                            $customer_name,
                            $contact_number,
                            $email !== '' ? $email : null,
                            $delivery_address,
                            $customer_id
                        ]);

                    } else {

                        // Preserve guest checkout behavior.
                        $existing_customer =
                            $customerModel->findByContact($contact_number);

                        if ($existing_customer) {

                            $customer_id =
                                (int) $existing_customer['customer_id'];

                        } else {

                            $customer_id = $customerModel->create(
                                $customer_name,
                                $contact_number,
                                $email !== '' ? $email : null,
                                $delivery_address
                            );
                        }
                    }

                    /*
                     * CREATE WEBSITE ORDER
                     */

                    $order_id = $orderModel->createOrder(
                        $customer_id,
                        'Website',
                        $delivery_address,
                        $remarks,
                        $product_ids,
                        $quantities,
                        null,
                        $customer_name,
                        $contact_number
                    );

                    // Clear the cart only after successful order creation.
                    $_SESSION['cart'] = [];

                    /*
                     * REDIRECT TO TRACKING PAGE
                     */

                    header(
                        "Location: track.php?id=" .
                        $order_id .
                        "&success=1"
                    );

                    exit;

                } catch (Exception $e) {

                    $error_message =
                        "Unable to submit order: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Place Order - Leah's Popstick</title>

    <link rel="stylesheet" href="../css/shop.css?v=checkout-layout2">

<style>

    /* =========================
       ORDER PAGE
    ========================= */

    .form-section {
        padding: 50px 20px 70px;
        background: #FAFAFA;
        min-height: 80vh;
    }

    .form-card {
        background: #FFFFFF;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        width: 100%;
        max-width: 700px;
        margin: 0 auto;
        border: 1px solid #E5E9F0;
        box-sizing: border-box;
    }

    .order-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .order-top h1 {
        margin: 0;
        color: #1B5E20;
        font-size: 26px;
    }

    .back-button {
        display: inline-block;
        padding: 10px 16px;
        background: #FFFFFF;
        color: #1B5E20;
        border: 1px solid #1B5E20;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        white-space: nowrap;
    }

    .back-button:hover {
        background: #1B5E20;
        color: #FFFFFF;
    }

    .form-card > p {
        color: #7A869A;
        font-size: 14px;
        margin-bottom: 25px;
        line-height: 1.6;
    }

    .field-group {
        margin-bottom: 20px;
    }

    .field-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #555;
        margin-bottom: 6px;
    }

    .field-group input,
    .field-group textarea,
    .field-group select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #E0E0E0;
        border-radius: 8px;
        font-size: 15px;
        background: #FAFAFA;
        box-sizing: border-box;
        font-family: inherit;
    }

    .field-group input:focus,
    .field-group textarea:focus,
    .field-group select:focus {
        outline: none;
        border-color: #2E7D32;
        background: #FFFFFF;
        box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
    }

    .item-row {
        display: grid;
        grid-template-columns: 3fr 1fr;
        gap: 16px;
        margin-bottom: 12px;
    }

    .alert-box {
        padding: 14px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .alert-error {
        background: #FFEBEE;
        color: #C62828;
        border-left: 4px solid #D32F2F;
    }

    .product-heading {
        margin-bottom: 12px;
        color: #1B5E20;
        font-size: 17px;
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        background: #1B5E20;
        color: #FFFFFF;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background: #2E7D32;
    }
    
    /* ADD / REMOVE PRODUCT ROWS */

.item-row {
    display: grid;
    grid-template-columns: 3fr 1fr auto;
    gap: 12px;
    margin-bottom: 12px;
    align-items: center;
}

.btn-remove-product {
    padding: 12px;
    border: 1px solid #E0B4B4;
    border-radius: 8px;
    background: #FFF5F5;
    color: #C62828;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
}

.btn-remove-product:hover {
    background: #FFEBEE;
}

.btn-add-product {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 13px;
    margin: 4px 0 18px;
    border: 1px dashed #2E7D32;
    border-radius: 8px;
    background: #F1F8F1;
    color: #1B5E20;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.btn-add-product:hover {
    background: #E4F2E4;
}

@media (max-width: 600px) {
    .item-row {
        grid-template-columns: 1fr;
        gap: 8px;
        padding-bottom: 14px;
        border-bottom: 1px solid #E5E9F0;
    }

    .btn-remove-product {
        width: 100%;
    }
}

    footer {
        text-align: center;
        padding: 25px 20px;
        background: #FFFFFF;
        border-top: 1px solid #E5E9F0;
        color: #7A869A;
        font-size: 13px;
    }
    
    /* =========================
   ORDER SUMMARY
========================= */

.order-summary {
    margin-top: 28px;
    padding: 24px;
    border: 1px solid #E5E9F0;
    border-radius: 12px;
    background: #F8FAF8;
}

.order-summary h3 {
    margin: 0 0 6px;
    color: #1B5E20;
    font-size: 20px;
}

.summary-description {
    margin: 0 0 20px;
    color: #7A869A;
    font-size: 13px;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #E5E9F0;
}

.summary-item-name {
    font-weight: 600;
    color: #263238;
    overflow-wrap: anywhere;
}

.summary-item-details {
    margin-top: 5px;
    color: #7A869A;
    font-size: 13px;
    line-height: 1.6;
}

.summary-item-total {
    font-weight: 700;
    color: #263238;
    text-align: right;
    white-space: nowrap;
}

.summary-empty {
    padding: 18px 0;
    color: #7A869A;
    font-size: 14px;
}

.summary-totals {
    padding-top: 18px;
}

.summary-total-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
    color: #455A64;
    font-size: 14px;
}

.summary-grand-total {
    margin-top: 18px;
    padding-top: 18px;
    border-top: 2px solid #DCE5DC;
    color: #1B5E20;
    font-size: 17px;
    font-weight: 700;
}

.summary-grand-total strong {
    font-size: 25px;
    overflow-wrap: anywhere;
}

.summary-notice {
    margin-top: 12px;
    color: #7A869A;
    font-size: 12px;
    line-height: 1.5;
}

@media (max-width: 600px) {
    .order-summary {
        padding: 18px;
    }

    .summary-item {
        gap: 10px;
    }

    .summary-grand-total strong {
        font-size: 21px;
    }
}


    /* =========================
       MOBILE
    ========================= */

    @media (max-width: 600px) {

        .form-section {
            padding: 30px 15px 50px;
        }

        .form-card {
            padding: 25px 20px;
        }

        .order-top {
            align-items: flex-start;
            flex-direction: column;
        }

        .back-button {
            width: 100%;
            text-align: center;
            box-sizing: border-box;
        }

        .item-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

    }
    
/* =========================================
   PLACE ORDER - RESPONSIVE LAYOUT FIX
   ========================================= */

/* Prevent form elements from overflowing */
.form-card,
.form-card * {
    box-sizing: border-box;
}

#checkout-matrix-container {
    width: 100%;
    min-width: 0;
}

#checkout-matrix-container .item-row {
    display: grid;
    grid-template-columns: minmax(0, 3fr) minmax(75px, 1fr) auto;
    gap: 10px;
    align-items: center;
    width: 100%;
    min-width: 0;
    margin-bottom: 14px;
}

#checkout-matrix-container .item-row select,
#checkout-matrix-container .item-row input {
    display: block;
    width: 100%;
    min-width: 0;
    max-width: 100%;
    margin: 0;
    padding: 11px 10px;
    font-size: 14px;
}

#checkout-matrix-container .btn-remove-product {
    width: auto;
    padding: 10px 12px;
    margin: 0;
    font-size: 12px;
}

/* Keep the form within the mobile screen */
.form-section {
    width: 100%;
    min-width: 0;
}

.form-card {
    min-width: 0;
    max-width: 700px;
}

/* Mobile layout */
@media screen and (max-width: 600px) {
    .form-section {
        padding: 22px 12px 35px;
    }

    .form-card {
        padding: 20px 14px;
        border-radius: 12px;
    }

    .order-top h1 {
        font-size: 23px;
    }

    #checkout-matrix-container .item-row {
        grid-template-columns: minmax(0, 1fr) auto;
        gap: 9px;
        padding: 0 0 14px;
        margin-bottom: 14px;
        border-bottom: 1px solid #E5E9F0;
    }

    /* Product dropdown gets its own full-width row */
    #checkout-matrix-container .item-row select {
        grid-column: 1 / -1;
    }

    /* Quantity input and compact Remove button share a row */
    #checkout-matrix-container .item-row input {
        grid-column: 1;
    }

    #checkout-matrix-container .btn-remove-product {
        grid-column: 2;
        width: auto;
        padding: 10px 12px;
        white-space: nowrap;
    }

    .btn-add-product {
        padding: 11px;
        font-size: 13px;
    }

    .order-summary {
        padding: 14px;
        margin-top: 20px;
    }

    .summary-item {
        gap: 8px;
    }

    .summary-grand-total strong {
        font-size: 19px;
    }

    .btn-submit {
        padding: 13px;
    }
}

/* Extra-small phones */
@media screen and (max-width: 360px) {
    .form-section {
        padding-left: 8px;
        padding-right: 8px;
    }

    .form-card {
        padding: 16px 10px;
    }

    #checkout-matrix-container .item-row select,
    #checkout-matrix-container .item-row input {
        font-size: 13px;
        padding: 10px 8px;
    }

    #checkout-matrix-container .btn-remove-product {
        padding: 9px;
        font-size: 11px;
    }
}
    
    /* Selected product card */
.selected-product-card {
    display: grid;
    grid-template-columns: 180px minmax(0, 1fr);
    gap: 22px;
    padding: 20px;
    margin: 20px 0 28px;
    background: #FFFFFF;
    border: 1px solid #E5E9F0;
    border-radius: 14px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
}

.selected-product-image {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 160px;
    overflow: hidden;
    border-radius: 10px;
    background: #F5F7F5;
    color: #1B5E20;
}

.selected-product-image img {
    width: 100%;
    height: 180px;
    object-fit: contain;
}

.selected-product-details h3 {
    margin: 8px 0;
    color: #263238;
    font-size: 21px;
}

.selected-product-details p {
    margin: 8px 0 14px;
    color: #687580;
    font-size: 14px;
    line-height: 1.6;
}

.selected-product-price {
    color: #1B5E20;
    font-size: 23px;
    font-weight: 700;
}

.selected-product-price span {
    color: #687580;
    font-size: 13px;
    font-weight: 400;
}

.selected-product-stock {
    margin-top: 10px;
    color: #687580;
    font-size: 13px;
}

@media (max-width: 600px) {
    .selected-product-card {
        grid-template-columns: 1fr;
        gap: 14px;
        padding: 14px;
    }

    .selected-product-image img {
        height: 200px;
    }

    .selected-product-details h3 {
        font-size: 19px;
    }
}

    
/* MOBILE FIX: CART QUANTITY AND REMOVE BUTTON */
@media screen and (max-width: 760px) {
    .checkout-layout {
        min-width: 0;
        width: 100%;
        padding-left: 12px;
        padding-right: 12px;
        box-sizing: border-box;
    }

    .checkout-panel {
        min-width: 0;
        width: 100%;
        box-sizing: border-box;
    }

    #checkout-matrix-container,
    .visual-cart {
        width: 100%;
        min-width: 0;
        overflow: visible;
        box-sizing: border-box;
    }

    .cart-product-card {
        display: grid;
        grid-template-columns: 60px minmax(0, 1fr);
        gap: 10px;
        width: 100%;
        min-width: 0;
        overflow: visible;
        box-sizing: border-box;
    }

    .cart-product-image {
        width: 60px;
        min-width: 0;
    }

    .cart-product-image img {
        display: block;
        width: 100%;
        max-width: 60px;
        height: 60px;
        object-fit: contain;
    }

    .cart-product-info {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .cart-product-actions {
        grid-column: 1 / -1;
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: flex-start;
        align-items: flex-end;
        gap: 12px;
        width: 100%;
        min-width: 0;
        margin: 8px 0 0;
        padding: 0;
        overflow: visible;
        box-sizing: border-box;
    }

    .cart-quantity {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
        flex: 0 1 auto;
        min-width: 0;
    }

    .cart-quantity label {
        display: block;
        margin: 0;
    }

    .cart-quantity input {
        display: block;
        width: 100px;
        min-width: 0;
        max-width: 100%;
        margin: 0;
        box-sizing: border-box;
    }

    .cart-remove-button {
        display: inline-flex;
        flex: 0 0 auto;
        align-items: center;
        justify-content: center;
        visibility: visible;
        opacity: 1;
        position: static;
        width: auto;
        min-width: 75px;
        max-width: 100%;
        margin: 0;
        padding: 10px 12px;
        white-space: nowrap;
        box-sizing: border-box;
    }
}

@media screen and (max-width: 380px) {
    .cart-product-actions {
        gap: 8px;
    }

    .cart-quantity input {
        width: 85px;
    }

    .cart-remove-button {
        min-width: 70px;
        padding: 9px 10px;
    }
}


</style>

</head>


<body>


<!-- =========================
     STOREFRONT HEADER
========================= -->

<header
    style="
        background:#FFFFFF;
        border-bottom:1px solid #E5E9F0;
        padding:18px 6%;
    "
>

    <div
        style="
            max-width:1200px;
            margin:0 auto;
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:20px;
        "
    >

       <a href="index.php" class="nav-brand">
        <img
           src="images/leah-logo.png"
            alt="Leah Wooden Products"
            class="brand-logo"
        >
        <span class="brand-name">Leah Wooden Products</span>
    </a>


        <a
            href="track.php"
            style="
                text-decoration:none;
                color:#1B5E20;
                font-size:14px;
                font-weight:600;
            "
        >
            Track Order
        </a>

    </div>

</header>


<!-- =========================
     ORDER FORM
========================= -->


<section class="form-section">
    <div class="checkout-layout">

        <!-- LEFT: CUSTOMER INFORMATION -->
        <div class="checkout-panel">
            <h2>Customer Information</h2>
            <p class="checkout-subtitle">
                Please verify your delivery details.
            </p>

            <?php if (!empty($error_message)) { ?>
                <div class="alert-box alert-error">
                    <?= htmlspecialchars($error_message); ?>
                </div>
            <?php } ?>

            <?php if ($loggedInCustomer !== null) { ?>

                <div class="customer-profile">
                    <div class="customer-profile-row">
                        <span class="customer-profile-label">Full Name</span>
                        <span class="customer-profile-value">
                            <?= htmlspecialchars($customer_name); ?>
                        </span>
                    </div>

                    <div class="customer-profile-row">
                        <span class="customer-profile-label">Contact Number</span>
                        <span class="customer-profile-value">
                            <?= htmlspecialchars($contact_number); ?>
                        </span>
                    </div>

                    <div class="customer-profile-row">
                        <span class="customer-profile-label">Email Address</span>
                        <span class="customer-profile-value">
                            <?= htmlspecialchars($email ?: 'Not provided'); ?>
                        </span>
                    </div>

                    <div class="customer-profile-row">
                        <span class="customer-profile-label">Delivery Address</span>
                        <span class="customer-profile-value">
                            <?= htmlspecialchars($delivery_address); ?>
                        </span>
                    </div>
                </div>

            <?php } else { ?>

                <div class="guest-fields">
                    <div class="field-group">
                        <label for="customer_name">Full Name / Business Name</label>
                        <input
                            form="order-form"
                            id="customer_name"
                            type="text"
                            name="customer_name"
                            value="<?= htmlspecialchars($customer_name); ?>"
                            required
                        >
                    </div>

                    <div class="field-group">
                        <label for="contact_number">Contact Number</label>
                        <input
                            form="order-form"
                            id="contact_number"
                            type="text"
                            name="contact_number"
                            value="<?= htmlspecialchars($contact_number); ?>"
                            required
                        >
                    </div>

                    <div class="field-group">
                        <label for="email">Email Address (Optional)</label>
                        <input
                            form="order-form"
                            id="email"
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($email); ?>"
                        >
                    </div>

                    <div class="field-group">
                        <label for="delivery_address">Delivery Address</label>
                        <textarea
                            form="order-form"
                            id="delivery_address"
                            name="delivery_address"
                            required
                        ><?= htmlspecialchars($delivery_address); ?></textarea>
                    </div>
                </div>

            <?php } ?>

            <div class="checkout-notes">
                <label for="remarks">Order Notes / Remarks (Optional)</label>
                <textarea
                    form="order-form"
                    id="remarks"
                    name="remarks"
                    placeholder="Add any special instructions..."
                ><?= htmlspecialchars($remarks); ?></textarea>
            </div>
        </div>

        <!-- RIGHT: CART AND ORDER SUMMARY -->
        <div class="checkout-panel">
            <h2>Your Cart</h2>
            <p class="checkout-subtitle">
                Adjust quantities or remove products before submitting.
            </p>

            <form method="POST" id="order-form">

                
<div id="checkout-matrix-container" class="visual-cart">

    <?php foreach ($displayCart as $cartProductId => $cartQuantity) { ?>

        <?php
        $cartProduct = null;

        foreach ($products as $product) {
            if ((int) $product['product_id'] === (int) $cartProductId) {
                $cartProduct = $product;
                break;
            }
        }

        if ($cartProduct === null) {
            continue;
        }
        ?>

        <div class="cart-product-card">

            <div class="cart-product-image">
                <?php if (!empty($cartProduct['image'])) { ?>
                    <img
                        src="/public/<?= htmlspecialchars(
                            ltrim($cartProduct['image'], '/'),
                            ENT_QUOTES,
                            'UTF-8'
                        ); ?>"
                        alt="<?= htmlspecialchars($cartProduct['product_name']); ?>"
                    >
                <?php } else { ?>
                    <span>🪵 Wooden Product</span>
                <?php } ?>
            </div>

            
<div class="cart-product-info">
    <h4><?= htmlspecialchars($cartProduct['product_name']); ?></h4>

    <p>
        ₱<?= number_format((float) $cartProduct['selling_price'], 2); ?>
        / <?= htmlspecialchars($cartProduct['unit']); ?>
    </p>

    <small>
        Stock:
        <?= number_format((float) $cartProduct['current_stock'], 0); ?>
        <?= htmlspecialchars($cartProduct['unit']); ?>
    </small>

    <input
        type="hidden"
        name="product_id[]"
        value="<?= (int) $cartProductId; ?>"
    >
</div>

<div class="cart-product-actions">

    <div class="cart-quantity">
        <label for="qty-<?= (int) $cartProductId; ?>">Quantity</label>

        <input
            id="qty-<?= (int) $cartProductId; ?>"
            type="number"
            name="quantity[]"
            min="1"
            max="<?= max(1, (int) $cartProduct['current_stock']); ?>"
            step="1"
            value="<?= (int) $cartQuantity; ?>"
            required
        >
    </div>

    <button
        type="submit"
        name="remove_product_id"
        value="<?= (int) $cartProductId; ?>"
        class="cart-remove-button"
        formnovalidate
        onclick="return confirm('Remove this product from your cart?');"
    >
        Remove
    </button>

</div>


        </div>

    <?php } ?>

    <?php if (empty($displayCart)) { ?>
        <div class="cart-empty">
            Your cart is empty. Return to the catalog to add products.
        </div>
    <?php } ?>

</div>

<!-- ORDER SUMMARY: OUTSIDE THE PRODUCT LOOP -->
<div class="order-summary">

    <h3>Order Summary</h3>

    <p class="summary-description">
        Review your selected products and the estimated order total.
    </p>

    <div id="summary-items"></div>

    <div class="summary-totals">

        <div class="summary-total-row">
            <span>Subtotal</span>
            <strong id="summary-subtotal">₱0.00</strong>
        </div>

        <div class="summary-total-row">
            <span>Charges</span>
            <strong id="summary-charges">₱0.00</strong>
        </div>

        <div class="summary-total-row summary-grand-total">
            <span>Total</span>
            <strong id="summary-total">₱0.00</strong>
        </div>

    </div>

    <p id="summary-notice" class="summary-notice"></p>

</div>


                <div class="checkout-actions">
                    <button
                        type="submit"
                        class="btn-checkout-submit"
                    >
                        Submit Order
                    </button>

                    <button
                        type="submit"
                        name="cart_action"
                        value="continue_shopping"
                        class="btn-checkout-continue"
                        formnovalidate
                    >
                        + Add to Cart &amp; Continue Shopping
                    </button>
                </div>

            </form>
        </div>

    </div>
</section>


<footer>

    <p>
        © <?= date('Y'); ?>
        Leah's Popstick Wood Products Manufacturing
    </p>

</footer>
    

<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.visual-cart');
    const summaryItems = document.getElementById('summary-items');
    const subtotalDisplay = document.getElementById('summary-subtotal');
    const chargesDisplay = document.getElementById('summary-charges');
    const totalDisplay = document.getElementById('summary-total');
    const notice = document.getElementById('summary-notice');
    const orderForm = document.querySelector('form[method="POST"]');

    if (!container || !orderForm) return;

    const charges = 0;

    const peso = new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    });

    function makeElement(tag, className, text) {
        const element = document.createElement(tag);

        if (className) element.className = className;
        if (text !== undefined) element.textContent = text;

        return element;
    }

    function calculateSummary() {
        summaryItems.replaceChildren();

        let subtotal = 0;
        let itemCount = 0;
        let invalidQuantity = false;

        const cards = container.querySelectorAll('.cart-product-card');

        cards.forEach(function (card) {
            const idInput = card.querySelector('input[name="product_id[]"]');
            const quantityInput = card.querySelector('input[name="quantity[]"]');
            const nameElement = card.querySelector('.cart-product-info h4');
            const priceElement = card.querySelector('.cart-product-info p');

            if (!idInput || !quantityInput || !nameElement || !priceElement) {
                return;
            }

            const name = nameElement.textContent.trim();
            const priceMatch = priceElement.textContent.match(/₱\s*([\d,]+(?:\.\d{1,2})?)/);
            const price = priceMatch
                ? Number(priceMatch[1].replace(/,/g, ''))
                : NaN;

            const rawQuantity = quantityInput.value.trim();
            const validQuantity =
                /^[1-9]\d*$/.test(rawQuantity) &&
                Number.isSafeInteger(Number(rawQuantity));

            if (!Number.isFinite(price) || price < 0) {
                invalidQuantity = true;
                return;
            }

            if (!validQuantity) {
                invalidQuantity = true;
            }

            const quantity = validQuantity ? Number(rawQuantity) : 0;
            const lineTotal = price * quantity;

            if (validQuantity) {
                subtotal += lineTotal;
            }

            itemCount++;

            const item = makeElement('div', 'summary-item');
            const details = makeElement('div');
            const amount = makeElement('div');

            details.appendChild(
                makeElement('div', 'summary-item-name', name)
            );

            details.appendChild(
                makeElement(
                    'div',
                    'summary-item-details',
                    'Price: ' + peso.format(price)
                )
            );

            details.appendChild(
                makeElement(
                    'div',
                    'summary-item-details',
                    validQuantity
                        ? 'Quantity: ' + quantity
                        : 'Please enter a valid whole-number quantity.'
                )
            );

            amount.appendChild(
                makeElement(
                    'div',
                    'summary-item-total',
                    validQuantity ? peso.format(lineTotal) : '—'
                )
            );

            amount.appendChild(
                makeElement('div', 'summary-item-details', 'Subtotal')
            );

            item.appendChild(details);
            item.appendChild(amount);
            summaryItems.appendChild(item);
        });

        if (itemCount === 0) {
            summaryItems.appendChild(
                makeElement(
                    'div',
                    'summary-empty',
                    'No products selected yet. Return to the catalog.'
                )
            );
        }

        subtotalDisplay.textContent = peso.format(subtotal);
        chargesDisplay.textContent = peso.format(charges);
        totalDisplay.textContent = peso.format(subtotal + charges);

        if (itemCount === 0) {
            notice.textContent = 'Choose a product from the catalog first.';
        } else if (invalidQuantity) {
            notice.textContent = 'Check the quantities before continuing.';
        } else {
            notice.textContent =
                itemCount + ' product(s). Final stock and prices are validated by the server.';
        }
    }

    container.addEventListener('input', calculateSummary);
    container.addEventListener('change', calculateSummary);

    orderForm.addEventListener('submit', function (event) {
    const submitter = event.submitter;

    // Allow removing a product without quantity validation.
    if (submitter && submitter.name === 'remove_product_id') {
        return;
    }

    const action = submitter ? submitter.value : '';

        const cards = container.querySelectorAll('.cart-product-card');

        if (cards.length === 0) {
            event.preventDefault();
            alert('Please select a product before continuing.');
            return;
        }

        let invalid = false;

        cards.forEach(function (card) {
            const quantityInput = card.querySelector('input[name="quantity[]"]');

            if (
                !quantityInput ||
                !/^[1-9]\d*$/.test(quantityInput.value.trim()) ||
                !Number.isSafeInteger(Number(quantityInput.value))
            ) {
                invalid = true;
            }
        });

        if (invalid) {
            event.preventDefault();
            calculateSummary();
            alert('Please enter a valid whole-number quantity for every product.');
            return;
        }

        // The PHP handler must distinguish this action from checkout.
        if (action === 'continue_shopping') {
            return;
        }
    });

    calculateSummary();
});
</script>



</body>

</html>

