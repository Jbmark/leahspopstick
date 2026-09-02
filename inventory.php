<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location:index.php");
    exit;
}

require_once 'app/controllers/InventoryController.php';
$controller = new InventoryController();
$controller->handleRequest();
