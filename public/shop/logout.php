
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Remove only the customer login session.
// Keep the staff session untouched.
unset($_SESSION['customer']);

header('Location: login.php');
exit;
