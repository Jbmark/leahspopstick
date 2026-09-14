<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'app/controllers/ExpenseController.php';
$controller = new ExpenseController();

$action = $_GET['action'] ?? 'index';

if($action == 'create'){
    $controller->create();
} else {
    $controller->index();
}
