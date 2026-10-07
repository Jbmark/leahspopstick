<?php

require_once '../../config/database.php';
require_once '../../app/models/OrderModel.php';
require_once '../../app/models/CustomerModel.php';

$customerModel = new CustomerModel($pdo);
$orderModel = new OrderModel();

// Fetch active products
$products = $pdo->query("
    SELECT *
    FROM products
    WHERE status = 'Active'
    ORDER BY product_name ASC
")->fetchAll(PDO::FETCH_ASSOC);

$error_message = "";

// Keep entered values if there is an error
$customer_name = trim($_POST['customer_name'] ?? '');
$contact_number = trim($_POST['contact_number'] ?? '');
$email = trim($_POST['email'] ?? '');
$delivery_address = trim($_POST['delivery_address'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

$product_ids = $_POST['product_id'] ?? [];
$quantities = $_POST['quantity'] ?? [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate customer information
    if ($customer_name === '') {

        $error_message = "Please enter your full name or business name.";

    } elseif ($contact_number === '') {

        $error_message = "Please enter your contact number.";

    } elseif ($delivery_address === '') {

        $error_message = "Please enter your delivery address.";

    } else {

        // Check if at least one valid product was selected
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
                $existing_customer =
                    $customerModel->findByContact($contact_number);

                if ($existing_customer) {

                    $customer_id =
                        $existing_customer['customer_id'];

                } else {

                    $customer_id = $customerModel->create(
                        $customer_name,
                        $contact_number,
                        $email,
                        $delivery_address
                    );
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
                    null
                );


                /*
                 * Redirect to tracking page
                 */
                header(
                    "Location: track.php?id=" .
                    $order_id .
                    "&success=1"
                );

                exit;

            } catch (Exception $e) {

                $error_message =
                    "Unable to submit order: " .
                    $e->getMessage();
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

<link
    rel="stylesheet"
    href="../css/shop.css"
>

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

    footer {
        text-align: center;
        padding: 25px 20px;
        background: #FFFFFF;
        border-top: 1px solid #E5E9F0;
        color: #7A869A;
        font-size: 13px;
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

        <a
            href="index.php"
            style="
                text-decoration:none;
                color:#1B5E20;
                font-size:20px;
                font-weight:700;
            "
        >
            LEAH'S POPSTICK
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

    <div class="form-card">


        <div class="order-top">

            <h1>Place an Order</h1>

            <a
                href="index.php"
                class="back-button"
            >
                ← Back to Products
            </a>

        </div>


        <p>
            Fill out the form below to submit your order request
            to Leah's Popstick Wood Products Manufacturing.
        </p>


        <?php if (!empty($error_message)) { ?>

            <div class="alert-box alert-error">

                <?= htmlspecialchars($error_message); ?>

            </div>

        <?php } ?>


        <form method="POST">


            <!-- CUSTOMER NAME -->

            <div class="field-group">

                <label>
                    Full Name / Business Name
                </label>

                <input
                    type="text"
                    name="customer_name"
                    value="<?= htmlspecialchars($customer_name); ?>"
                    required
                    placeholder="e.g., Juan Dela Cruz / ABC Ice Drop Co."
                >

            </div>


            <!-- CONTACT NUMBER -->

            <div class="field-group">

                <label>
                    Contact Number
                </label>

                <input
                    type="text"
                    name="contact_number"
                    value="<?= htmlspecialchars($contact_number); ?>"
                    required
                    placeholder="e.g., 09123456789"
                >

            </div>


            <!-- EMAIL -->

            <div class="field-group">

                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($email); ?>"
                    placeholder="Optional"
                >

            </div>


            <!-- DELIVERY ADDRESS -->

            <div class="field-group">

                <label>
                    Delivery Address
                </label>

                <textarea
                    name="delivery_address"
                    required
                    style="height:80px;"
                    placeholder="Enter your complete delivery address..."
                ><?= htmlspecialchars($delivery_address); ?></textarea>

            </div>


            <!-- REMARKS -->

            <div class="field-group">

                <label>
                    Order Notes / Remarks
                </label>

                <textarea
                    name="remarks"
                    style="height:60px;"
                    placeholder="Optional order notes..."
                ><?= htmlspecialchars($remarks); ?></textarea>

            </div>


            <hr
                style="
                    border:0;
                    border-top:1px solid #E5E9F0;
                    margin:25px 0;
                "
            >


            <h3 class="product-heading">
                Select Products
            </h3>


            <!-- PRODUCT SELECTION -->

            <div id="checkout-matrix-container">

                <?php for ($loop = 0; $loop < 3; $loop++) { ?>

                    <div class="item-row">

                        <select name="product_id[]">

                            <option value="">
                                -- Choose Product --
                            </option>

                            <?php foreach ($products as $p) { ?>

                                <option
                                    value="<?= $p['product_id']; ?>"
                                    <?= (
                                        isset($product_ids[$loop]) &&
                                        $product_ids[$loop] == $p['product_id']
                                    ) ? 'selected' : ''; ?>
                                >

                                    <?= htmlspecialchars(
                                        $p['product_name']
                                    ); ?>

                                    -
                                    ₱<?= number_format(
                                        $p['selling_price'],
                                        2
                                    ); ?>

                                    per
                                    <?= htmlspecialchars(
                                        $p['unit']
                                    ); ?>

                                </option>

                            <?php } ?>

                        </select>


                        <input
                            type="number"
                            name="quantity[]"
                            min="1"
                            step="1"
                            value="<?= htmlspecialchars(
                                $quantities[$loop] ?? ''
                            ); ?>"
                            placeholder="Qty"
                        >

                    </div>

                <?php } ?>

            </div>


            <button
                type="submit"
                class="btn-submit"
            >
                Submit Order
            </button>


        </form>

    </div>

</section>


<footer>

    <p>
        © <?= date('Y'); ?>
        Leah's Popstick Wood Products Manufacturing
    </p>

</footer>


</body>

</html>

