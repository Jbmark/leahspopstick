<?php include 'app/views/layouts/header.php';?>

<div class="container">

<?php include 'app/views/layouts/sidebar.php';?>

<div class="content">

    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1>Financial Performance Dashboard</h1>
            
            <!-- DATE RANGE FILTER -->
<div class="card" style="margin-top: 20px; padding: 20px; border-radius: 12px; background: #ffffff;">

    <h3 style="font-size: 14px; color: #5A6A85; margin-bottom: 15px;">
        Generate Report by Date Range
    </h3>

    <form method="GET" action="reports.php"
          style="display: flex; gap: 15px; align-items: end; flex-wrap: wrap;">

        <div>
            <label style="display: block; font-size: 13px; margin-bottom: 6px;">
                From Date
            </label>

            <input
                type="date"
                name="from_date"
                value="<?= htmlspecialchars($fromDate); ?>"
                required
                style="padding: 9px 12px; border: 1px solid #D5DCE5; border-radius: 6px;"
            >
        </div>

        <div>
            <label style="display: block; font-size: 13px; margin-bottom: 6px;">
                To Date
            </label>

            <input
                type="date"
                name="to_date"
                value="<?= htmlspecialchars($toDate); ?>"
                required
                style="padding: 9px 12px; border: 1px solid #D5DCE5; border-radius: 6px;"
            >
        </div>

        <button
            type="submit"
            class="btn"
            style="background: #2E7D32; border: none; cursor: pointer;"
        >
            Generate Report
        </button>

        <a
            href="reports.php"
            class="btn"
            style="background: #757575;"
        >
            Clear
        </a>

    </form>

</div>
        </div>
        
        <!-- 🌟 STEP 10E CONNECTED: Exporting Action Panel Buttons Row -->
        <div style="display:flex; gap:12px;">
            <a href="reports.php?action=printSales" target="_blank" class="btn" style="background:#0288D1;">🖨️ Print Monthly Report</a>
            <a href="export.php" class="btn" style="background:#2E7D32;">📥 Export Excel CSV</a>
        </div>
    </div>

    <!-- 📊 TIME-SERIES SUMMARY AGGREGATE VALUES MATRIX -->
    <div class="cards" style="margin-bottom: 30px;">
        <div class="card">
            <h3>Today's Sales Gross</h3>
            <p>₱<?=number_format($todaySales,2)?></p>
        </div>
        <div class="card">
            <h3>Weekly Sales Cycle</h3>
            <p>₱<?=number_format($weeklySales,2)?></p>
        </div>
        <div class="card">
            <h3>Monthly Sales Volume</h3>
            <p>₱<?=number_format($monthlySales,2)?></p>
        </div>
        <div class="card">
            <h3>Expenses Ledger Draw</h3>
            <p style="color:#C62828;">₱<?=number_format($expenses,2)?></p>
        </div>
        <div class="card">
            <h3>Net Profit / Loss</h3>
            <p style="color:<?=($profit>=0?'#1B5E20':'#D32F2F')?>">
                ₱<?=number_format($profit,2)?>
            </p>
        </div>
    </div>

    <!-- 🌟 STEP 10D ADDED: Monthly Structural Financial Summary Card Row -->
    <div class="card" style="margin-bottom: 30px; padding:24px; border-radius:12px; background:#ffffff;">
        <h3 style="font-size: 14px; color: #5A6A85; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom:15px;">
            Current Monthly Consolidated Statements Summary
        </h3>
        <div style="display: flex; gap: 40px; font-size:16px;">
            <p><strong>Gross realized turnover:</strong> <span style="color:#2E7D32; font-weight:700;">₱<?= number_format($summary['sales'], 2); ?></span></p>
            <p><strong>Total expenditures balance:</strong> <span style="color:#C62828; font-weight:700;">₱<?= number_format($summary['expenses'], 2); ?></span></p>
            <p><strong>Net operating residual income:</strong> <span style="color:<?= $summary['profit'] >= 0 ? '#1B5E20;' : '#D32F2F;' ?> font-weight:700;">₱<?= number_format($summary['profit'], 2); ?></span></p>
        </div>
    </div>

    <!-- 📊 TWO-COLUMN GRID: TREND CHART & RECENT TRANSACTIONS LEDGER -->
    <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 24px; align-items: start;">
        
        <!-- LEFT PANEL: Line Trend Chart Canvas -->
        <div class="card" style="padding: 24px; border-radius: 12px; background: #ffffff;">
            <h3 style="font-size: 13px; color: #5A6A85; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">
                Sales Trend Graph (Trailing 7 Operational Dates)
            </h3>
            <div style="width: 100%; position: relative;">
                <canvas id="salesChart" height="130"></canvas>
            </div>
        </div>

        <!-- RIGHT PANEL: Recent Completed Orders Matrix -->
        <div class="card" style="padding: 0; overflow: hidden; border-radius: 12px; background: #ffffff;">
            <h3 style="padding: 20px; font-size: 13px; color: #5A6A85; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid #E5E9F0; margin: 0;">
                Recent Completed Orders
            </h3>
            <table style="width: 100%; margin-top: 0; border-collapse: collapse;">
                <thead>
                    <tr style="background: #F8F9FA;">
                        <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: left; font-size: 12px; color: #5A6A85;">Order</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: left; font-size: 12px; color: #5A6A85;">Customer</th>
                        <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: right; font-size: 12px; color: #5A6A85;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent)) { ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 30px; color: #7A869A; font-style: italic; font-size: 14px;">
                                No completed transaction logs found.
                            </td>
                        </tr>
                    <?php } else { 
                        foreach($recent as $r) { ?>
                        <tr>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; font-size: 14px;">
                                <code style="background: #ECEFF1; padding: 3px 6px; border-radius: 4px; font-weight: 600;">#ORD-<?= $r['order_id']?></code>
                            </td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; font-size: 14px;">
                                <b><?= htmlspecialchars($r['customer_name']); ?></b><br>
                                <small style="color: #7A869A; font-size: 11px;"><?= date('M d, Y', strtotime($r['completed_at']))?></small>
                            </td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: right; font-size: 14px; color: #1B5E20; font-weight: 700;">
                                ₱<?= number_format($r['total_amount'], 2)?>
                            </td>
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>

    </div>

</div>

</div>

<!-- INVENTORY SUMMARY -->
<div class="card" style="margin-top: 24px; padding: 24px; border-radius: 12px; background: #ffffff;">

    <h3 style="font-size: 13px; color: #5A6A85; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">
        Inventory Summary
    </h3>

    <table style="width: 100%; border-collapse: collapse;">

        <thead>
            <tr style="background: #F8F9FA;">
                <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: left;">
                    Product
                </th>

                <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: left;">
                    Unit
                </th>

                <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: right;">
                    Current Stock
                </th>

                <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: right;">
                    Reorder Level
                </th>

                <th style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: center;">
                    Status
                </th>
            </tr>
        </thead>

        <tbody>

            <?php if (empty($inventory)) { ?>

                <tr>
                    <td colspan="5" style="text-align: center; padding: 30px; color: #7A869A;">
                        No inventory records found.
                    </td>
                </tr>

            <?php } else { ?>

                <?php foreach ($inventory as $item) { ?>

                    <?php
                    $stock = (float)$item['current_stock'];
                    $reorder = (float)$item['reorder_level'];

                    $lowStock = $stock <= $reorder;
                    ?>

                    <tr>

                        <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0;">
                            <?= htmlspecialchars($item['product_name']); ?>
                        </td>

                        <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0;">
                            <?= htmlspecialchars($item['unit']); ?>
                        </td>

                        <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: right;">
                            <?= number_format($stock, 2); ?>
                        </td>

                        <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: right;">
                            <?= number_format($reorder, 2); ?>
                        </td>

                        <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: center;">

                            <?php if ($lowStock) { ?>

                                <span style="color: #C62828; font-weight: 600;">
                                    Low Stock
                                </span>

                            <?php } else { ?>

                                <span style="color: #2E7D32; font-weight: 600;">
                                    In Stock
                                </span>

                            <?php } ?>

                        </td>

                    </tr>

                <?php } ?>

            <?php } ?>

        </tbody>

    </table>

</div>
<?php if ($dateRange !== null) { ?>

<div class="card" style="margin-bottom: 30px; padding: 24px; border-radius: 12px; background: #ffffff;">

    <h3 style="font-size: 14px; color: #5A6A85; margin-bottom: 15px;">
        Date Range Report
    </h3>

    <p style="color: #7A869A; margin-bottom: 20px;">
        <?= htmlspecialchars($fromDate); ?>
        to
        <?= htmlspecialchars($toDate); ?>
    </p>

    <div class="cards">

        <div class="card">
            <h3>Sales</h3>
            <p>
                ₱<?= number_format($dateRange['sales'], 2); ?>
            </p>
        </div>

        <div class="card">
            <h3>Expenses</h3>
            <p>
                ₱<?= number_format($dateRange['expenses'], 2); ?>
            </p>
        </div>

        <div class="card">
            <h3>Profit / Loss</h3>
            <p style="color: <?= $dateRange['profit'] >= 0 ? '#1B5E20' : '#D32F2F'; ?>;">
                ₱<?= number_format($dateRange['profit'], 2); ?>
            </p>
        </div>

    </div>

</div>

<?php } ?>

<!-- ChartJS Script Canvas Wiring Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById("salesChart").getContext("2d");
    const trendData = <?= json_encode($trend); ?>;
    
    const labels = trendData.map(item => {
        const dateObj = new Date(item.sale_date);
        return dateObj.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    });
    const values = trendData.map(item => Number(item.total));

    new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [{
                label: "Turnover",
                data: values,
                borderColor: "#1B5E20",
                backgroundColor: "rgba(27, 94, 32, 0.04)",
                fill: true,
                tension: 0.3,
                borderWidth: 3,
                pointBackgroundColor: "#1B5E20",
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return '₱' + value.toLocaleString(); }, color: "#5A6A85", font: { size: 11 } },
                    grid: { color: "rgba(0, 0, 0, 0.04)" }
                },
                x: { ticks: { color: "#5A6A85", font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
});
</script>

<?php include 'app/views/layouts/footer.php';?>
