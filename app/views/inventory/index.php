<?php include 'app/views/layouts/header.php'; ?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1>Inventory Dashboard</h1>
            <a href="inventory.php?action=add_batch" class="btn">+ Add Batch</a>
        </div>

        <h2 style="font-size:18px; margin-bottom:12px; color:#5A6A85;">Current Stock Levels</h2>
        <div class="card" style="padding: 0; overflow: hidden; margin-bottom:30px;">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>
                <?php foreach($stocks as $s){ ?>
                <tr>
                    <td><b><?= htmlspecialchars($s['product_name']); ?></b></td>
                    <td><span class="role-tag" style="background:#ECEFF1; color:#455A64; font-size:14px;"><?= $s['current_stock']; ?> <?= htmlspecialchars($s['unit']); ?></span></td>
                    <td><?= $s['reorder_level']; ?> <?= htmlspecialchars($s['unit']); ?></td>
                    <td>
                        <?php if($s['current_stock'] <= $s['reorder_level']){ ?>
                            <span class="role-tag" style="background:#FFEBEE; color:#C62828;">⚠️ LOW STOCK</span>
                        <?php } else { ?>
                            <span class="role-tag" style="background:#E8F5E9; color:#2E7D32;">Optimal Available</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>

        <h2 style="font-size:18px; margin-bottom:12px; color:#5A6A85;">Active FIFO Batches Ledger</h2>
        <div class="card" style="padding: 0; overflow: hidden;">
            <table>
                <tr>
                    <th>Batch</th>
                    <th>Product</th>
                    <th>Received</th>
                    <th>Remaining</th>
                    <th>Date Received</th>
                </tr>
                <?php foreach($batches as $b){ ?>
                <tr style="<?= $b['quantity_remaining'] == 0 ? 'opacity: 0.4; background:#FAFAFA;' : '' ?>">
                    <td><code style="background:#ECEFF1; padding:4px 8px; border-radius:4px; font-weight:600;"><?= htmlspecialchars($b['batch_number']); ?></code></td>
                    <td><b><?= htmlspecialchars($b['product_name']); ?></b></td>
                    <td><?= $b['quantity_received']; ?></td>
                    <td>
                        <span class="role-tag" style="background: <?= $b['quantity_remaining'] > 0 ? '#E8F5E9; color:#2E7D32;' : '#CFD8DC; color:#37474F;' ?>">
                            <?= $b['quantity_remaining']; ?> remaining
                        </span>
                    </td>
                    <td><?= date('M d, Y', strtotime($b['date_received'])); ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
