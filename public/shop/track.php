<?php

require_once '../../config/database.php';

$order = null;
$error = "";
$success_message = "";

// Get Order ID from URL after a successful order submission
$prefill_order_id = trim($_GET['id'] ?? '');

if ($prefill_order_id !== '') {
    $prefill_order_id = preg_replace(
        '/[^0-9]/',
        '',
        $prefill_order_id
    );

    if ($prefill_order_id !== '') {
        $prefill_order_id = 'ORD-' . $prefill_order_id;
    }
}

// Keep entered values
$order_input = trim($_POST['order_id'] ?? $prefill_order_id);
$contact = trim($_POST['contact_number'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $orderInput = strtoupper($order_input);

    // Accept ORD-15 or 15
    $order_id = preg_replace(
        '/[^0-9]/',
        '',
        $orderInput
    );

    if (empty($order_id) || empty($contact)) {

        $error = "Please enter both your Order ID and Contact Number.";

    } else {

        /*
         * Find the order using:
         * 1. Order ID
         * 2. Exact customer contact number
         *
         * This prevents someone from viewing an order
         * using the Order ID alone.
         */
        $stmt = $pdo->prepare("
            SELECT
                o.order_id,
                o.customer_id,
                o.delivery_address,
                o.remarks,
                o.status,
                o.total_amount,
                o.created_at,
                c.customer_name,
                c.contact_number
            FROM orders o
            INNER JOIN customers c
                ON o.customer_id = c.customer_id
            WHERE o.order_id = ?
              AND c.contact_number = ?
            LIMIT 1
        ");

        $stmt->execute([
            $order_id,
            $contact
        ]);

        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {

            $error =
                "Order not found. Please double-check your Order ID and Contact Number.";

        } else {

            $success_message =
                "Order found successfully.";
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

<title>Track Order - Leah's Popstick</title>

<link
    rel="stylesheet"
    href="../css/shop.css"
>

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

<div
    class="track-container"
    style="
        max-width:800px;
        margin:40px auto;
        padding:0 20px;
    "
>


<!-- BACK BUTTON -->

<a
    href="index.php"
    class="back-link"
    style="
        display:inline-block;
        margin-bottom:20px;
        color:#1B5E20;
        text-decoration:none;
        font-size:14px;
        font-weight:600;
    "
>
    ← Back to Products
</a>


<!-- PAGE TITLE -->

<h1
    style="
        color:#1B5E20;
        font-size:28px;
        margin-bottom:8px;
        font-weight:700;
    "
>
    Track Your Order
</h1>


<p
    style="
        color:#666;
        font-size:14px;
        margin-bottom:25px;
    "
>
    Enter your Order ID and Contact Number to check your order status.
</p>


<!-- SUCCESS MESSAGE -->

<?php if (!empty($success_message) && !$order) { ?>

    <div
        style="
            background:#E8F5E9;
            color:#2E7D32;
            border-left:4px solid #43A047;
            padding:14px;
            border-radius:8px;
            margin-bottom:20px;
            font-size:14px;
        "
    >

        <strong>
            Order submitted successfully!
        </strong>

        <br>

        Your Order ID is
        <strong>
            <?= htmlspecialchars($order_input); ?>
        </strong>.

        Enter your contact number below to view the order status.

    </div>

<?php } ?>


<!-- TRACKING FORM -->

<div
    style="
        background:#FFFFFF;
        padding:25px;
        border-radius:12px;
        border:1px solid #E5E9F0;
        box-shadow:0 4px 15px rgba(0,0,0,0.05);
        margin-bottom:25px;
    "
>

    <form method="POST">

        <!-- ORDER ID -->

        <div style="margin-bottom:15px;">

            <label
                style="
                    display:block;
                    margin-bottom:7px;
                    font-size:14px;
                    font-weight:600;
                    color:#333;
                "
            >
                Order ID
            </label>

            <input
                type="text"
                name="order_id"
                value="<?= htmlspecialchars($order_input); ?>"
                placeholder="Example: ORD-1"
                required
                style="
                    width:100%;
                    box-sizing:border-box;
                    padding:13px;
                    border:1px solid #D9DEE7;
                    border-radius:8px;
                    font-size:14px;
                "
            >

        </div>


        <!-- CONTACT NUMBER -->

        <div style="margin-bottom:15px;">

            <label
                style="
                    display:block;
                    margin-bottom:7px;
                    font-size:14px;
                    font-weight:600;
                    color:#333;
                "
            >
                Contact Number
            </label>

            <input
                type="text"
                name="contact_number"
                value="<?= htmlspecialchars($contact); ?>"
                placeholder="Enter your contact number"
                required
                style="
                    width:100%;
                    box-sizing:border-box;
                    padding:13px;
                    border:1px solid #D9DEE7;
                    border-radius:8px;
                    font-size:14px;
                "
            >

        </div>


        <button
            type="submit"
            style="
                width:100%;
                border:none;
                padding:14px;
                background:#6B8E3B;
                color:white;
                border-radius:8px;
                font-weight:700;
                font-size:15px;
                cursor:pointer;
            "
        >
            Track Order
        </button>

    </form>

</div>


<!-- ERROR -->

<?php if ($error) { ?>

    <div class="error-box">

        ⚠️
        <?= htmlspecialchars($error); ?>

    </div>

<?php } ?>


<!-- ORDER RESULT -->

<?php if ($order) { ?>

    <div class="order-card">

        <!-- ORDER HEADER -->

        <div
            style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                border-bottom:1px solid #E5E9F0;
                padding-bottom:12px;
                margin-bottom:15px;
                gap:15px;
            "
        >

            <h2
                style="
                    margin:0;
                    font-size:18px;
                    font-family:monospace;
                    color:#333;
                "
            >
                Order #ORD-<?= htmlspecialchars(
                    $order['order_id']
                ); ?>
            </h2>


            <div
                class="status-badge status-<?= strtolower(
                    htmlspecialchars($order['status'])
                ); ?>"
                style="margin:0;"
            >
                <?= htmlspecialchars($order['status']); ?>
            </div>

        </div>


        <!-- ORDER INFORMATION -->

        <div
            style="
                font-size:14px;
                color:#444;
                line-height:1.6;
                margin-bottom:20px;
            "
        >

            <p>

                <strong>
                    Customer:
                </strong>

                <?= htmlspecialchars(
                    $order['customer_name']
                ); ?>

            </p>


            <p>

                <strong>
                    Contact Number:
                </strong>

                <?= htmlspecialchars(
                    $order['contact_number']
                ); ?>

            </p>


            <p>

                <strong>
                    Order Date:
                </strong>

                <?= date(
                    "M d, Y h:i A",
                    strtotime($order['created_at'])
                ); ?>

            </p>


            <p>

                <strong>
                    Delivery Address:
                </strong>

                <?= htmlspecialchars(
                    $order['delivery_address']
                ); ?>

            </p>


            <?php if (!empty($order['remarks'])) { ?>

                <p>

                    <strong>
                        Remarks:
                    </strong>

                    <?= htmlspecialchars(
                        $order['remarks']
                    ); ?>

                </p>

            <?php } ?>


            <p
                style="
                    font-size:16px;
                    margin-top:10px;
                    color:#1B5E20;
                "
            >

                <strong>

                    Total Amount:
                    ₱<?= number_format(
                        $order['total_amount'],
                        2
                    ); ?>

                </strong>

            </p>

        </div>


        <!-- ORDER STATUS TIMELINE -->

        <div class="timeline">

            <?php if ($order['status'] === 'Cancelled') { ?>

                <div class="step cancelled">

                    <i class="fa-solid fa-circle-xmark"></i>

                    Order Cancelled

                </div>

            <?php } else { ?>

                <!-- PENDING -->

                <div
                    class="step
                    <?= in_array(
                        $order['status'],
                        [
                            'Pending',
                            'Processing',
                            'Completed'
                        ]
                    ) ? 'done' : ''; ?>"
                >

                    <i
                        class="fa-solid
                        <?= in_array(
                            $order['status'],
                            [
                                'Pending',
                                'Processing',
                                'Completed'
                            ]
                        )
                        ? 'fa-circle-check'
                        : 'fa-circle'; ?>"
                    ></i>

                    Order Received

                </div>


                <!-- PROCESSING -->

                <div
                    class="step
                    <?= in_array(
                        $order['status'],
                        [
                            'Processing',
                            'Completed'
                        ]
                    ) ? 'done' : ''; ?>"
                >

                    <i
                        class="fa-solid
                        <?= in_array(
                            $order['status'],
                            [
                                'Processing',
                                'Completed'
                            ]
                        )
                        ? 'fa-circle-check'
                        : 'fa-circle'; ?>"
                    ></i>

                    Processing

                </div>


                <!-- COMPLETED -->

                <div
                    class="step
                    <?= $order['status'] === 'Completed'
                    ? 'done'
                    : ''; ?>"
                >

                    <i
                        class="fa-solid
                        <?= $order['status'] === 'Completed'
                        ? 'fa-circle-check'
                        : 'fa-circle'; ?>"
                    ></i>

                    Order Completed

                </div>

            <?php } ?>

        </div>

    </div>

<?php } ?>


</div>

</body>

</html>
