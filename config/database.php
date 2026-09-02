<?php

$host = "localhost";
$dbname = "leahs_popstick_db";
$username = "root";
$password = "";

try{
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(PDOException $e){

    die("Database Connection Failed: " . $e->getMessage());

}