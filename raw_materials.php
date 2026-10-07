<?php

session_start();

require_once 'config/database.php';
require_once 'app/controllers/RawMaterialController.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$controller = new RawMaterialController();

$controller->handleRequest();