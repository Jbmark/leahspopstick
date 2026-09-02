<?php include 'app/views/layouts/header.php';?>

<div class="container">

<?php include 'app/views/layouts/sidebar.php';?>

<div class="content">

    <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1>Financial Performance Dashboard</h1>
            <p style="color: #7A869A; margin: 0;">Real-time calculation metrics panel optimized for auditing business health benchmarks.</p>
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

<!-- ChartJS Script Canvas Wiring Engine -->
<script src="https://jsdelivr.net"></script>
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
