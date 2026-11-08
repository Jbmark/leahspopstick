
<?php
session_start();

require_once __DIR__ . '/../../config/database.php';

$error = '';
$success = '';

$customerName = '';
$email = '';
$contactNumber = '';
$address = '';

if (isset($_SESSION['customer'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (
        $customerName === '' ||
        $email === '' ||
        $contactNumber === '' ||
        $address === '' ||
        $password === '' ||
        $confirmPassword === ''
    ) {
        $error = 'Please complete all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        
try {
    $check = $pdo->prepare(
        'SELECT customer_id, customer_name, contact_number, password
         FROM customers
         WHERE LOWER(email) = ?
         LIMIT 1'
    );
    $check->execute([$email]);
    $existingCustomer = $check->fetch(PDO::FETCH_ASSOC);

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    if ($existingCustomer) {
        if (!empty($existingCustomer['password'])) {
            $error = 'This email already has an account. Please log in.';
        } elseif (
            trim((string) $existingCustomer['contact_number']) !== $contactNumber
        ) {
            $error = 'This email belongs to an existing customer record. Please check the contact number.';
        } else {
            $stmt = $pdo->prepare(
                'UPDATE customers
                 SET password = ?
                 WHERE customer_id = ? AND password IS NULL'
            );

            $stmt->execute([
                $hashedPassword,
                $existingCustomer['customer_id']
            ]);

            if ($stmt->rowCount() === 1) {
                $success = 'Account setup successful! You can now log in.';
                $customerName = '';
                $email = '';
                $contactNumber = '';
                $address = '';
            } else {
                $error = 'Account setup could not be completed. Please try again.';
            }
        }
    } else {
        $stmt = $pdo->prepare(
            'INSERT INTO customers
             (customer_name, contact_number, email, address, password)
             VALUES (?, ?, ?, ?, ?)'
        );

        $stmt->execute([
            $customerName,
            $contactNumber,
            $email,
            $address,
            $hashedPassword
        ]);

        $success = 'Registration successful! You can now log in.';
        $customerName = '';
        $email = '';
        $contactNumber = '';
        $address = '';
    }
} catch (PDOException $e) {
    $error = 'Registration could not be completed. Please try again.';
}
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration | Leah's Popstick</title>
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
    max-width: 520px;
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

.auth-form input,
.auth-form textarea {
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

.auth-form input:focus,
.auth-form textarea:focus {
    outline: none;
    border-color: #6B8E3B;
    box-shadow: 0 0 0 3px rgba(107, 142, 59, 0.15);
}

.auth-form textarea {
    resize: vertical;
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

.auth-message {
    padding: 13px 15px;
    margin: 16px 0;
    border-radius: 8px;
    font-size: 14px;
    line-height: 1.5;
}

.auth-error {
    border: 1px solid #F1BFC3;
    background: #FCEBEC;
    color: #842029;
}

.auth-success {
    border: 1px solid #B7DFC8;
    background: #EAF6EE;
    color: #155724;
}

.auth-message a {
    color: #1B5E20;
    font-weight: 600;
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
    <h1>Create Customer Account</h1>
    <p>Register to make ordering easier.</p>

    <?php if ($error !== ''): ?>
        <div class="auth-message auth-error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="auth-message auth-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            <a href="login.php">Log in here</a>
        </div>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="register.php">
        <label for="customer_name">Full Name / Business Name</label>
        <input
            type="text"
            id="customer_name"
            name="customer_name"
            maxlength="150"
            value="<?= htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="email">Email Address</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="150"
            autocomplete="email"
            value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="contact_number">Contact Number</label>
        <input
            type="text"
            id="contact_number"
            name="contact_number"
            maxlength="30"
            autocomplete="tel"
            value="<?= htmlspecialchars($contactNumber, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <label for="address">Delivery Address</label>
        <textarea
            id="address"
            name="address"
            rows="3"
            required
        ><?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></textarea>

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <label for="confirm_password">Confirm Password</label>
        <input
            type="password"
            id="confirm_password"
            name="confirm_password"
            minlength="8"
            autocomplete="new-password"
            required
        >

        <button type="submit">Create Account</button>
    </form>

    <div class="auth-links">
        Already have an account?
        <a href="login.php">Log in</a>
        <br><br>
        <a href="index.php">Back to Products</a>
    </div>
</div>

</body>
</html>
