<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

/*
 * Inventory is available only to Owner and Office Staff.
 */
$role = $_SESSION['user']['role_name'] ?? '';

if (!in_array($role, ['Owner', 'Office Staff'])) {
    die("Access Denied.");
}

require_once 'app/controllers/InventoryController.php';

$controller = new InventoryController();

$controller->handleRequest();
