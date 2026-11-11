
<div class="sidebar" id="adminSidebar">

    <!-- BUSINESS BRANDING -->
    <a href="index.php" class="nav-brand">
        <img
           src="public/shop/images/leah-logo.png"
            alt="Leah Wooden Products"
            class="brand-logo"
        >
        <span class="brand-name">Leah Wooden Products</span>
    </a>

    <!-- USER ROLE -->
    <span class="role-tag">
        <?= htmlspecialchars(
            $_SESSION['user']['role_name'] ?? '',
            ENT_QUOTES,
            'UTF-8'
        ); ?>
    </span>

    <hr>

    <nav class="sidebar-nav">

        <?php
        $role = $_SESSION['user']['role_name'] ?? '';
        ?>

        <!-- OWNER -->
        <?php if ($role === "Owner") { ?>

            <a href="index.php">Dashboard</a>
            <a href="orders.php">Orders</a>
            <a href="customers.php">Customers</a>
            <a href="products.php">Product List</a>
            <a href="inventory.php">Inventory Batches</a>
            <a href="raw_materials.php">Raw Materials</a>
            <a href="expenses.php">Expenses Records</a>
            <a href="reports.php">Reports</a>
            <a href="users.php">Users</a>

        <?php } ?>

        <!-- BOOKKEEPER -->
        <?php if ($role === "Bookkeeper") { ?>

            <a href="index.php">Dashboard</a>
            <a href="expenses.php">Expenses Ledger</a>
            <a href="reports.php">Reports</a>

        <?php } ?>

        <!-- OFFICE STAFF -->
        <?php if ($role === "Office Staff") { ?>

            <a href="index.php">Dashboard</a>
            <a href="orders.php">Orders</a>
            <a href="customers.php">Customers</a>
            <a href="products.php">Product List</a>
            <a href="inventory.php">Inventory Batches</a>
            <a href="raw_materials.php">Raw Materials</a>

        <?php } ?>

    </nav>

    <hr class="sidebar-bottom-divider">

    <a href="logout.php" class="logout-btn">Logout</a>

</div>

<!-- Mobile menu overlay: only one instance -->
<button
    type="button"
    class="sidebar-overlay"
    id="sidebarOverlay"
    aria-label="Close navigation menu"
></button>
