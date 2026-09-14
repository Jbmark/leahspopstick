<div class="sidebar" style="display: flex; flex-direction: column; align-items: stretch; text-align: left; background: #ffffff; width: 260px; min-width: 260px; border-right: 1px solid #E5E9F0; padding: 24px; min-height: 100vh;">
    <h2 style="font-size: 22px; color: #1B5E20; font-weight: 700; margin-bottom: 4px; letter-spacing: -0.5px; text-align: left;">Leah's Popstick</h2>
    <!-- Clean badge for user role designation -->
    <span class="role-tag" style="font-size: 12px; color: #2E7D32; background: #E8F5E9; padding: 4px 8px; border-radius: 4px; display: inline-block; align-self: flex-start; font-weight: 600; margin-bottom: 24px; text-transform: capitalize; text-align: left; width: max-content;"><?= htmlspecialchars($_SESSION['user']['role_name']); ?></span>
    
    <hr style="border: 0; border-top: 1px solid #E5E9F0; margin: 15px 0; width: 100%;">
    
    <!-- Link Navigation Container Layout Block forced vertically -->
    <div style="display: flex; flex-direction: column; gap: 4px; width: 100%; text-align: left; align-items: stretch;">
        <?php 
        $role = $_SESSION['user']['role_name']; 
        
     
        if($role == "Owner"){ 
        ?>
            <a href="index.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Dashboard</a>
            <a href="orders.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Orders</a>
            <a href="products.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Product List</a>
            <a href="inventory.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Inventory Batches</a>
            <a href="expenses.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Expenses Ledger</a> 
            <a href="reports.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Reports</a> 
            <a href="#" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Users</a>
        <?php } ?>
        
     
        <?php if($role == "Bookkeeper"){ ?>
            <a href="index.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Dashboard</a>
            <a href="expenses.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Expenses Ledger</a> 
            <a href="reports.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Reports</a> 
        <?php } ?>
        
        
        <?php if($role == "Office Staff"){ ?>
            <a href="index.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Dashboard</a>
            <a href="orders.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Orders</a>
            <a href="products.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Product List</a>
            <a href="inventory.php" style="display: block; color: #5A6A85; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 500; border-radius: 8px; text-align: left;">Inventory Batches</a>
        <?php } ?>
    </div>
    
    <hr style="border: 0; border-top: 1px solid #E5E9F0; margin: auto 0 15px 0; width: 100%;">
    <a href="logout.php" class="logout-btn" style="display: block; color: #D32F2F; padding: 12px 16px; text-decoration: none; font-size: 15px; font-weight: 600; border-radius: 8px; text-align: left;">Logout</a>
</div>
