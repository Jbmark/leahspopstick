<div class="sidebar">
    <h2>Leah's Popstick</h2>
    <span class="role-tag"><?= $_SESSION['user']['role_name']; ?></span>
    
    <hr>
    
    <?php 
    $role = $_SESSION['user']['role_name']; 
    
    if($role == "Owner"){ 
    ?>
        <a href="index.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="products.php">Product List</a>
        <a href="inventory.php">Inventory Batches</a>
        <a href="#">Expenses</a>
        <a href="reports.php">Reports</a> <!-- 🌟 FIXED: Points to reports.php -->
        <a href="#">Users</a>
    <?php } ?>

    <?php if($role == "Bookkeeper"){ ?>
        <a href="index.php">Dashboard</a>
        <a href="#">Expenses</a>
        <a href="reports.php">Reports</a> <!-- 🌟 FIXED: Points to reports.php -->
    <?php } ?>
    
    <?php if($role == "Office Staff"){ ?>
        <a href="index.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="products.php">Product List</a>
        <a href="inventory.php">Inventory Batches</a>
    <?php } ?>
    
    <hr>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>
