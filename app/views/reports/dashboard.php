<?php include 'app/views/layouts/header.php';?>

<div class="container reports-dashboard">

<?php include 'app/views/layouts/sidebar.php';?>

<div class="content">

    
<div class="reports-tools-row">

    <div class="reports-tools-main">
        <h1>Financial Performance Dashboard</h1>


        <!-- REPORT PERIOD FILTER -->
        <div class="card" style="margin-top: 20px; padding: 20px; border-radius: 12px; background: #ffffff;">

            <h3 style="font-size: 14px; color: #5A6A85; margin-bottom: 15px;">
                Generate Financial Report
            </h3>

           <form method="GET" action="reports.php" class="reports-filter-form">

                <div style="min-width: 180px;">
                    <label for="period" style="display: block; font-size: 13px; margin-bottom: 6px;">
                        Report Period
                    </label>

                    <select id="period" name="period"
                            onchange="toggleCustomDates()"
                            style="padding: 9px 12px; border: 1px solid #D5DCE5; border-radius: 6px; width: 100%;">

                        <option value="daily" <?= $period === 'daily' ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?= $period === 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                        <option value="monthly" <?= $period === 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                        <option value="yearly" <?= $period === 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                        <option value="custom" <?= $period === 'custom' ? 'selected' : ''; ?>>Custom Date Range</option>

                    </select>
                </div>

                <div id="customDateFields"
                     style="display: <?= $period === 'custom' ? 'flex' : 'none'; ?>; gap: 15px; flex-wrap: wrap;">

                    <div>
                        <label for="from_date" style="display: block; font-size: 13px; margin-bottom: 6px;">
                            From Date
                        </label>

                        <input type="date"
                               id="from_date"
                               name="from_date"
                               value="<?= htmlspecialchars($fromDate ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                               <?= $period === 'custom' ? 'required' : ''; ?>
                               style="padding: 9px 12px; border: 1px solid #D5DCE5; border-radius: 6px;">
                    </div>

                    <div>
                        <label for="to_date" style="display: block; font-size: 13px; margin-bottom: 6px;">
                            To Date
                        </label>

                        <input type="date"
                               id="to_date"
                               name="to_date"
                               value="<?= htmlspecialchars($toDate ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                               <?= $period === 'custom' ? 'required' : ''; ?>
                               style="padding: 9px 12px; border: 1px solid #D5DCE5; border-radius: 6px;">
                    </div>

                </div>

                <button type="submit" class="btn"
                        style="background: #2E7D32; border: none; cursor: pointer;">
                    Generate Report
                </button>

                <a href="reports.php?clear=1" class="btn"
                   style="background: #757575;">
                    Clear
                </a>

            </form>

            <?php if (!empty($dateError)) { ?>
                <p style="color: #c62828; margin-top: 12px;">
                    <?= htmlspecialchars($dateError, ENT_QUOTES, 'UTF-8'); ?>
                </p>
            <?php } ?>

        </div>

        <!-- SELECTED PERIOD FINANCIAL REPORT -->
        <?php if ($dateRange !== null) { ?>

            <div class="card" style="margin-top: 20px; margin-bottom: 30px; padding: 24px; border-radius: 12px; background: #ffffff;">

                <h3 style="font-size: 14px; color: #5A6A85; margin-bottom: 15px;">
                    <?= htmlspecialchars($periodLabel, ENT_QUOTES, 'UTF-8'); ?> Financial Report
                </h3>

                <p style="color: #7A869A; margin-bottom: 20px;">
                    <?= htmlspecialchars($fromDate, ENT_QUOTES, 'UTF-8'); ?>
                    to
                    <?= htmlspecialchars($toDate, ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <div class="cards">
                    <div class="card">
                        <h3>Total Gross Sales</h3>
                        <p>₱<?= number_format((float) $dateRange['sales'], 2); ?></p>
                    </div>

                    <div class="card">
                        <h3>Total Recorded Expenses</h3>
                        <p style="color: #C62828;">
                            ₱<?= number_format((float) $dateRange['expenses'], 2); ?>
                        </p>
                    </div>

                    <div class="card">
                        <h3>Sales Less Recorded Expenses</h3>
                        <p style="color: <?= $dateRange['profit'] >= 0 ? '#1B5E20' : '#D32F2F'; ?>;">
                            ₱<?= number_format((float) $dateRange['profit'], 2); ?>
                        </p>
                    </div>
                </div>

            </div>

        <?php } ?>

    </div>

    <!-- PRINT AND EXPORT BUTTONS -->
    <div class="reports-action-buttons">
        <a href="reports.php?action=printSales" target="_blank" class="btn" style="background:#0288D1;">
            🖨️ Print Monthly Report
        </a>

        <a href="export.php" class="btn" style="background:#2E7D32;">
            📥 Export Excel CSV
        </a>
    </div>

</div>


    <!-- 📊 TIME-SERIES SUMMARY AGGREGATE VALUES MATRIX -->
    <div class="cards quick-overview-grid" style="margin-bottom: 30px;">
        <div class="card">
            <h3>Today's Sales</h3>
            <p>₱<?=number_format($todaySales,2)?></p>
        </div>
        <div class="card">
            <h3>This Week's Sales</h3>
            <p>₱<?=number_format($weeklySales,2)?></p>
        </div>
        <div class="card">
            <h3>This Month's Sales</h3>
            <p>₱<?=number_format($monthlySales,2)?></p>
        </div>
        <div class="card">
            <h3>Recorded Expenses</h3>
            <p style="color:#C62828;">₱<?=number_format($expenses,2)?></p>
        </div>
        <div class="card">
            <h3>Sales Less Recorded Expenses</h3>
            <p style="color:<?=($profit>=0?'#1B5E20':'#D32F2F')?>">
                ₱<?=number_format($profit,2)?>
            </p>
        </div>
    </div>
    
 
    

    <!-- 📊 TWO-COLUMN GRID: TREND CHART & RECENT TRANSACTIONS LEDGER -->
    <div class="reports-performance-grid">
        
        <!-- LEFT PANEL: Line Trend Chart Canvas -->
        <div class="card reports-chart-card" style="padding: 24px; border-radius: 12px; background: #ffffff;">
            <h3 style="font-size: 13px; color: #5A6A85; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px;">
                Sales Trend Graph (Trailing 7 Operational Dates)
            </h3>
            <div style="width: 100%; position: relative;">
                <canvas id="salesChart" height="130"></canvas>
            </div>
        </div>

        <!-- BEST-SELLING PRODUCTS -->
<div class="card" style="margin-bottom: 30px; padding: 24px; border-radius: 12px; background: #ffffff;">

    <div style="margin-bottom: 20px;">
        <h3 style="font-size: 13px; color: #5A6A85; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">
            Top-Selling Products
        </h3>

        <p style="font-size: 13px; color: #7A869A; margin: 0;">
            Products ranked by quantity sold during the selected report period
        </p>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">

            <thead>
                <tr style="background: #F8F9FA;">
                    <th style="padding: 12px 16px; text-align: left; border-bottom: 1px solid #E5E9F0;">Product</th>
                    <th style="padding: 12px 16px; text-align: left; border-bottom: 1px solid #E5E9F0;">Unit</th>
                    <th style="padding: 12px 16px; text-align: right; border-bottom: 1px solid #E5E9F0;">Quantity Sold</th>
                    <th style="padding: 12px 16px; text-align: right; border-bottom: 1px solid #E5E9F0;">Total Sales</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($bestSellingProducts)) { ?>

                    <tr>
                        <td colspan="4" style="text-align: center; padding: 30px; color: #7A869A;">
                            No completed product sales found for this period.
                        </td>
                    </tr>

                <?php } else { ?>

                    <?php foreach ($bestSellingProducts as $product) { ?>

                        <tr>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0;">
                                <b><?= htmlspecialchars($product['product_name']); ?></b>
                            </td>

                            <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0;">
                                <?= htmlspecialchars($product['unit']); ?>
                            </td>

                            <td style="padding: 12px 16px; text-align: right; border-bottom: 1px solid #E5E9F0;">
                                <?= number_format((int) $product['total_quantity_sold']); ?>
                            </td>

                            <td style="padding: 12px 16px; text-align: right; border-bottom: 1px solid #E5E9F0; color: #1B5E20; font-weight: 600;">
                                ₱<?= number_format((float) $product['total_sales'], 2); ?>
                            </td>
                        </tr>

                    <?php } ?>

                <?php } ?>

            </tbody>

        </table>
    </div>

</div>
    </div>


    <!-- ORDERS GRID -->
    <div class="reports-orders-grid">

            <!-- ORDER STATUS SUMMARY -->
            <div class="card" style="margin-bottom: 30px; padding: 24px; border-radius: 12px; background: #ffffff;">

                <div style="margin-bottom: 20px;">
                    <h3 style="font-size: 13px; color: #5A6A85; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;">
                        Orders Overview
                    </h3>

                    <p style="font-size: 13px; color: #7A869A; margin: 0;">
                        Overview of orders for the selected report period
                    </p>
                </div>

                <div class="cards order-status-grid">

                    <div class="card">
                        <h3>Total Orders</h3>
                        <p><?= number_format((int) $orderStatusSummary['total_orders']); ?></p>
                    </div>

                    <div class="card">
                        <h3>Pending</h3>
                        <p style="color: #F9A825;">
                            <?= number_format((int) $orderStatusSummary['pending_orders']); ?>
                        </p>
                    </div>

                    <div class="card">
                        <h3>Processing</h3>
                        <p style="color: #0288D1;">
                            <?= number_format((int) $orderStatusSummary['processing_orders']); ?>
                        </p>
                    </div>

                    <div class="card">
                        <h3>Completed</h3>
                        <p style="color: #2E7D32;">
                            <?= number_format((int) $orderStatusSummary['completed_orders']); ?>
                        </p>
                    </div>

                    <div class="card">
                        <h3>Cancelled</h3>
                        <p style="color: #C62828;">
                            <?= number_format((int) $orderStatusSummary['cancelled_orders']); ?>
                        </p>
                    </div>

                </div>

            </div>

          <div class="card reports-orders-card" style="padding: 0; overflow: hidden; border-radius: 12px; background: #ffffff;">
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
    

    <!-- EXPENSE MONITORING -->
    <div class="card" style="margin-bottom: 30px; padding: 24px; border-radius: 12px; background: #ffffff;">

        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
            <div>
                <h3 class="reports-section-title">
    Expenses for Selected Period
</h3>

<p class="reports-section-description">
    Detailed list of expenses recorded within the selected date range
</p>
            </div>

            <strong style="color: #C62828; font-size: 16px;">
                Total: ₱<?= number_format((float) array_sum(array_column($periodExpenses, 'amount')), 2); ?>
            </strong>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">

                <thead>
                    <tr style="background: #F8F9FA;">
                        <th>Date</th>
                        <th>Expense</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($periodExpenses)) { ?>

                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px; color: #7A869A;">
                                No expense records found for this period.
                            </td>
                        </tr>

                    <?php } else { ?>

                        <?php foreach ($periodExpenses as $expense) { ?>
                            <tr>
                                <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0;">
                                    <?= date('M d, Y', strtotime($expense['expense_date'])); ?>
                                </td>

                                <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0;">
                                    <b><?= htmlspecialchars($expense['expense_name']); ?></b>
                                </td>
                                
                                <td><?= htmlspecialchars($expense['category']) ?></td>

                                <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; color: #7A869A;">
                                    <?= htmlspecialchars($expense['description'] ?? 'No description'); ?>
                                </td>

                                <td style="padding: 12px 16px; border-bottom: 1px solid #E5E9F0; text-align: right; color: #C62828; font-weight: 600;">
                                    ₱<?= number_format((float) $expense['amount'], 2); ?>
                                </td>
                            </tr>
                        <?php } ?>

                    <?php } ?>
                </tbody>

            </table>
        </div>

    </div>



<?php
$totalActiveProducts = count($inventory);
$totalStock = 0;
$inStockProducts = 0;
$lowStockProducts = 0;
$outOfStockProducts = 0;

foreach ($inventory as $item) {

    $stock = (float) $item['current_stock'];
    $reorder = (float) $item['reorder_level'];

    $totalStock += $stock;

    if ($stock <= 0) {
        $outOfStockProducts++;
    } elseif ($stock <= $reorder) {
        $lowStockProducts++;
    } else {
        $inStockProducts++;
    }
}
?>


<!-- INVENTORY SUMMARY -->
<div class="card" style="margin-top: 24px; padding: 24px; border-radius: 12px; background: #ffffff;">

   <div style="margin-bottom: 20px;">
    <h3 class="reports-section-title">
        Inventory Overview
    </h3>

    <p class="reports-section-description">
        Current stock levels and product availability
    </p>
</div>
    
    <div class="cards inventory-kpi-grid">
    <div class="card">
        <h4>Active Products</h4>
        <p><?= number_format($totalActiveProducts); ?></p>
    </div>

    <div class="card">
        <h4>Total Stock (Boxes)</h4>
        <p><?= number_format($totalStock); ?></p>
    </div>

    <div class="card">
        <h4>In Stock</h4>
        <p><?= number_format($inStockProducts); ?></p>
    </div>

    <div class="card">
        <h4>Low Stock</h4>
        <p><?= number_format($lowStockProducts); ?></p>
    </div>

    <div class="card">
        <h4>Out of Stock</h4>
        <p><?= number_format($outOfStockProducts); ?></p>
    </div>
</div>

    <table class="reports-data-table" style="width: 100%; border-collapse: collapse;">

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

if ($stock <= 0) {
    $stockStatus = 'Out of Stock';
    $stockColor = '#C62828';
} elseif ($stock <= $reorder) {
    $stockStatus = 'Low Stock';
    $stockColor = '#F9A825';
} else {
    $stockStatus = 'In Stock';
    $stockColor = '#2E7D32';
}
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

                            <span style="color: <?= $stockColor; ?>; font-weight: 600;">
    <?= $stockStatus; ?>
</span>

                        </td>

                    </tr>

                <?php } ?>

            <?php } ?>

        </tbody>

    </table>

</div>
    
    </div>
</div>


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
                    ticks: { callback: function(value) {
    return '₱' + Number(value).toLocaleString('en-PH', {
        maximumFractionDigits: 0
    });
}, color: "#5A6A85", font: { size: 11 } },
                    grid: { color: "rgba(0, 0, 0, 0.04)" }
                },
                x: { ticks: { color: "#5A6A85", font: { size: 11 } }, grid: { display: false } }
            }
        }
    });
});
</script>

<script>
function toggleCustomDates() {
    const period = document.getElementById('period').value;
    const customFields = document.getElementById('customDateFields');
    const fromDate = document.getElementById('from_date');
    const toDate = document.getElementById('to_date');

    const isCustom = period === 'custom';

    customFields.style.display = isCustom ? 'flex' : 'none';

    fromDate.required = isCustom;
    toDate.required = isCustom;
}
</script>

<?php include 'app/views/layouts/footer.php';?>
