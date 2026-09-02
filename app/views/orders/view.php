<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <div style="margin-bottom: 20px;">
            <a href="orders.php" style="text-decoration:none; color:#1B5E20; font-weight:600;">&larr; Back to Order Ledger Panel</a>
            <h1 style="margin-top:15px;">Fulfillment Board #ORD-<?= $order['order_id']; ?></h1>
            <p>Review acquisition details, calculate item distributions, and process programmatic state steps.</p>
        </div>

        <!-- 🌟 8.4 FIXED: Banner UI Status Notifications Success Message Wrapper -->
        <?php if (isset($_SESSION['success'])) { ?>
            <div class="role-tag" style="background:#E8F5E9; color:#2E7D32; padding:14px; display:block; border-radius:8px; margin-bottom:20px; font-weight:600; text-align:left; font-size:14px; border-left: 5px solid #2E7D32; text-transform: none; max-width: 100%;">
                🎉 <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            </div>
        <?php } ?>

        <?php if (isset($_SESSION['order_error'])) { ?>
            <div class="login-error" style="max-width:100%; margin-bottom:20px;">
                <?= htmlspecialchars($_SESSION['order_error']); unset($_SESSION['order_error']); ?>
            </div>
        <?php } ?>

        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px; align-items: start;">
            
            <!-- LEFT COLUMN: Itemized Line Breakdown Matrix -->
            <div class="card" style="padding:0; overflow:hidden; border-radius:12px;">
                <table style="width:100%; margin-top:0;">
                    <tr>
                        <th>Product Specification</th>
                        <th>Unit Cost Vector</th>
                        <th>Quantity Ordered</th>
                        <th>Subtotal Amount</th>
                    </tr>
                    <?php foreach ($items as $i) { ?>
                    <tr>
                        <td><b><?= htmlspecialchars($i['product_name']); ?></b></td>
                        <td>₱<?= number_format($i['unit_price'], 2); ?></td>
                        <td><span class="role-tag" style="background:#ECEFF1; color:#37474F;"><?= $i['quantity']; ?> <?= htmlspecialchars($i['unit']); ?></span></td>
                        <td><b>₱<?= number_format($i['subtotal'], 2); ?></b></td>
                    </tr>
                    <?php } ?>
                    <tr style="background:#F8F9FA; font-weight:700;">
                        <td colspan="3" style="text-align:right; padding:16px; color:#5A6A85;">Combined Document Gross Sum:</td>
                        <td style="color:#1B5E20; padding:16px; font-size:18px;">₱<?= number_format($order['total_amount'], 2); ?></td>
                    </tr>
                </table>
            </div>

            <!-- RIGHT COLUMN: Metadata and Interactive Workflow State Controls -->
            <div class="card" style="border-radius:12px; padding:24px;">
                <h3 style="margin-bottom:15px; color:#1B5E20; font-size:16px; text-transform:uppercase; letter-spacing:0.5px;">Operational Metrics</h3>
                
                <div style="font-size:14px; color:#333; line-height:1.6; margin-bottom:20px;">
                    <p style="margin-bottom:8px;"><strong>Client Profile:</strong> <?= htmlspecialchars($order['customer_name']); ?></p>
                    <p style="margin-bottom:8px;"><strong>Shipping Target:</strong> <?= htmlspecialchars($order['delivery_address'] ?: 'No physical destination recorded.'); ?></p>
                    <p style="margin-bottom:8px;"><strong>Internal Context:</strong> <i><?= htmlspecialchars($order['remarks'] ?: 'None.'); ?></i></p>
                    <p style="margin-bottom:8px;"><strong>Logged Channel:</strong> <span class="role-tag" style="background:#ECEFF1; color:#37474F; text-transform:none;"><?= $order['order_source']; ?></span></p>
                    <p style="margin-bottom:8px;"><strong>Account Encoder:</strong> <?= htmlspecialchars($order['encoder_name'] ?? 'External Automated API Storefront'); ?></p>
                    <p style="margin-bottom:8px;"><strong>Creation Date:</strong> <?= date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                    <?php if ($order['completed_at']) { ?>
                        <p style="margin-bottom:8px; color:#2E7D32;"><strong>Completion Stamp:</strong> <?= date('M d, Y h:i A', strtotime($order['completed_at'])); ?></p>
                    <?php } ?>
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:11px; font-weight:700; color:#7A869A; text-transform:uppercase; margin-bottom:6px;">Current State Status</label>
                    <span class="role-tag" style="display:block; text-align:center; padding:10px; font-size:14px; font-weight:700; background: <?= $order['status'] == 'Completed' ? '#E8F5E9; color:#2E7D32;' : ($order['status'] == 'Pending' ? '#FFF3E0; color:#E65100;' : ($order['status'] == 'Processing' ? '#E1F5FE; color:#0288D1;' : '#FFEBEE; color:#C62828;')) ?>">
                        <?= $order['status']; ?>
                    </span>
                </div>
                
                <hr style="border:0; border-top:1px solid #E5E9F0; margin:20px 0;">

                <!-- RBAC Interactive Controls Interface Section -->
                <?php if ($_SESSION['user']['role_name'] == 'Bookkeeper') { ?>
                    <p style="text-align:center; color:#7A869A; font-size:13px; font-style:italic;">Your auditing role profile is restricted to View-Only configurations.</p>
                <?php } else { ?>
                    
                    <?php if ($order['status'] == 'Pending') { ?>
                        <a href="orders.php?action=processing&id=<?= $order['order_id']; ?>" class="btn" style="display:block; text-align:center; margin-bottom:10px; background:#0288D1; text-transform:uppercase; font-size:13px;">Advance to Processing</a>
                    <?php } ?>

                    <?php if ($order['status'] == 'Pending' || $order['status'] == 'Processing') { ?>
                        <a href="orders.php?action=complete&id=<?= $order['order_id']; ?>" class="btn" style="display:block; text-align:center; margin-bottom:10px; text-transform:uppercase; font-size:13px;" onclick="return confirm('Execute First-In, First-Out (FIFO) allocation algorithms and lock inventory parameters?')">Confirm & Complete Delivery</a>
                    <?php } ?>

                    <?php if ($order['status'] != 'Cancelled') { ?>
                        <a href="orders.php?action=cancel&id=<?= $order['order_id']; ?>" class="btn" style="display:block; text-align:center; background:#D32F2F; text-transform:uppercase; font-size:13px;" onclick="return confirm('Execute order cancellation? This automatically triggers symmetrical stock restoration back into original batch nodes.')">Cancel & Rollback Record</a>
                    <?php } else { ?>
                        <p style="text-align:center; color:#D32F2F; font-size:13px; font-weight:600;">This ticket profile is permanently deactivated.</p>
                    <?php } ?>

                    <!-- 🌟 STEP 10F CONNECTED: Print Invoice Link (Only visible on Completed status) -->
                    <?php if ($order['status'] == 'Completed') { ?>
                        <a href="reports.php?action=invoice&id=<?= $order['order_id']; ?>" class="btn" target="_blank" style="display:block; text-align:center; background:#455A64; margin-top:10px; text-transform:uppercase; font-size:13px; color:white;">
                            📄 Print Invoice
                        </a>
                    <?php } ?>

                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
