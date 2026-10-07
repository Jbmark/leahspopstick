<?php include 'app/views/layouts/header.php'; ?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

        <div
            style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                margin-bottom:20px;
            "
        >

            <div>

                <h1>Order Management</h1>

                <p>
                    View and manage customer orders from different order sources.
                </p>

            </div>


            <?php if ($_SESSION['user']['role_name'] != 'Bookkeeper') { ?>

                <a
                    href="orders.php?action=create"
                    class="btn"
                    style="
                        padding:12px 24px;
                        font-size:14px;
                    "
                >
                    + New Order
                </a>

            <?php } ?>

        </div>


        <div
            class="card"
            style="
                padding:0;
                overflow:hidden;
                border-radius:12px;
            "
        >

            <table>

                <tr>

                    <th>Order ID</th>

                    <th>Customer</th>

                    <th>Order Source</th>

                    <th>Status</th>

                    <th>Total Amount</th>

                    <th>Date</th>

                    <th>Encoded By</th>

                    <th>Action</th>

                </tr>


                <?php if (empty($orders)) { ?>

                    <tr>

                        <td
                            colspan="8"
                            style="
                                text-align:center;
                                color:#7A869A;
                                padding:40px;
                                font-size:15px;
                            "
                        >
                            No orders found.
                        </td>

                    </tr>

                <?php } else { ?>

                    <?php foreach ($orders as $o) { ?>

                        <?php

                        /*
                         * Order source styling.
                         */

                        $sourceBg = '#E0F2F1';
                        $sourceColor = '#004D40';

                        switch ($o['order_source']) {

                            case 'Website':
                                $sourceBg = '#E8F5E9';
                                $sourceColor = '#2E7D32';
                                break;

                            case 'Messenger':
                                $sourceBg = '#E3F2FD';
                                $sourceColor = '#0D47A1';
                                break;

                            case 'Viber':
                                $sourceBg = '#F3E5F5';
                                $sourceColor = '#4A148C';
                                break;

                            case 'Phone':
                                $sourceBg = '#FFF3E0';
                                $sourceColor = '#E65100';
                                break;

                            case 'Email':
                                $sourceBg = '#ECEFF1';
                                $sourceColor = '#37474F';
                                break;

                            case 'Walk-in':
                                $sourceBg = '#E0F2F1';
                                $sourceColor = '#004D40';
                                break;
                        }


                        /*
                         * Order status styling.
                         */

                        $statusBg = '#ECEFF1';
                        $statusColor = '#455A64';

                        switch ($o['status']) {

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

                        <tr>

                            <!-- Order ID -->

                            <td>

                                <code
                                    style="
                                        background:#ECEFF1;
                                        padding:4px 8px;
                                        border-radius:4px;
                                        font-weight:600;
                                        color:#333;
                                    "
                                >
                                    #ORD-<?= $o['order_id']; ?>
                                </code>

                            </td>


                            <!-- Customer -->

                            <td>

                                <b>
                                    <?= htmlspecialchars(
                                        $o['customer_name']
                                    ); ?>
                                </b>

                            </td>


                            <!-- Order Source -->

                            <td>

                                <span
                                    class="role-tag"
                                    style="
                                        background:<?= $sourceBg; ?>;
                                        color:<?= $sourceColor; ?>;
                                        font-weight:600;
                                        text-transform:none;
                                    "
                                >
                                    <?= htmlspecialchars(
                                        $o['order_source']
                                    ); ?>
                                </span>

                            </td>


                            <!-- Status -->

                            <td>

                                <span
                                    class="role-tag"
                                    style="
                                        background:<?= $statusBg; ?>;
                                        color:<?= $statusColor; ?>;
                                        font-weight:600;
                                        text-transform:none;
                                    "
                                >
                                    <?= htmlspecialchars(
                                        $o['status']
                                    ); ?>
                                </span>

                            </td>


                            <!-- Total -->

                            <td>

                                <b>
                                    ₱<?= number_format(
                                        $o['total_amount'],
                                        2
                                    ); ?>
                                </b>

                            </td>


                            <!-- Date -->

                            <td>

                                <?= date(
                                    'M d, Y h:i A',
                                    strtotime($o['created_at'])
                                ); ?>

                            </td>


                            <!-- Encoder -->

                            <td>

                                <?= !empty($o['encoder_name'])
                                    ? htmlspecialchars($o['encoder_name'])
                                    : 'Website'; ?>

                            </td>


                            <!-- Action -->

                            <td>

                                <a
                                    href="orders.php?action=view&id=<?= $o['order_id']; ?>"
                                    style="
                                        color:#1B5E20;
                                        font-weight:700;
                                        text-decoration:none;
                                    "
                                >
                                    View &rarr;
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