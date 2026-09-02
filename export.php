<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'app/controllers/ExportController.php';

$controller = new ExportController();
$controller->salesCSV(); // Instantly captures data and triggers spreadsheet downloads stream
