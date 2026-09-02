<?php 
require_once 'config/database.php';

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE status='Active'")->fetchColumn();
$totalOrders   = 0; // Set to 0 temporarily until we build the orders table!
$totalExpenses = 0; // Set to 0 temporarily until we build the expenses table!

include 'app/views/layouts/header.php'; 
?>
<div class="container">
    <?php include 'app/views/layouts/sidebar.php'; ?>
    <div class="content">
        <h1>Owner Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['user']['full_name']); ?></p>
        
        <div class="cards">
            <div class="card">
                <h3>Active Products</h3>
                <p><?= $totalProducts; ?></p>
            </div>
            <div class="card">
                <h3>Total Orders</h3>
                <p><?= $totalOrders; ?></p>
            </div>
            <div class="card">
                <h3>Sales Revenue</h3>
                <p>₱0.00</p>
            </div>
            <div class="card">
                <h3>Total Expenses</h3>
                <p>₱<?= number_format($totalExpenses, 2); ?></p>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/layouts/footer.php'; ?>
