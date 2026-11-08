<?php include 'app/views/layouts/header.php'; ?>

<div class="login-body-wrapper">
    <div class="login-container">
        <a href="index.php" class="login-brand">
    <img
        src="/public/shop/images/leah-logo.png"
        alt="Leah Wooden Products logo"
        class="login-brand-logo"
    >
</a>

<p>Sign in to manage operations</p>
        
        <?php 
        if (isset($_SESSION['error'])) {
            echo '<div class="login-error">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <form action="index.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Enter username">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>
</div>

</body>
</html>
