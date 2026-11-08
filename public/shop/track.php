
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../config/database.php';

$loggedInCustomer = $_SESSION['customer'] ?? null;

if (!is_array($loggedInCustomer)) {
    $loggedInCustomer = null;
}

$order = null;
$error = "";
$success_message = "";
$stockSnapshots = [];
$customerOrders = [];
$orderItems = [];

$prefill_order_id = trim($_GET['id'] ?? '');
$order_input = trim($_POST['order_id'] ?? $prefill_order_id);
$contact = trim($_POST['contact_number'] ?? '');

// Load order history for the logged-in customer only.
if ($loggedInCustomer !== null) {
    $ordersStmt = $pdo->prepare("
        SELECT order_id, status, total_amount, created_at
        FROM orders
        WHERE customer_id = ?
        ORDER BY created_at DESC, order_id DESC
    ");

    $ordersStmt->execute([
        (int) $loggedInCustomer['customer_id']
    ]);

    $customerOrders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);
}

// Determine whether an order should be loaded.
$requestedOrderId = '';
$shouldLookup = false;

if (
    $loggedInCustomer !== null
    && $_SERVER['REQUEST_METHOD'] === 'GET'
    && $prefill_order_id !== ''
) {
    // Clicking an order in My Orders loads it automatically.
    $requestedOrderId = preg_replace('/[^0-9]/', '', $prefill_order_id);
    $shouldLookup = $requestedOrderId !== '';

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $requestedOrderId = preg_replace('/[^0-9]/', '', strtoupper($order_input));

    if ($requestedOrderId === '' || $contact === '') {
        $error = "Please enter both your Order ID and Contact Number.";
    } else {
        $shouldLookup = true;
    }
}

if ($shouldLookup) {

    $sql = "
        SELECT
            o.order_id,
            o.customer_id,
            o.delivery_address,
            o.remarks,
            o.status,
            o.total_amount,
            o.created_at,
            o.completed_at,
            COALESCE(NULLIF(o.customer_name, ''), c.customer_name) AS customer_name,
            COALESCE(NULLIF(o.contact_number, ''), c.contact_number) AS contact_number
        FROM orders o
        INNER JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?
    ";

    $params = [(int) $requestedOrderId];

    if ($loggedInCustomer !== null) {
        $sql .= " AND o.customer_id = ?";
        $params[] = (int) $loggedInCustomer['customer_id'];
    } else {
        $sql .= "
            AND COALESCE(
                NULLIF(o.contact_number, ''),
                c.contact_number
            ) = ?
        ";
        $params[] = $contact;
    }

    $sql .= " LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
        
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $error = "Order not found. Please check your order details.";
} else {
    $success_message = "Order found successfully.";

    // LOAD ORDER PRODUCTS
    $itemStmt = $pdo->prepare("
        SELECT
            od.quantity,
            od.unit_price,
            od.subtotal,
            p.product_name,
            p.unit
        FROM order_details od
        INNER JOIN products p
            ON p.product_id = od.product_id
        WHERE od.order_id = ?
        ORDER BY od.order_detail_id ASC
    ");

    $itemStmt->execute([
        (int) $order['order_id']
    ]);

    $orderItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

    // LOAD STOCK SNAPSHOTS FOR COMPLETED ORDERS
    if ($order['status'] === 'Completed') {
        $snapshotStmt = $pdo->prepare("
            SELECT
                oss.quantity_ordered,
                oss.stock_before,
                oss.stock_after,
                oss.recorded_at,
                p.product_name,
                p.unit
            FROM order_stock_snapshots oss
            INNER JOIN products p
                ON p.product_id = oss.product_id
            WHERE oss.order_id = ?
            ORDER BY p.product_name ASC
        ");

        $snapshotStmt->execute([
            $order['order_id']
        ]);

        $stockSnapshots = $snapshotStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

        if ($order['status'] === 'Completed') {
            $snapshotStmt = $pdo->prepare("
                SELECT
                    oss.quantity_ordered,
                    oss.stock_before,
                    oss.stock_after,
                    oss.recorded_at,
                    p.product_name,
                    p.unit
                FROM order_stock_snapshots oss
                INNER JOIN products p
                    ON p.product_id = oss.product_id
                WHERE oss.order_id = ?
                ORDER BY p.product_name ASC
            ");

            $snapshotStmt->execute([
                $order['order_id']
            ]);

            $stockSnapshots = $snapshotStmt->fetchAll(PDO::FETCH_ASSOC);
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

<title>Track Order - Leah's Popstick</title>

   <link rel="stylesheet" href="../css/shop.css?v=logo-fix1"> 

</head>

<body style="background:#F8F6EF;">

<!-- STORE HEADER -->

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
        href="order.php"
        style="
            text-decoration:none;
            color:#1B5E20;
            font-size:14px;
            font-weight:600;
        "
    >
        Place an Order
    </a>

</div>


</header>

<!-- TRACK ORDER CONTENT -->

<main class="track-page-layout">

    <!-- LEFT: MY ORDERS OR GUEST TRACKING FORM -->
    <section class="track-orders-panel">

        <?php if ($loggedInCustomer !== null) { ?>

            <h2>My Orders</h2>

            <p style="color:#666;font-size:14px;margin-bottom:20px;">
                Select an order to view its details.
            </p>

            <?php if (empty($customerOrders)) { ?>

                <p style="color:#666;">
                    You haven't placed any orders yet.
                </p>

                <a href="order.php" style="color:#1B5E20;">
                    Place your first order →
                </a>

            <?php } else { ?>

                <?php foreach ($customerOrders as $customerOrder) { ?>

                    <a
                        class="track-order-item <?= (
                            $order
                            && (int)$order['order_id'] === (int)$customerOrder['order_id']
                        ) ? 'active' : ''; ?>"
                        href="track.php?id=<?= (int)$customerOrder['order_id']; ?>"
                    >

                        <div style="
                            display:flex;
                            justify-content:space-between;
                            gap:10px;
                            flex-wrap:wrap;
                        ">
                            <strong>
                                Order #ORD-<?= (int)$customerOrder['order_id']; ?>
                            </strong>

                            <span style="
                                font-size:12px;
                                font-weight:700;
                                color:<?= $customerOrder['status'] === 'Completed'
                                    ? '#2E7D32'
                                    : ($customerOrder['status'] === 'Cancelled'
                                        ? '#C62828'
                                        : '#8A6D1D'); ?>;
                            ">
                                <?= htmlspecialchars($customerOrder['status']); ?>
                            </span>
                        </div>

                        <p style="
                            margin:9px 0 0;
                            color:#666;
                            font-size:13px;
                        ">
                            <?= date(
                                'M d, Y',
                                strtotime($customerOrder['created_at'])
                            ); ?>
                        </p>

                        <p style="
                            margin:7px 0 0;
                            font-weight:700;
                            color:#1B5E20;
                        ">
                            ₱<?= number_format(
                                (float)$customerOrder['total_amount'],
                                2
                            ); ?>
                        </p>

                        <span style="
                            display:block;
                            margin-top:10px;
                            color:#1B5E20;
                            font-size:13px;
                            font-weight:600;
                        ">
                            View Details →
                        </span>

                    </a>

                <?php } ?>

            <?php } ?>

        <?php } else { ?>

            <h2>Track Your Order</h2>

            <p style="color:#666;font-size:14px;line-height:1.6;">
                Enter your Order ID and Contact Number to check your order status.
            </p>

            <form method="POST" action="track.php">

                <label for="order_id" style="
                    display:block;
                    margin:16px 0 7px;
                    font-weight:600;
                    font-size:14px;
                ">
                    Order ID
                </label>

                <input
                    id="order_id"
                    type="text"
                    name="order_id"
                    value="<?= htmlspecialchars($order_input); ?>"
                    placeholder="Example: ORD-1"
                    required
                    style="
                        width:100%;
                        box-sizing:border-box;
                        padding:12px;
                        border:1px solid #D9DEE7;
                        border-radius:8px;
                    "
                >

                <label for="contact_number" style="
                    display:block;
                    margin:16px 0 7px;
                    font-weight:600;
                    font-size:14px;
                ">
                    Contact Number
                </label>

                <input
                    id="contact_number"
                    type="text"
                    name="contact_number"
                    value="<?= htmlspecialchars($contact); ?>"
                    placeholder="Enter your contact number"
                    required
                    style="
                        width:100%;
                        box-sizing:border-box;
                        padding:12px;
                        border:1px solid #D9DEE7;
                        border-radius:8px;
                    "
                >

                <button type="submit" style="
                    width:100%;
                    margin-top:18px;
                    padding:13px;
                    border:0;
                    border-radius:8px;
                    background:#6B8E3B;
                    color:#fff;
                    font-weight:700;
                    cursor:pointer;
                ">
                    Track Order
                </button>

            </form>

        <?php } ?>

    </section>


    <!-- RIGHT: SELECTED ORDER DETAILS -->
    <section class="track-details-panel">

        <?php if ($error !== '') { ?>

            <div class="error-box">
                ⚠️ <?= htmlspecialchars($error); ?>
            </div>

        <?php } ?>

        <?php if ($order) { ?>

            <div style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                flex-wrap:wrap;
                gap:12px;
                padding-bottom:16px;
                margin-bottom:18px;
                border-bottom:1px solid #E5E9F0;
            ">

                <div>
                    <p style="
                        color:#777;
                        font-size:12px;
                        margin:0 0 6px;
                    ">
                        ORDER DETAILS
                    </p>

                    <h2 style="
                        margin:0;
                        color:#333;
                        font-size:22px;
                    ">
                        #ORD-<?= (int)$order['order_id']; ?>
                    </h2>
                </div>

                <span class="status-badge status-<?= strtolower(
                    htmlspecialchars($order['status'])
                ); ?>">
                    <?= htmlspecialchars($order['status']); ?>
                </span>

            </div>


            <!-- CUSTOMER INFORMATION -->
            <h3 style="color:#1B5E20;font-size:16px;">
                Customer Information
            </h3>

            <div style="
                display:grid;
                grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
                gap:14px;
                font-size:14px;
                line-height:1.6;
            ">

                <div>
                    <strong>Customer</strong>
                    <div><?= htmlspecialchars($order['customer_name'] ?? ''); ?></div>
                </div>

                <div>
                    <strong>Contact Number</strong>
                    <div><?= htmlspecialchars($order['contact_number'] ?? ''); ?></div>
                </div>

                <div>
                    <strong>Order Date</strong>
                    <div><?= date(
                        'M d, Y h:i A',
                        strtotime($order['created_at'])
                    ); ?></div>
                </div>

                <div>
                    <strong>Delivery Address</strong>
                    <div><?= htmlspecialchars($order['delivery_address'] ?? ''); ?></div>
                </div>

                <?php if (!empty($order['completed_at'])) { ?>
                    <div>
                        <strong>Completed Date</strong>
                        <div><?= date(
                            'M d, Y h:i A',
                            strtotime($order['completed_at'])
                        ); ?></div>
                    </div>
                <?php } ?>

            </div>

            <?php if (!empty($order['remarks'])) { ?>
                <p style="font-size:14px;line-height:1.6;margin-top:16px;">
                    <strong>Remarks:</strong>
                    <?= htmlspecialchars($order['remarks']); ?>
                </p>
            <?php } ?>


            <!-- ORDERED PRODUCTS -->
            <h3 style="
                margin-top:26px;
                color:#1B5E20;
                font-size:16px;
            ">
                Ordered Products
            </h3>

            <?php if (!empty($orderItems)) { ?>

                <div class="track-table-wrapper">
                    <table class="track-products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty.</th>
                                <th>Unit Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($orderItems as $item) { ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['product_name']); ?>
                                    </td>

                                    <td>
                                        <?= number_format((float)$item['quantity']); ?>
                                        <?= htmlspecialchars($item['unit'] ?? ''); ?>
                                    </td>

                                    <td>
                                        ₱<?= number_format((float)$item['unit_price'], 2); ?>
                                    </td>

                                    <td>
                                        ₱<?= number_format((float)$item['subtotal'], 2); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            <?php } else { ?>

                <p style="color:#777;font-size:14px;">
                    No product details are available for this order.
                </p>

            <?php } ?>


            <!-- ORDER TOTAL -->
            <div style="
                display:flex;
                justify-content:space-between;
                gap:12px;
                margin-top:18px;
                padding:16px;
                background:#F3F6EF;
                border-radius:8px;
                font-weight:700;
            ">
                <span>Total Amount</span>
                <span style="color:#1B5E20;">
                    ₱<?= number_format((float)$order['total_amount'], 2); ?>
                </span>
            </div>


            <!-- ORDER STATUS TIMELINE -->
            <h3 style="
                margin-top:26px;
                color:#1B5E20;
                font-size:16px;
            ">
                Order Progress
            </h3>

            <div class="timeline">

                <?php if ($order['status'] === 'Cancelled') { ?>

                    <div class="step cancelled">
                        Order Cancelled
                    </div>

                <?php } else { ?>

                    <div class="step <?= in_array(
                        $order['status'],
                        ['Pending', 'Processing', 'Completed'],
                        true
                    ) ? 'done' : ''; ?>">
                        1. Order Received
                    </div>

                    <div class="step <?= in_array(
                        $order['status'],
                        ['Processing', 'Completed'],
                        true
                    ) ? 'done' : ''; ?>">
                        2. Processing
                    </div>

                    <div class="step <?= $order['status'] === 'Completed'
                        ? 'done'
                        : ''; ?>">
                        3. Order Completed
                    </div>

                <?php } ?>

            </div>


            <!-- STOCK SNAPSHOTS FOR COMPLETED ORDERS -->
            <?php if (
                $order['status'] === 'Completed'
                && !empty($stockSnapshots)
            ) { ?>

                <h3 style="
                    margin-top:28px;
                    color:#1B5E20;
                    font-size:16px;
                ">
                    Product and Stock Update
                </h3>

                <p style="
                    color:#666;
                    font-size:13px;
                    line-height:1.6;
                ">
                    Inventory records saved when this order was completed.
                </p>

                <div class="track-table-wrapper">
                    <table class="track-products-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Ordered</th>
                                <th>Stock Before</th>
                                <th>Stock After</th>
                                <th>Recorded</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($stockSnapshots as $snapshot) { ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($snapshot['product_name']); ?>
                                    </td>

                                    <td>
                                        <?= number_format((float)$snapshot['quantity_ordered']); ?>
                                        <?= htmlspecialchars($snapshot['unit'] ?? 'pcs'); ?>
                                    </td>

                                    <td>
                                        <?= number_format((float)$snapshot['stock_before']); ?>
                                    </td>

                                    <td>
                                        <?= number_format((float)$snapshot['stock_after']); ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'M d, Y h:i A',
                                            strtotime($snapshot['recorded_at'])
                                        ); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

            <?php } ?>

        <?php } else { ?>

            <div style="
                min-height:260px;
                display:flex;
                flex-direction:column;
                align-items:center;
                justify-content:center;
                text-align:center;
                color:#777;
                padding:20px;
            ">

                <h2 style="color:#1B5E20;">
                    Order Details
                </h2>

                <p style="line-height:1.7;max-width:360px;">
                    <?php if ($loggedInCustomer !== null) { ?>
                        Select an order from the list on the left to view its products, status, and details.
                    <?php } else { ?>
                        Enter your Order ID and Contact Number on the left to view your order.
                    <?php } ?>
                </p>

            </div>

        <?php } ?>

    </section>

</main>

</body>

</html>
