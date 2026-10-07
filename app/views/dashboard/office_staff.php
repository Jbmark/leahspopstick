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

        <h1>Office Staff Dashboard</h1>

        <p>Manage customer orders and inventory.</p>

        <div class="cards">

            <div class="card">
                <h3>Pending Orders</h3>
                <p><?= $pendingOrders; ?></p>
            </div>

            <div class="card">
                <h3>Processing Orders</h3>
                <p><?= $processingOrders; ?></p>
            </div>

            <div class="card">
                <h3>Completed Orders</h3>
                <p><?= $completedOrders; ?></p>
            </div>

            <div class="card">
                <h3>Active Products</h3>
                <p><?= $activeProducts; ?></p>
            </div>

        </div>

    </div>

</div>

<?php include 'app/views/layouts/footer.php'; ?>