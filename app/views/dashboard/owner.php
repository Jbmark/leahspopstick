<?php 
require_once 'config/database.php';

$totalProducts = $pdo->query("
    SELECT COUNT(*) 
    FROM products 
    WHERE status = 'Active'
")->fetchColumn();

$totalOrders = $pdo->query("
    SELECT COUNT(*) 
    FROM orders
")->fetchColumn();

$totalSales = $pdo->query("
    SELECT COALESCE(SUM(total_amount), 0)
    FROM orders
    WHERE status = 'Completed'
")->fetchColumn();

$totalExpenses = $pdo->query("
    SELECT COALESCE(SUM(amount), 0)
    FROM expenses
")->fetchColumn();

include 'app/views/layouts/header.php'; 
?>

<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>

    <div class="content">

    <div class="dashboard-heading">
        <div>
            <h1>Owner Dashboard</h1>

            <p>
                Welcome, <?= htmlspecialchars($_SESSION['user']['full_name']); ?>
            </p>
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

    <a href="products.php" class="card dashboard-card">
        <h3>Active Products</h3>
        <p><?= $totalProducts; ?></p>
        <span>View products →</span>
    </a>

    <a href="orders.php" class="card dashboard-card">
        <h3>Total Orders</h3>
        <p><?= $totalOrders; ?></p>
        <span>Manage orders →</span>
    </a>

    <a href="reports.php" class="card dashboard-card">
        <h3>Sales Revenue</h3>
        <p>₱<?= number_format($totalSales, 2); ?></p>
        <span>View reports →</span>
    </a>

    <a href="expenses.php" class="card dashboard-card">
        <h3>Total Expenses</h3>
        <p>₱<?= number_format($totalExpenses, 2); ?></p>
        <span>View expenses →</span>
    </a>

</div>
    </div>
</div>

<?php include 'app/views/layouts/footer.php'; ?>