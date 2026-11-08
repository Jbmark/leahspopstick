<?php
require_once 'config/database.php';

$totalExpenses = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM expenses
")->fetchColumn();

$totalSales = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM orders
    WHERE status = 'Completed'
")->fetchColumn();

$completedOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE status = 'Completed'
")->fetchColumn();

$pendingOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE status = 'Pending'
")->fetchColumn();

include 'app/views/layouts/header.php';
?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    
<div class="content">

    <div class="dashboard-heading">
        <div>
            <h1>Bookkeeper Dashboard</h1>
            <p>Manage expenses and reports.</p>
        </div>

        <a
            href="/public/shop/index.php"
            class="visit-shop-btn dashboard-visit-shop"
            target="_blank"
            rel="noopener noreferrer"
        >
            <span aria-hidden="true">🛍</span>
            <span>Visit Shop</span>
        </a>
    </div>

        
<div class="cards">

    <a href="reports.php" class="card dashboard-card">
        <h3>Total Sales</h3>
        <p>₱<?= number_format($totalSales, 2); ?></p>
        <span>View financial reports →</span>
    </a>

    <a href="expenses.php" class="card dashboard-card">
        <h3>Total Expenses</h3>
        <p>₱<?= number_format($totalExpenses, 2); ?></p>
        <span>View expenses ledger →</span>
    </a>

    <a href="reports.php" class="card dashboard-card">
        <h3>Completed Orders</h3>
        <p><?= $completedOrders; ?></p>
        <span>View reports →</span>
    </a>

    <a href="reports.php" class="card dashboard-card">
        <h3>Pending Orders</h3>
        <p><?= $pendingOrders; ?></p>
        <span>View reports →</span>
    </a>

</div>

    </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>