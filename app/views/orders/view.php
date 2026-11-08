
<?php include 'app/views/layouts/header.php'; ?>

<?php
$deliveryAddress = trim($order['delivery_address'] ?? '');
$contactNumber = trim($order['contact_number'] ?? '');
$mapsUrl = $deliveryAddress !== ''
    ? 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($deliveryAddress)
    : '';
?>

<style>
    .order-view-stack {
        display: flex;
        flex-direction: column;
        gap: 22px;
        min-width: 0;
    }

    .order-view-card {
        background: #fff;
        border: 1px solid #E1E7EF;
        border-radius: 14px;
        overflow: hidden;
        min-width: 0;
        box-shadow: 0 3px 12px rgba(25, 45, 75, 0.04);
    }

    .order-view-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #E5E9F0;
        background: #FAFCFE;
    }

    .order-view-card-header h2 {
        margin: 0;
        color: #1B5E20;
        font-size: 18px;
    }

    .order-view-card-header p {
        margin: 6px 0 0;
        color: #718096;
        font-size: 13px;
    }

    .order-view-card-body {
        padding: 24px;
    }

    .order-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .order-table-wrap table {
        width: 100%;
        min-width: 570px;
        margin-top: 0;
    }

    .logistics-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .logistics-field {
        min-width: 0;
        padding: 20px;
        background: #F8FAFC;
        border: 1px solid #DCE5EE;
        border-radius: 12px;
    }

    .logistics-label {
        display: block;
        margin-bottom: 10px;
        color: #65758B;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .logistics-value {
        display: block;
        color: #1F2937;
        font-size: 17px;
        font-weight: 600;
        line-height: 1.65;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    .logistics-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-top: 16px;
    }

    .logistics-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 40px;
        padding: 9px 14px;
        border: 1px solid #C9D5E3;
        border-radius: 8px;
        background: #fff;
        color: #24543A;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
    }

    .logistics-btn:hover {
        background: #EDF7EF;
    }

    .logistics-btn-primary {
        background: #1B5E20;
        color: #fff;
        border-color: #1B5E20;
    }

    .logistics-btn-primary:hover {
        background: #144A19;
        color: #fff;
    }

    .logistics-empty {
        color: #7A869A;
        font-size: 14px;
        font-style: italic;
    }

    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px 24px;
    }

    .order-info-item {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .order-info-item strong {
        display: block;
        margin-bottom: 5px;
        color: #718096;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    .order-info-item span,
    .order-info-item p {
        color: #283444;
        font-size: 14px;
        line-height: 1.65;
    }

    .order-workflow {
        padding: 22px 24px;
        border-top: 1px solid #E5E9F0;
        background: #FCFDFE;
    }

    .order-workflow h3 {
        margin: 0 0 14px;
        color: #1B5E20;
        font-size: 15px;
    }

    .order-workflow .btn {
        box-sizing: border-box;
    }

    @media (max-width: 700px) {
        .logistics-grid,
        .order-info-grid {
            grid-template-columns: minmax(0, 1fr);
        }

        .order-view-card-header {
            padding: 17px;
        }

        .order-view-card-body {
            padding: 17px;
        }

        .order-workflow {
            padding: 17px;
        }

        .logistics-value {
            font-size: 16px;
        }
    }
</style>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <div style="margin-bottom:20px;">

            <a
                href="orders.php"
                style="text-decoration:none;color:#1B5E20;font-weight:600;"
            >
                &larr; Back to Orders
            </a>

            <h1 style="margin-top:15px;">
                Order #ORD-<?= (int)$order['order_id']; ?>
            </h1>

            <p>
                Review the order, delivery details, customer information,
                and order processing status.
            </p>

        </div>

        <!-- SUCCESS MESSAGE -->
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
            </div>

            <?php unset($_SESSION['success']); ?>

        <?php } ?>

        <!-- ERROR MESSAGE -->
        <?php if (isset($_SESSION['order_error'])) { ?>

            <div
                class="login-error"
                style="max-width:100%;margin-bottom:20px;"
            >
                <?= htmlspecialchars($_SESSION['order_error']); ?>
            </div>

            <?php unset($_SESSION['order_error']); ?>

        <?php } ?>


        <div class="order-view-stack">

            <!-- 1. PRODUCTION PACKING LIST / ORDER ITEMS -->
            <section class="order-view-card">

                <div class="order-view-card-header">
                    <h2>Production Packing List</h2>
                    <p>Review the products and quantities included in this order.</p>
                </div>

                <div class="order-table-wrap">

                    <table>

                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php if (empty($items)) { ?>

                            <tr>
                                <td
                                    colspan="4"
                                    style="text-align:center;padding:30px;color:#7A869A;"
                                >
                                    No order items found.
                                </td>
                            </tr>

                        <?php } else { ?>

                            <?php foreach ($items as $i) { ?>

                                <tr>

                                    <td>
                                        <b>
                                            <?= htmlspecialchars($i['product_name']); ?>
                                        </b>
                                    </td>

                                    <td>
                                        ₱<?= number_format((float)$i['unit_price'], 2); ?>
                                        / <?= htmlspecialchars($i['unit']); ?>
                                    </td>

                                    <td>
                                        <span
                                            class="role-tag"
                                            style="background:#ECEFF1;color:#37474F;"
                                        >
                                            <?= number_format((float)$i['quantity'], 0); ?>
                                            <?= htmlspecialchars($i['unit']); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <b>
                                            ₱<?= number_format((float)$i['subtotal'], 2); ?>
                                        </b>
                                    </td>

                                </tr>

                            <?php } ?>

                            <tr style="background:#F8F9FA;font-weight:700;">

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
                                    ₱<?= number_format((float)$order['total_amount'], 2); ?>
                                </td>

                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- 2. DELIVERY LOGISTICS: DIRECTLY BELOW THE PACKING LIST -->
            <section class="order-view-card">

                <div class="order-view-card-header">
                    <h2>Delivery Logistics</h2>
                    <p>Contact and destination information for order delivery.</p>
                </div>

                <div class="order-view-card-body">

                    <div class="logistics-grid">

                        <!-- CONTACT NUMBER -->
                        <div class="logistics-field">

                            <span class="logistics-label">
                                Customer Contact Number
                            </span>

                            <?php if ($contactNumber !== '') { ?>

                                <span
                                    class="logistics-value"
                                    id="delivery-contact-number"
                                ><?= htmlspecialchars($contactNumber); ?></span>

                                <div class="logistics-actions">

                                    <button
                                        type="button"
                                        class="logistics-btn"
                                        data-copy-target="delivery-contact-number"
                                    >
                                        Copy Phone Number
                                    </button>

                                </div>

                            <?php } else { ?>

                                <span class="logistics-empty">
                                    No contact number recorded for this customer.
                                </span>

                            <?php } ?>

                        </div>


                        <!-- DELIVERY ADDRESS -->
                        <div class="logistics-field">

                            <span class="logistics-label">
                                Delivery Destination
                            </span>

                            <?php if ($deliveryAddress !== '') { ?>

                                <span
                                    class="logistics-value"
                                    id="delivery-address"
                                ><?= htmlspecialchars($deliveryAddress); ?></span>

                                <div class="logistics-actions">

                                    <button
                                        type="button"
                                        class="logistics-btn"
                                        data-copy-target="delivery-address"
                                    >
                                        Copy Address
                                    </button>

                                    <a
                                        href="<?= htmlspecialchars($mapsUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                        class="logistics-btn logistics-btn-primary"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        Open in Google Maps
                                    </a>

                                </div>

                            <?php } else { ?>

                                <span class="logistics-empty">
                                    No delivery address recorded for this order.
                                </span>

                            <?php } ?>

                        </div>

                    </div>

                    <p
                        id="logistics-copy-message"
                        role="status"
                        aria-live="polite"
                        style="margin:14px 0 0;font-size:13px;color:#2E7D32;"
                    ></p>

                </div>

            </section>


            <!-- 3. ORDER INFORMATION -->
            <section class="order-view-card">

                <div class="order-view-card-header">
                    <h2>Order Information</h2>
                    <p>Customer details, order source, and order timestamps.</p>
                </div>

                <div class="order-view-card-body">

                    <div class="order-info-grid">

                        <div class="order-info-item">
                            <strong>Customer</strong>
                            <span>
                                <?= htmlspecialchars($order['customer_name'] ?? 'Not provided'); ?>
                            </span>
                        </div>

                        <div class="order-info-item">
                            <strong>Email</strong>
                            <span>
                                <?= htmlspecialchars($order['email'] ?? 'Not provided'); ?>
                            </span>
                        </div>

                        <div class="order-info-item">
                            <strong>Order Source</strong>
                            <span
                                class="role-tag"
                                style="
                                    display:inline-block;
                                    background:#ECEFF1;
                                    color:#37474F;
                                    text-transform:none;
                                "
                            >
                                <?= htmlspecialchars($order['order_source'] ?? 'Not specified'); ?>
                            </span>
                        </div>

                        <div class="order-info-item">
                            <strong>Encoded By</strong>
                            <span>
                                <?= !empty($order['encoder_name'])
                                    ? htmlspecialchars($order['encoder_name'])
                                    : 'Website'; ?>
                            </span>
                        </div>

                        <div class="order-info-item">
                            <strong>Order Date</strong>
                            <span>
                                <?= !empty($order['created_at'])
                                    ? date('M d, Y h:i A', strtotime($order['created_at']))
                                    : 'Not available'; ?>
                            </span>
                        </div>

                        <?php if (!empty($order['completed_at'])) { ?>

                            <div class="order-info-item">
                                <strong>Completed Date</strong>
                                <span style="color:#2E7D32;">
                                    <?= date('M d, Y h:i A', strtotime($order['completed_at'])); ?>
                                </span>
                            </div>

                        <?php } ?>

                        <div class="order-info-item" style="grid-column:1 / -1;">
                            <strong>Remarks</strong>
                            <p style="margin:0;">
                                <?= htmlspecialchars($order['remarks'] ?: 'None.'); ?>
                            </p>
                        </div>

                    </div>

                </div>


                <!-- CURRENT STATUS AND WORKFLOW -->
                <div class="order-workflow">

                    <h3>Order Status &amp; Workflow</h3>

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

                    <div style="margin-bottom:20px;">

                        <label
                            style="
                                display:block;
                                font-size:11px;
                                font-weight:700;
                                color:#7A869A;
                                text-transform:uppercase;
                                margin-bottom:8px;
                            "
                        >
                            Current Status
                        </label>

                        <span
                            class="role-tag"
                            style="
                                display:inline-block;
                                padding:10px 18px;
                                font-size:14px;
                                font-weight:700;
                                background:<?= $statusBg; ?>;
                                color:<?= $statusColor; ?>;
                                text-transform:none;
                            "
                        >
                            <?= htmlspecialchars($order['status']); ?>
                        </span>

                    </div>

                    <hr
                        style="
                            border:0;
                            border-top:1px solid #E5E9F0;
                            margin:20px 0;
                        "
                    >

                    <?php if (($_SESSION['user']['role_name'] ?? '') == 'Bookkeeper') { ?>

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
                                href="orders.php?action=processing&id=<?= (int)$order['order_id']; ?>"
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
                                href="orders.php?action=cancel&id=<?= (int)$order['order_id']; ?>"
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
                                href="orders.php?action=complete&id=<?= (int)$order['order_id']; ?>"
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
                                href="orders.php?action=cancel&id=<?= (int)$order['order_id']; ?>"
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
                                href="reports.php?action=invoice&id=<?= (int)$order['order_id']; ?>"
                                class="btn"
                                target="_blank"
                                rel="noopener noreferrer"
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
                                href="orders.php?action=cancel&id=<?= (int)$order['order_id']; ?>"
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
                                Cancel &amp; Restore Inventory
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

            </section>

        </div>

    </div>

</div>

<script>
document.addEventListener('click', async function (event) {
    const button = event.target.closest('[data-copy-target]');

    if (!button) {
        return;
    }

    const targetId = button.getAttribute('data-copy-target');
    const target = document.getElementById(targetId);
    const message = document.getElementById('logistics-copy-message');

    if (!target) {
        return;
    }

    const textToCopy = target.textContent.trim();
    const originalLabel = button.textContent.trim();

    try {
        if (!navigator.clipboard || !navigator.clipboard.writeText) {
            throw new Error('Clipboard access is unavailable.');
        }

        await navigator.clipboard.writeText(textToCopy);

        button.textContent = 'Copied!';
        if (message) {
            message.textContent = originalLabel.includes('Phone')
                ? 'Phone number copied.'
                : 'Delivery address copied.';
        }

        window.setTimeout(function () {
            button.textContent = originalLabel;
        }, 1800);

    } catch (error) {
        if (message) {
            message.textContent =
                'Copy was unavailable. Please select the text and copy it manually.';
        }
    }
});
</script>

<?php include 'app/views/layouts/footer.php'; ?>
