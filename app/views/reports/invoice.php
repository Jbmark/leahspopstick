<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice #ORD-<?= $order['order_id']; ?></title>

<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 40px;
    color: #333;
    background-color: #ffffff;
}

.header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 40px;
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

.meta-info {
    margin-bottom: 30px;
    line-height: 1.6;
    font-size: 15px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th, td {
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
    background-color: #FAFAFA;
}

.total {
    text-align: right;
    font-size: 22px;
    margin-top: 30px;
    color: #1B5E20;
}

.btn-print {
    display: inline-block;
    padding: 12px 24px;
    background-color: #1B5E20;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 30px;
    transition: background-color 0.2s;
}

.btn-print:hover {
    background-color: #2E7D32;
}

@media print {
    body { margin: 20px; }
    .no-print { display: none !important; }
    th { background: #1B5E20 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>

<body>

<div class="header">
    <div>
        <h2>Leah's Popstick</h2>
        <p>Talacogon, Agusan del Sur</p>
    </div>
    <div style="text-align: right;">
        <h3>Commercial Invoice</h3>
        <p style="font-weight: 600; font-family: monospace; font-size:15px;">#ORD-<?= $order['order_id']; ?></p>
    </div>
</div>

<div class="meta-info">
    <p><strong>Customer Client Entity:</strong> <?= htmlspecialchars($order['customer_name']); ?></p>
    <p><strong>Fulfillment Date Stamp:</strong> <?= $order['completed_at'] ? date('F d, Y h:i A', strtotime($order['completed_at'])) : date('F d, Y', strtotime($order['created_at'])); ?></p>
    <p><strong>Acquisition Channel:</strong> <?= htmlspecialchars($order['order_source']); ?></p>
</div>

<table>
    <thead>
        <tr>
            <th>Product Specification</th>
            <th style="text-align: center;">Quantity Ordered</th>
            <th style="text-align: right;">Unit Price Vector</th>
            <th style="text-align: right;">Subtotal Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($items as $item) { ?>
        <tr>
            <td><b><?= htmlspecialchars($item['product_name']); ?></b></td>
            <td style="text-align: center;"><?= $item['quantity']; ?> <?= htmlspecialchars($item['unit']); ?></td>
            <td style="text-align: right;">₱<?= number_format($item['unit_price'], 2); ?></td>
            <td style="text-align: right; font-weight: 600;">₱<?= number_format($item['subtotal'], 2); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<div class="total">
    <strong>Total Valuation: ₱<?= number_format($order['total_amount'], 2); ?></strong>
</div>

<button onclick="window.print()" class="btn-print no-print">
    🖨️ Execute Native Document Print
</button>

</body>
</html>
