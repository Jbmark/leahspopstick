<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        
<!-- PAGE HEADER -->
<div style="
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:12px;
    margin-bottom:20px;
">

    <h1 style="margin:0;">Inventory Dashboard</h1>

    <div style="
        display:flex;
        align-items:center;
        flex-wrap:wrap;
        gap:10px;
    ">

        <a href="inventory.php?action=scan_qr"
           class="btn"
           style="
               background:#166534;
               color:#fff;
               text-decoration:none;
           ">
            Scan Batch QR
        </a>

        <a href="inventory.php?action=add_batch" class="btn">
            + Add Batch
        </a>

    </div>

</div>


<section class="content-section">
    <div style="margin-bottom:16px;">
        <h2 style="margin:0 0 6px; font-size:20px; color:#334155;">
            Product Stock Overview
        </h2>

        <p style="margin:0; color:#64748b; font-size:14px;">
            Monitor stock levels, reorder points, and projected sales value.
            Select a product for more details.
        </p>
    </div>

    <div class="inventory-product-grid">
        <?php foreach ($stocks as $s): ?>
            <?php
                $stock = (float) $s['current_stock'];
                $reorder = (float) $s['reorder_level'];
                $price = (float) ($s['selling_price'] ?? 0);
                $projectedValue = $stock * $price;
                $isLowStock = $stock <= $reorder;

                $imagePath = !empty($s['image'])
                    ? '../public/' . ltrim($s['image'], '/')
                    : '';
            ?>

            <details class="inventory-product-card">
    <summary class="inventory-product-summary">
                    <?php if ($imagePath): ?>
                        <img
    class="inventory-product-image"
    src="<?= htmlspecialchars($imagePath) ?>"
    alt="<?= htmlspecialchars($s['product_name']) ?>"
    loading="lazy"
    onerror="this.style.display='none';"
>
                    <?php else: ?>
                        <div class="inventory-no-image">
                            No product image
                        </div>
                    <?php endif; ?>

                    <div class="inventory-product-info">
                        <h3>
                            <?= htmlspecialchars($s['product_name']) ?>
                        </h3>

                        <p>
                            Remaining Stock:
                            <strong>
                                <?= number_format($stock, 0) ?>
                                <?= htmlspecialchars($s['unit']) ?>
                            </strong>
                        </p>

                        <span class="inventory-stock-badge <?= $isLowStock ? 'low' : 'normal' ?>">
                            <?= $isLowStock ? 'Low Stock' : 'In Stock' ?>
                        </span>

                        <p class="inventory-card-hint">
                            Click to view details
                        </p>
                    </div>
                </summary>

                <div class="inventory-product-details">
                    <p>
                        <strong>Reorder Level:</strong>
                        <?= number_format($reorder, 0) ?>
                        <?= htmlspecialchars($s['unit']) ?>
                    </p>

                    <p>
                        <strong>Selling Price:</strong>
                        ₱<?= number_format($price, 2) ?>
                    </p>

                    <p>
                        <strong>Projected Sales Value:</strong>
                        <span class="inventory-projected-value">
                            ₱<?= number_format($projectedValue, 2) ?>
                        </span>
                    </p>

                    <p class="inventory-value-note">
                        Estimated value if all remaining stock were sold
                        at the current selling price. This is not actual
                        sales revenue or profit.
                    </p>

                    <?php if (!empty($s['description'])): ?>
                        <p>
                            <strong>Description:</strong>
                            <?= htmlspecialchars($s['description']) ?>
                        </p>
                    <?php endif; ?>

                    <a
                        class="btn btn-primary"
                        href="inventory.php?action=add_batch"
                    >
                        Add Inventory Batch
                    </a>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
</section>



        <!-- INVENTORY BATCH LEDGER -->
     <h2 style="font-size:20px; margin:28px 0 12px; color:#334155;">
    FIFO Batches Ledger
</h2>
<p style="margin:-4px 0 16px; color:#64748b; font-size:14px;">
    View received quantities, remaining stock, batch status, and QR details.
</p>

        <div class="card" style="padding:0; overflow:hidden;">

            <table>

                <tr>
                    <th>Batch</th>
                    <th>Product</th>
                    <th>Received</th>
                    <th>Remaining</th>
                    <th>Date Received</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php if (empty($batches)) { ?>

                    <tr>
                        <td colspan="7"
                            style="text-align:center; color:#7A869A; padding:25px;">
                            No inventory batches found.
                        </td>
                    </tr>

                <?php } else { ?>

                    <?php foreach ($batches as $b) { ?>
                
                        <tr style="
                            <?= $b['status'] === 'Depleted'
                                ? 'opacity:0.55; background:#FAFAFA;'
                                : ''; ?>
                        ">
                            

                            <!-- BATCH NUMBER -->
                            <td>

                                <code style="
                                    background:#ECEFF1;
                                    padding:4px 8px;
                                    border-radius:4px;
                                    font-weight:600;
                                ">
                                    <?= htmlspecialchars($b['batch_number']); ?>
                                </code>

                            </td>


                            <!-- PRODUCT -->
                            <td>

                                <b>
                                    <?= htmlspecialchars($b['product_name']); ?>
                                </b>

                            </td>


                            <!-- QUANTITY RECEIVED -->
                            <td>

                                <?= number_format(
                                (int)$b['quantity_received'],
                                0
                            ); ?>

                            </td>


                            <!-- QUANTITY REMAINING -->
                            <td>

                                <span class="role-tag"
                                      style="
                                      background:
                                      <?= $b['status'] === 'Available'
                                          ? '#E8F5E9'
                                          : '#CFD8DC'; ?>;

                                      color:
                                      <?= $b['status'] === 'Available'
                                          ? '#2E7D32'
                                          : '#37474F'; ?>;
                                      ">

                                    <?= number_format(
                                        (int)$b['quantity_remaining'],
                                        0
                                    ); ?>

                                    remaining

                                </span>

                            </td>


                            <!-- DATE RECEIVED -->
                            <td>

                                <?= date(
                                    'M d, Y',
                                    strtotime($b['date_received'])
                                ); ?>

                            </td>


                            <!-- BATCH STATUS -->
                            <td>

                                <?php if ($b['status'] === 'Available') { ?>

                                    <span class="role-tag"
                                          style="
                                          background:#E8F5E9;
                                          color:#2E7D32;
                                          ">
                                        Available
                                    </span>

                                <?php } else { ?>

                                    <span class="role-tag"
                                          style="
                                          background:#ECEFF1;
                                          color:#37474F;
                                          ">
                                        Depleted
                                    </span>

                                <?php } ?>

                            </td>
                            
                            <td style="padding:10px;">
    <a
        href="inventory.php?action=view_batch&id=<?= (int)$b['batch_id'] ?>"
        class="btn btn-primary"
        style="text-decoration:none; white-space:nowrap;"
    >
        View QR
    </a>
</td>

                        </tr>

                    <?php } ?>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>