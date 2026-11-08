
<?php
session_start();

require_once __DIR__ . '/../../config/database.php';

$error = '';
$email = '';

if (isset($_SESSION['customer'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter your email and password.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT customer_id, customer_name, contact_number,
                        email, address, password
                 FROM customers
                 WHERE LOWER(email) = ?
                 LIMIT 1"
            );

            $stmt->execute([$email]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);

            if (
                $customer &&
                !empty($customer['password']) &&
                password_verify($password, $customer['password'])
            ) {
                session_regenerate_id(true);

                unset($customer['password']);
                $_SESSION['customer'] = $customer;

                header('Location: index.php');
                exit;
            }

            $error = 'Invalid email or password. If you have not created a password yet, register first.';
        } catch (PDOException $e) {
            $error = 'Login is temporarily unavailable. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login | Leah's Popstick</title>
    <link rel="stylesheet" href="../css/shop.css">

    <style>

/* CUSTOMER AUTHENTICATION PAGES */

body {
    background: #F5F7F2;
    color: #333333;
}

.auth-container {
    box-sizing: border-box;
    width: calc(100% - 32px);
    max-width: 440px;
    margin: 45px auto;
    padding: 32px;
    background: #FFFFFF;
    border: 1px solid #E5E9F0;
    border-radius: 14px;
    box-shadow: 0 6px 24px rgba(27, 94, 32, 0.08);
}

.auth-container h1 {
    margin: 0 0 10px;
    color: #1B5E20;
    text-align: center;
    font-size: 28px;
}

.auth-container > p {
    margin: 0 0 24px;
    color: #666666;
    text-align: center;
    line-height: 1.6;
}

.auth-form label {
    display: block;
    margin: 16px 0 7px;
    color: #333333;
    font-size: 14px;
    font-weight: 600;
}

.auth-form input {
    box-sizing: border-box;
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #D5DCCF;
    border-radius: 8px;
    background: #FFFFFF;
    color: #333333;
    font: inherit;
    font-size: 14px;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.auth-form input:focus {
    outline: none;
    border-color: #6B8E3B;
    box-shadow: 0 0 0 3px rgba(107, 142, 59, 0.15);
}

.auth-form button {
    width: 100%;
    margin-top: 24px;
    padding: 13px 16px;
    border: none;
    border-radius: 8px;
    background: #1B5E20;
    color: #FFFFFF;
    cursor: pointer;
    font: inherit;
    font-weight: 600;
    transition: background 0.2s, transform 0.2s;
}

.auth-form button:hover {
    background: #154A19;
}

.auth-form button:active {
    transform: scale(0.99);
}

.auth-error {
    padding: 13px 15px;
    margin: 16px 0;
    border: 1px solid #F1BFC3;
    border-radius: 8px;
    background: #FCEBEC;
    color: #842029;
    font-size: 14px;
    line-height: 1.5;
}

.auth-links {
    margin-top: 24px;
    color: #666666;
    text-align: center;
    font-size: 14px;
    line-height: 1.8;
}

.auth-links a {
    color: #1B5E20;
    font-weight: 600;
    text-decoration: none;
}

.auth-links a:hover {
    color: #6B8E3B;
    text-decoration: underline;
}

@media (max-width: 480px) {
    .auth-container {
        margin: 24px auto;
        padding: 22px 18px;
    }

    .auth-container h1 {
        font-size: 24px;
    }
}

    </style>
</head>
<body>

<div class="auth-container">
    <h1>Customer Login</h1>
    <p>Sign in to your customer account.</p>

    <?php if ($error !== ''): ?>
        <div class="auth-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="login.php">
        <label for="email">Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            autocomplete="email"
            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Log In</button>
    </form>

    <div class="auth-links">
        Don't have an account?
        <a href="register.php">Register here</a>
        <br><br>
        <a href="index.php">Back to Products</a>
    </div>
</div>

</body>
</html>
