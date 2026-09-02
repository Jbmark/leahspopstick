<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div>
                <h1>Order Management Panel</h1>
                <p>Centralized ledger tracking multi-channel corporate request pipelines.</p>
            </div>
            <?php if ($_SESSION['user']['role_name'] != 'Bookkeeper') { ?>
                <a href="orders.php?action=create" class="btn" style="padding: 12px 24px; font-size:14px;">+ New Order Entry</a>
            <?php } ?>
        </div>

        <div class="card" style="padding: 0; overflow: hidden; border-radius:12px;">
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Source</th>
                    <th>Fulfillment State</th>
                    <th>Total Capital Gross</th>
                    <th>Date Registered</th>
                    <th>Action</th>
                </tr>
                <?php if (empty($orders)) { ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#7A869A; padding:40px; font-size:15px;">
                            No administrative ordering metrics found in database ledger nodes.
                        </td>
                    </tr>
                <?php } else { 
                    foreach ($orders as $o) { 
                        // Color coding mappings for multi-channel source visualization tags
                        $sourceBg = '#E0F2F1'; $sourceColor = '#004D40'; // Default Walk-in
                        switch($o['order_source']) {
                            case 'Website':   $sourceBg = '#E8F5E9'; $sourceColor = '#2E7D32'; break; // Green
                            case 'Messenger': $sourceBg = '#E3F2FD'; $sourceColor = '#0D47A1'; break; // Blue
                            case 'Viber':     $sourceBg = '#F3E5F5'; $sourceColor = '#4A148C'; break; // Purple
                            case 'Phone':     $sourceBg = '#FFF3E0'; $sourceColor = '#E65100'; break; // Orange
                            case 'Email':     $sourceBg = '#ECEFF1'; $sourceColor = '#37474F'; break; // Gray
                        }
                    ?>
                    <tr>
                        <td><code style="background:#ECEFF1; padding:4px 8px; border-radius:4px; font-weight:600; color:#333;">#ORD-<?= $o['order_id']; ?></code></td>
                        <td><b><?= htmlspecialchars($o['customer_name']); ?></b></td>
                        <td>
                            <span class="role-tag" style="background: <?= $sourceBg; ?>; color: <?= $sourceColor; ?>; font-weight:600; text-transform:none;">
                                <?= $o['order_source']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="role-tag" style="background: <?= $o['status'] == 'Completed' ? '#E8F5E9; color:#2E7D32;' : ($o['status'] == 'Pending' ? '#FFF3E0; color:#E65100;' : ($o['status'] == 'Processing' ? '#E1F5FE; color:#0288D1;' : '#FFEBEE; color:#C62828;')) ?>">
                                <?= $o['status']; ?>
                            </span>
                        </td>
                        <td><b>₱<?= number_format($o['total_amount'], 2); ?></b></td>
                        <td><?= date('M d, Y h:i A', strtotime($o['created_at'])); ?></td>
                        <td>
                            <a href="orders.php?action=view&id=<?= $o['order_id']; ?>" style="color:#1B5E20; font-weight:700; text-decoration:none;">
                                View Ticket &rarr;
                            </a>
                        </td>
                    </tr>
                <?php } } ?>
            </table>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
