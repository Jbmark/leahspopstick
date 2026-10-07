<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <div
            style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:20px;
                margin-bottom:25px;
            "
        >

            <div>

                <h1>Transaction History</h1>

                <p>
                    <?= htmlspecialchars($rawMaterial['material_name']); ?>
                </p>

            </div>


            <a
                href="raw_materials.php"
                class="btn"
                style="background:#757575;"
            >
                Back to Raw Materials
            </a>

        </div>


        <!-- CURRENT STOCK -->

        <div
            style="
                background:#F5F7FA;
                border:1px solid #E5E9F0;
                border-radius:6px;
                padding:16px;
                margin-bottom:25px;
            "
        >

            <h2 style="margin:0;">

                Current Stock:

                <?= htmlspecialchars(
                    $rawMaterial['quantity']
                ); ?>

                <?= htmlspecialchars(
                    $rawMaterial['unit']
                ); ?>

            </h2>

        </div>


        <!-- TRANSACTION TABLE -->

        <table>

            <thead>

                <tr>

                    <th>Date</th>

                    <th>Transaction</th>

                    <th>Quantity</th>

                    <th>Remarks</th>

                    <th>Recorded By</th>

                </tr>

            </thead>


            <tbody>

                <?php if (empty($transactions)): ?>

                    <tr>

                        <td
                            colspan="5"
                            style="text-align:center;"
                        >
                            No transactions found.
                        </td>

                    </tr>

                <?php else: ?>

                    <?php foreach ($transactions as $transaction): ?>

                        <tr>

                            <td>
                                <?= htmlspecialchars(
                                    $transaction['transaction_date']
                                ); ?>
                            </td>


                            <td>
                                <?= htmlspecialchars(
                                    $transaction['transaction_type']
                                ); ?>
                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $transaction['quantity']
                                ); ?>

                                <?= htmlspecialchars(
                                    $rawMaterial['unit']
                                ); ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $transaction['remarks'] ?? ''
                                ); ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $transaction['created_by_name']
                                    ?? 'Unknown'
                                ); ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>


<?php include 'app/views/layouts/footer.php'; ?>

