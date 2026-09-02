<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Sales Report - <?= date('F Y'); ?></title>

<style>
body {
    font-family: 'Segoe UI', Arial, sans-serif;
    margin: 40px;
    color: #333;
    background-color: #ffffff;
}

.header {
    border-bottom: 2px solid #E5E9F0;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.header h2 {
    color: #1B5E20;
    margin: 0 0 5px 0;
    font-size: 24px;
}

.header p {
    color: #7A869A;
    margin: 0;
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

.summary-block {
    margin-top: 30px;
    padding: 20px;
    background-color: #F8F9FA;
    border-radius: 8px;
    border: 1px solid #E5E9F0;
    display: inline-block;
    min-width: 300px;
    float: right;
    text-align: right;
}

.summary-block h3 {
    margin: 0;
    font-size: 20px;
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
    <h2>Leah's Popstick - Commercial Sales Audit Log</h2>
    <p>Monthly Performance Report Allocation Overview: <b><?= date('F Y'); ?></b></p>
</div>

<table>
    <thead>
        <tr>
            <th>Order Document Ticket</th>
            <th>Customer Client Entity</th>
            <th>Fulfillment Completion Date</th>
            <th style="text-align: right;">Total Realized Value</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($recent)) { ?>
            <tr><td colspan="4" style="text-align:center; color:#7A869A; padding:30px;">No realized sales logs logged for the current duration context.</td></tr>
        <?php } else { 
            foreach($recent as $sale) { ?>
            <tr>
                <td><code style="font-family: monospace; font-weight: 600;">#ORD-<?= $sale['order_id']; ?></code></td>
                <td><b><?= htmlspecialchars($sale['customer_name']); ?></b></td>
                <td><?= date('M d, Y h:i A', strtotime($sale['completed_at'])); ?></td>
                <td style="text-align: right; font-weight: 700; color: #1B5E20;">₱<?= number_format($sale['total_amount'], 2); ?></td>
            </tr>
        <?php } } ?>
    </tbody>
</table>

<div style="width: 100%; overflow: hidden;">
    <div class="summary-block">
        <h3>Total Monthly Income Yield: ₱<?= number_format($monthlySales, 2); ?></h3>
    </div>
</div>

<button onclick="window.print()" class="btn-print no-print">
    🖨️ Execute Native Document Print
</button>

</body>
</html>
