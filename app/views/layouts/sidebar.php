    <?php 
    $role = $_SESSION['user']['role_name']; 
    
   
    if($role == "Owner"){ 
    ?>
        <a href="index.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="products.php">Product List</a>
        <a href="inventory.php">Inventory Batches</a>
        <a href="expenses.php">Expenses Ledger</a> <!-- 🌟 LINK FIXED -->
        <a href="reports.php">Reports</a> 
        <a href="#">Users</a>
    <?php } ?>
    
   
    <?php if($role == "Bookkeeper"){ ?>
        <a href="index.php">Dashboard</a>
        <a href="expenses.php">Expenses Ledger</a> <!-- 🌟 LINK FIXED -->
        <a href="reports.php">Reports</a> 
    <?php } ?>
    
   
    <?php if($role == "Office Staff"){ ?>
        <a href="index.php">Dashboard</a>
        <a href="orders.php">Orders</a>
        <a href="products.php">Product List</a>
        <a href="inventory.php">Inventory Batches</a>
    <?php } ?>
