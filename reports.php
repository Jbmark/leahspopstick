<?php
session_start();

// Protection: Kick out anyone not logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'app/controllers/ReportController.php';
$controller = new ReportController();

// Grab the action from the URL link parameters
$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'invoice':
        $controller->invoice($_GET['id']);
        break;
        
    case 'printSales':
        $controller->printSales();
        break;
        
    case 'dashboard':
    default:
        $controller->dashboard(); // This opens the main page with charts and buttons!
        break;
}
