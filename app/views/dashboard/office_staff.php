<?php
require_once 'config/database.php';

$pendingOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE status = 'Pending'
")->fetchColumn();

$processingOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE status = 'Processing'
")->fetchColumn();

$completedOrders = $pdo->query("
    SELECT COUNT(*)
    FROM orders
    WHERE status = 'Completed'
")->fetchColumn();

$activeProducts = $pdo->query("
    SELECT COUNT(*)
    FROM products
    WHERE status = 'Active'
")->fetchColumn();

include 'app/views/layouts/header.php';
?>

<div class="container">

    <?php include 'app/views/layouts/sidebar.php'; ?>

    
<div class="content">

    <div class="dashboard-heading">
        <div>
            <h1>Office Staff Dashboard</h1>
            <p>Manage customer orders and inventory.</p>
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

    <a href="orders.php" class="card dashboard-card">
        <h3>Pending Orders</h3>
        <p><?= $pendingOrders; ?></p>
        <span>View pending orders →</span>
    </a>

    <a href="orders.php" class="card dashboard-card">
        <h3>Processing Orders</h3>
        <p><?= $processingOrders; ?></p>
        <span>Manage orders →</span>
    </a>

    <a href="orders.php" class="card dashboard-card">
        <h3>Completed Orders</h3>
        <p><?= $completedOrders; ?></p>
        <span>View completed orders →</span>
    </a>

    <a href="products.php" class="card dashboard-card">
        <h3>Active Products</h3>
        <p><?= $activeProducts; ?></p>
        <span>View products →</span>
    </a>

</div>
    </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>