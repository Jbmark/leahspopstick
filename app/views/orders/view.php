<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <div style="margin-bottom:20px;">

            <a
                href="orders.php"
                style="
                    text-decoration:none;
                    color:#1B5E20;
                    font-weight:600;
                "
            >
                &larr; Back to Orders
            </a>

            <h1 style="margin-top:15px;">
                Order #ORD-<?= $order['order_id']; ?>
            </h1>

            <p>
                Review order details, products, customer information,
                and order processing status.
            </p>

        </div>


        <!-- Success Message -->

        <?php if (isset($_SESSION['success'])) { ?>

            <div
                class="role-tag"
                style="
                    background:#E8F5E9;
                    color:#2E7D32;
                    padding:14px;
                    display:block;
                    border-radius:8px;
                    margin-bottom:20px;
                    font-weight:600;
                    text-align:left;
                    font-size:14px;
                    border-left:5px solid #2E7D32;
                    text-transform:none;
                    max-width:100%;
                "
            >

                ✓ <?= htmlspecialchars($_SESSION['success']); ?>

                <?php unset($_SESSION['success']); ?>

            </div>

        <?php } ?>


        <!-- Error Message -->

        <?php if (isset($_SESSION['order_error'])) { ?>

            <div
                class="login-error"
                style="
                    max-width:100%;
                    margin-bottom:20px;
                "
            >

                <?= htmlspecialchars($_SESSION['order_error']); ?>

                <?php unset($_SESSION['order_error']); ?>

            </div>

        <?php } ?>


        <div
            style="
                display:grid;
                grid-template-columns:2fr 1fr;
                gap:24px;
                align-items:start;
            "
        >


            <!-- LEFT COLUMN: ORDER ITEMS -->

            <div
                class="card"
                style="
                    padding:0;
                    overflow:hidden;
                    border-radius:12px;
                "
            >

                <table style="width:100%; margin-top:0;">

                    <tr>

                        <th>Product</th>

                        <th>Unit Price</th>

                        <th>Quantity</th>

                        <th>Subtotal</th>

                    </tr>


                    <?php if (empty($items)) { ?>

                        <tr>

                            <td
                                colspan="4"
                                style="
                                    text-align:center;
                                    padding:30px;
                                    color:#7A869A;
                                "
                            >
                                No order items found.
                            </td>

                        </tr>

                    <?php } else { ?>

                        <?php foreach ($items as $i) { ?>

                            <tr>

                                <td>

                                    <b>
                                        <?= htmlspecialchars(
                                            $i['product_name']
                                        ); ?>
                                    </b>

                                </td>


                                <td>

                                    ₱<?= number_format(
                                        $i['unit_price'],
                                        2
                                    ); ?>

                                    /
                                    <?= htmlspecialchars(
                                        $i['unit']
                                    ); ?>

                                </td>


                                <td>

                                    <span
                                        class="role-tag"
                                        style="
                                            background:#ECEFF1;
                                            color:#37474F;
                                        "
                                    >
                                        <?= number_format(
                                            $i['quantity'],
                                            0
                                        ); ?>

                                        <?= htmlspecialchars(
                                            $i['unit']
                                        ); ?>
                                    </span>

                                </td>


                                <td>

                                    <b>
                                        ₱<?= number_format(
                                            $i['subtotal'],
                                            2
                                        ); ?>
                                    </b>

                                </td>

                            </tr>

                        <?php } ?>


                        <tr
                            style="
                                background:#F8F9FA;
                                font-weight:700;
                            "
                        >

                            <td
                                colspan="3"
                                style="
                                    text-align:right;
                                    padding:16px;
                                    color:#5A6A85;
                                "
                            >
                                Total Amount:
                            </td>

                            <td
                                style="
                                    color:#1B5E20;
                                    padding:16px;
                                    font-size:18px;
                                "
                            >
                                ₱<?= number_format(
                                    $order['total_amount'],
                                    2
                                ); ?>
                            </td>

                        </tr>

                    <?php } ?>

                </table>

            </div>


            <!-- RIGHT COLUMN -->

            <div
                class="card"
                style="
                    border-radius:12px;
                    padding:24px;
                "
            >

                <h3
                    style="
                        margin-bottom:15px;
                        color:#1B5E20;
                        font-size:16px;
                    "
                >
                    Order Information
                </h3>


                <div
                    style="
                        font-size:14px;
                        color:#333;
                        line-height:1.6;
                        margin-bottom:20px;
                    "
                >

                    <p style="margin-bottom:8px;">

                        <strong>Customer:</strong>

                        <?= htmlspecialchars(
                            $order['customer_name']
                        ); ?>

                    </p>


                    <?php if (!empty($order['contact_number'])) { ?>

                        <p style="margin-bottom:8px;">

                            <strong>Contact:</strong>

                            <?= htmlspecialchars(
                                $order['contact_number']
                            ); ?>

                        </p>

                    <?php } ?>


                    <?php if (!empty($order['email'])) { ?>

                        <p style="margin-bottom:8px;">

                            <strong>Email:</strong>

                            <?= htmlspecialchars(
                                $order['email']
                            ); ?>

                        </p>

                    <?php } ?>


                    <p style="margin-bottom:8px;">

                        <strong>Delivery Address:</strong>

                        <?= htmlspecialchars(
                            $order['delivery_address']
                            ?: 'No delivery address recorded.'
                        ); ?>

                    </p>


                    <p style="margin-bottom:8px;">

                        <strong>Remarks:</strong>

                        <i>
                            <?= htmlspecialchars(
                                $order['remarks']
                                ?: 'None.'
                            ); ?>
                        </i>

                    </p>


                    <p style="margin-bottom:8px;">

                        <strong>Order Source:</strong>

                        <span
                            class="role-tag"
                            style="
                                background:#ECEFF1;
                                color:#37474F;
                                text-transform:none;
                            "
                        >
                            <?= htmlspecialchars(
                                $order['order_source']
                            ); ?>
                        </span>

                    </p>


                    <p style="margin-bottom:8px;">

                        <strong>Encoded By:</strong>

                        <?= !empty($order['encoder_name'])
                            ? htmlspecialchars($order['encoder_name'])
                            : 'Website'; ?>

                    </p>


                    <p style="margin-bottom:8px;">

                        <strong>Order Date:</strong>

                        <?= date(
                            'M d, Y h:i A',
                            strtotime($order['created_at'])
                        ); ?>

                    </p>


                    <?php if (!empty($order['completed_at'])) { ?>

                        <p
                            style="
                                margin-bottom:8px;
                                color:#2E7D32;
                            "
                        >

                            <strong>Completed:</strong>

                            <?= date(
                                'M d, Y h:i A',
                                strtotime($order['completed_at'])
                            ); ?>

                        </p>

                    <?php } ?>

                </div>


                <!-- CURRENT STATUS -->

                <div style="margin-bottom:20px;">

                    <label
                        style="
                            display:block;
                            font-size:11px;
                            font-weight:700;
                            color:#7A869A;
                            text-transform:uppercase;
                            margin-bottom:6px;
                        "
                    >
                        Current Status
                    </label>


                    <?php

                    $statusBg = '#ECEFF1';
                    $statusColor = '#455A64';

                    switch ($order['status']) {

                        case 'Pending':
                            $statusBg = '#FFF3E0';
                            $statusColor = '#E65100';
                            break;

                        case 'Processing':
                            $statusBg = '#E3F2FD';
                            $statusColor = '#0288D1';
                            break;

                        case 'Completed':
                            $statusBg = '#E8F5E9';
                            $statusColor = '#2E7D32';
                            break;

                        case 'Cancelled':
                            $statusBg = '#FFEBEE';
                            $statusColor = '#C62828';
                            break;
                    }

                    ?>


                    <span
                        class="role-tag"
                        style="
                            display:block;
                            text-align:center;
                            padding:10px;
                            font-size:14px;
                            font-weight:700;
                            background:<?= $statusBg; ?>;
                            color:<?= $statusColor; ?>;
                            text-transform:none;
                        "
                    >
                        <?= htmlspecialchars(
                            $order['status']
                        ); ?>
                    </span>

                </div>


                <hr
                    style="
                        border:0;
                        border-top:1px solid #E5E9F0;
                        margin:20px 0;
                    "
                >


                <!-- WORKFLOW CONTROLS -->

                <?php if ($_SESSION['user']['role_name'] == 'Bookkeeper') { ?>

                    <p
                        style="
                            text-align:center;
                            color:#7A869A;
                            font-size:13px;
                            font-style:italic;
                        "
                    >
                        Bookkeeper access is view-only.
                    </p>


                <?php } else { ?>


                    <!-- PENDING -->

                    <?php if ($order['status'] == 'Pending') { ?>

                        <a
                            href="orders.php?action=processing&id=<?= $order['order_id']; ?>"
                            class="btn"
                            style="
                                display:block;
                                text-align:center;
                                margin-bottom:10px;
                                background:#0288D1;
                                font-size:13px;
                            "
                        >
                            Move to Processing
                        </a>


                        <a
                            href="orders.php?action=cancel&id=<?= $order['order_id']; ?>"
                            class="btn"
                            style="
                                display:block;
                                text-align:center;
                                background:#D32F2F;
                                font-size:13px;
                            "
                            onclick="return confirm('Cancel this order?');"
                        >
                            Cancel Order
                        </a>


                    <!-- PROCESSING -->

                    <?php } elseif ($order['status'] == 'Processing') { ?>


                        <a
                            href="orders.php?action=complete&id=<?= $order['order_id']; ?>"
                            class="btn"
                            style="
                                display:block;
                                text-align:center;
                                margin-bottom:10px;
                                font-size:13px;
                            "
                            onclick="return confirm('Complete this order? FIFO will deduct the required quantity from the oldest available inventory batches.');"
                        >
                            Complete Order
                        </a>


                        <a
                            href="orders.php?action=cancel&id=<?= $order['order_id']; ?>"
                            class="btn"
                            style="
                                display:block;
                                text-align:center;
                                background:#D32F2F;
                                font-size:13px;
                            "
                            onclick="return confirm('Cancel this order?');"
                        >
                            Cancel Order
                        </a>


                    <!-- COMPLETED -->

                    <?php } elseif ($order['status'] == 'Completed') { ?>


                        <p
                            style="
                                text-align:center;
                                color:#2E7D32;
                                font-size:13px;
                                font-weight:600;
                                margin-bottom:15px;
                            "
                        >
                            ✓ Order completed and inventory was deducted using FIFO.
                        </p>


                        <a
                            href="reports.php?action=invoice&id=<?= $order['order_id']; ?>"
                            class="btn"
                            target="_blank"
                            style="
                                display:block;
                                text-align:center;
                                background:#455A64;
                                margin-top:10px;
                                font-size:13px;
                                color:white;
                            "
                        >
                            Print Invoice
                        </a>


                        <a
                            href="orders.php?action=cancel&id=<?= $order['order_id']; ?>"
                            class="btn"
                            style="
                                display:block;
                                text-align:center;
                                margin-top:10px;
                                background:#D32F2F;
                                font-size:13px;
                            "
                            onclick="return confirm('Cancel this completed order? The FIFO inventory deducted for this order will be restored.');"
                        >
                            Cancel & Restore Inventory
                        </a>


                    <!-- CANCELLED -->

                    <?php } elseif ($order['status'] == 'Cancelled') { ?>


                        <p
                            style="
                                text-align:center;
                                color:#D32F2F;
                                font-size:13px;
                                font-weight:600;
                            "
                        >
                            This order has been cancelled.
                            No further workflow actions are available.
                        </p>


                    <?php } ?>

                <?php } ?>

            </div>

        </div>

    </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>