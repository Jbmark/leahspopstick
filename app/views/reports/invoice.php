<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        Order Invoice #ORD-<?= $order['order_id']; ?>
    </title>

    <style>

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 40px;
            color: #333;
            background: #ffffff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 35px;
            border-bottom: 2px solid #E5E9F0;
            padding-bottom: 20px;
        }

        .header h2 {
            color: #1B5E20;
            margin: 0 0 5px 0;
            font-size: 26px;
        }

        .header h3 {
            margin: 0 0 5px 0;
            font-size: 22px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .company-info p,
        .invoice-info p {
            margin: 4px 0;
        }

        .meta-info {
            margin-bottom: 30px;
            line-height: 1.6;
            font-size: 15px;
        }

        .customer-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 25px;
        }

        .info-box {
            border: 1px solid #E5E9F0;
            border-radius: 8px;
            padding: 15px;
        }

        .info-box h4 {
            margin: 0 0 10px 0;
            color: #1B5E20;
            font-size: 13px;
            text-transform: uppercase;
        }

        .info-box p {
            margin: 5px 0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #E5E9F0;
            padding: 12px;
            text-align: left;
            font-size: 15px;
        }

        th {
            background: #1B5E20;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) td {
            background: #FAFAFA;
        }

        .total {
            text-align: right;
            font-size: 22px;
            margin-top: 25px;
            color: #1B5E20;
        }

        .footer-note {
            margin-top: 35px;
            padding-top: 15px;
            border-top: 1px solid #E5E9F0;
            color: #666;
            font-size: 13px;
            text-align: center;
        }

        .btn-print {
            display: inline-block;
            padding: 12px 24px;
            background: #1B5E20;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 25px;
        }

        .btn-print:hover {
            background: #2E7D32;
        }

        @media print {

            body {
                margin: 20px;
            }

            .no-print {
                display: none !important;
            }

            th {
                background: #1B5E20 !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

        }

    </style>

</head>


<body>


    <!-- HEADER -->

    <div class="header">

        <div class="company-info">

            <h2>Leah's Popstick</h2>

            <p>
                Talacogon, Agusan del Sur
            </p>

        </div>


        <div
            class="invoice-info"
            style="text-align:right;"
        >

            <h3>Order Invoice</h3>

            <p
                style="
                    font-weight:600;
                    font-family:monospace;
                    font-size:15px;
                "
            >
                #ORD-<?= $order['order_id']; ?>
            </p>

        </div>

    </div>


    <!-- CUSTOMER / ORDER INFORMATION -->

    <div class="customer-section">

        <div class="info-box">

            <h4>Customer Information</h4>

            <p>
                <strong>Name:</strong>
                <?= htmlspecialchars(
                    $order['customer_name']
                ); ?>
            </p>


            <?php if (!empty($order['contact_number'])) { ?>

                <p>
                    <strong>Contact:</strong>
                    <?= htmlspecialchars(
                        $order['contact_number']
                    ); ?>
                </p>

            <?php } ?>


            <?php if (!empty($order['email'])) { ?>

                <p>
                    <strong>Email:</strong>
                    <?= htmlspecialchars(
                        $order['email']
                    ); ?>
                </p>

            <?php } ?>

        </div>


        <div class="info-box">

            <h4>Order Information</h4>

            <p>
                <strong>Order Date:</strong>

                <?= date(
                    'F d, Y h:i A',
                    strtotime($order['created_at'])
                ); ?>
            </p>


            <?php if (!empty($order['completed_at'])) { ?>

                <p>
                    <strong>Completed Date:</strong>

                    <?= date(
                        'F d, Y h:i A',
                        strtotime($order['completed_at'])
                    ); ?>
                </p>

            <?php } ?>


            <p>
                <strong>Order Source:</strong>

                <?= htmlspecialchars(
                    $order['order_source']
                ); ?>
            </p>

        </div>

    </div>


    <!-- DELIVERY ADDRESS -->

    <div class="meta-info">

        <p>

            <strong>Delivery Address:</strong>

            <?= htmlspecialchars(
                $order['delivery_address']
                ?: 'No delivery address recorded.'
            ); ?>

        </p>

    </div>


    <!-- ORDER ITEMS -->

    <table>

        <thead>

            <tr>

                <th>
                    Product
                </th>

                <th style="text-align:center;">
                    Quantity
                </th>

                <th style="text-align:right;">
                    Unit Price
                </th>

                <th style="text-align:right;">
                    Subtotal
                </th>

            </tr>

        </thead>


        <tbody>

            <?php foreach ($items as $item) { ?>

                <tr>

                    <td>

                        <b>
                            <?= htmlspecialchars(
                                $item['product_name']
                            ); ?>
                        </b>

                    </td>


                    <td style="text-align:center;">

                        <?= number_format(
                            $item['quantity'],
                            0
                        ); ?>

                        <?= htmlspecialchars(
                            $item['unit']
                        ); ?>

                    </td>


                    <td style="text-align:right;">

                        ₱<?= number_format(
                            $item['unit_price'],
                            2
                        ); ?>

                    </td>


                    <td
                        style="
                            text-align:right;
                            font-weight:600;
                        "
                    >

                        ₱<?= number_format(
                            $item['subtotal'],
                            2
                        ); ?>

                    </td>

                </tr>

            <?php } ?>

        </tbody>

    </table>


    <!-- TOTAL -->

    <div class="total">

        <strong>
            Total Amount:
            ₱<?= number_format(
                $order['total_amount'],
                2
            ); ?>
        </strong>

    </div>


    <!-- FOOTER -->

    <div class="footer-note">

        Thank you for your order.

    </div>


    <!-- PRINT BUTTON -->

    <button
        onclick="window.print()"
        class="btn-print no-print"
    >
        Print Invoice
    </button>


</body>

</html>