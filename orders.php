<?php
session_start();

// Defense Checkpoint: Force redirect if no session exists
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'app/controllers/OrderController.php';

// Instantiate and fire the state-management router engine
$controller = new OrderController();
$controller->handleRequest();
