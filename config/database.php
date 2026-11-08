
<?php

// Use Philippines local time
date_default_timezone_set('Asia/Manila');

// Database connection details
$host = 'localhost';
$dbname = 'leahs_popstick_db';
$username = 'root';  
$password =  '';  

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Set MySQL connection to Philippine time (UTC+8)
    $pdo->exec("SET time_zone = '+08:00'");

} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
