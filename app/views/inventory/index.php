<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <!-- PAGE HEADER -->
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">

            <h1>Inventory Dashboard</h1>

            <a href="inventory.php?action=add_batch" class="btn">
                + Add Batch
            </a>

        </div>


        <!-- CURRENT STOCK LEVELS -->
        <h2 style="font-size:18px; margin-bottom:12px; color:#5A6A85;">
            Current Stock Levels
        </h2>

        <div class="card" style="padding:0; overflow:hidden; margin-bottom:30px;">

            <table>

                <tr>
                    <th>Product</th>
                    <th>Current Stock</th>
                    <th>Reorder Level</th>
                    <th>Status</th>
                </tr>

                <?php if (empty($stocks)) { ?>

                    <tr>
                        <td colspan="4"
                            style="text-align:center; color:#7A869A; padding:25px;">
                            No inventory records found.
                        </td>
                    </tr>

                <?php } else { ?>

                    <?php foreach ($stocks as $s) { ?>

                        <?php
                        $currentStock = (float)$s['current_stock'];
                        $reorderLevel = (float)$s['reorder_level'];
                        ?>

                        <tr>

                            <td>
                                <b>
                                    <?= htmlspecialchars($s['product_name']); ?>
                                </b>
                            </td>

                            <td>

                                <span class="role-tag"
                                      style="
                                      background:#ECEFF1;
                                      color:#455A64;
                                      font-size:14px;
                                      ">

                                    <?= number_format($currentStock, 2); ?>
                                    <?= htmlspecialchars($s['unit']); ?>

                                </span>

                            </td>

                            <td>

                                <?= number_format($reorderLevel, 2); ?>
                                <?= htmlspecialchars($s['unit']); ?>

                            </td>

                            <td>

                                <?php if ($currentStock <= $reorderLevel) { ?>

                                    <span class="role-tag"
                                          style="
                                          background:#FFEBEE;
                                          color:#C62828;
                                          ">
                                        ⚠️ LOW STOCK
                                    </span>

                                <?php } else { ?>

                                    <span class="role-tag"
                                          style="
                                          background:#E8F5E9;
                                          color:#2E7D32;
                                          ">
                                        Optimal Available
                                    </span>

                                <?php } ?>

                            </td>

                        </tr>

                    <?php } ?>

                <?php } ?>

            </table>

        </div>


        <!-- INVENTORY BATCH LEDGER -->
        <h2 style="font-size:18px; margin-bottom:12px; color:#5A6A85;">
            FIFO Batches Ledger
        </h2>

        <div class="card" style="padding:0; overflow:hidden;">

            <table>

                <tr>
                    <th>Batch</th>
                    <th>Product</th>
                    <th>Received</th>
                    <th>Remaining</th>
                    <th>Date Received</th>
                    <th>Status</th>
                </tr>

                <?php if (empty($batches)) { ?>

                    <tr>
                        <td colspan="6"
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
                                    (float)$b['quantity_received'],
                                    2
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
                                        (float)$b['quantity_remaining'],
                                        2
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

                        </tr>

                    <?php } ?>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>