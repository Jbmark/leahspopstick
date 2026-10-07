<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <?php
        $success = $_SESSION['raw_material_success'] ?? '';
        $error = $_SESSION['raw_material_error'] ?? '';

        unset($_SESSION['raw_material_success']);
        unset($_SESSION['raw_material_error']);
        ?>


        <!-- PAGE HEADER -->

        <h1>Raw Materials</h1>

        <p>
            Manage raw materials and monitor available stock.
        </p>


        <!-- SUCCESS MESSAGE -->

        <?php if ($success): ?>

            <div
                class="login-error"
                style="
                    background:#E8F5E9;
                    color:#2E7D32;
                    border-left-color:#2E7D32;
                "
            >
                <?= htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>


        <!-- ERROR MESSAGE -->

        <?php if ($error): ?>

            <div class="login-error">

                <?= htmlspecialchars($error); ?>

            </div>

        <?php endif; ?>


        <!-- ADD RAW MATERIAL -->

        <a
            href="raw_materials.php?action=create"
            class="btn"
        >
            + Add Raw Material
        </a>


        <br><br>


        <!-- INVENTORY TABLE -->

        <h2>
            Raw Material Inventory
        </h2>

        <br>


        <table>

            <thead>

                <tr>

                    <th>Material</th>

                    <th>Description</th>

                    <th>Unit</th>

                    <th>Quantity</th>

                    <th>Reorder Level</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($rawMaterials)): ?>

                    <tr>

                        <td
                            colspan="7"
                            style="text-align:center;"
                        >
                            No raw materials found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($rawMaterials as $material): ?>

                        <tr>


                            <!-- MATERIAL -->

                            <td>

                                <strong>
                                    <?= htmlspecialchars(
                                        $material['material_name']
                                    ); ?>
                                </strong>

                            </td>


                            <!-- DESCRIPTION -->

                            <td>

                                <?= !empty(
                                    $material['description']
                                )
                                    ? htmlspecialchars(
                                        $material['description']
                                    )
                                    : '—'; ?>

                            </td>


                            <!-- UNIT -->

                            <td>

                                <?= htmlspecialchars(
                                    $material['unit']
                                ); ?>

                            </td>


                            <!-- QUANTITY -->

                            <td>

                                <?= htmlspecialchars(
                                    $material['quantity']
                                ); ?>

                                <?= htmlspecialchars(
                                    $material['unit']
                                ); ?>


                                <?php if (
                                    $material['quantity']
                                    <= $material['reorder_level']
                                    &&
                                    $material['status'] === 'Active'
                                ): ?>

                                    <span
                                        style="
                                            display:inline-block;
                                            margin-left:6px;
                                            padding:3px 7px;
                                            border-radius:4px;
                                            background:#FFEBEE;
                                            color:#C62828;
                                            font-size:11px;
                                            font-weight:600;
                                        "
                                    >
                                        LOW STOCK
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- REORDER LEVEL -->

                            <td>

                                <?= htmlspecialchars(
                                    $material['reorder_level']
                                ); ?>

                                <?= htmlspecialchars(
                                    $material['unit']
                                ); ?>

                            </td>


                            <!-- STATUS -->

                            <td>

                                <?php if (
                                    $material['status'] === 'Active'
                                ): ?>

                                    <span
                                        style="
                                            color:#2E7D32;
                                            font-weight:600;
                                        "
                                    >
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span
                                        style="
                                            color:#757575;
                                            font-weight:600;
                                        "
                                    >
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- ACTIONS -->

                            <td>

                                <a
                                    href="raw_materials.php?action=stock_in&id=<?= intval($material['raw_material_id']); ?>"
                                    class="btn"
                                    style="
                                        background:#2E7D32;
                                        margin:2px;
                                    "
                                >
                                    Stock In
                                </a>


                                <a
                                    href="raw_materials.php?action=stock_out&id=<?= intval($material['raw_material_id']); ?>"
                                    class="btn"
                                    style="
                                        background:#EF6C00;
                                        margin:2px;
                                    "
                                >
                                    Stock Out
                                </a>


                                <a
                                    href="raw_materials.php?action=adjustment&id=<?= intval($material['raw_material_id']); ?>"
                                    class="btn"
                                    style="
                                        background:#757575;
                                        margin:2px;
                                    "
                                >
                                    Adjust
                                </a>


                                <a
                                    href="raw_materials.php?action=history&id=<?= intval($material['raw_material_id']); ?>"
                                    class="btn"
                                    style="
                                        background:#1976D2;
                                        margin:2px;
                                    "
                                >
                                    History
                                </a>


                                <a
                                    href="raw_materials.php?action=edit&id=<?= intval($material['raw_material_id']); ?>"
                                    class="btn"
                                    style="
                                        margin:2px;
                                    "
                                >
                                    Edit
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<?php include 'app/views/layouts/footer.php'; ?>

