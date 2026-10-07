<?php

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

require_once 'app/controllers/CustomerController.php';

$controller = new CustomerController();

$action = $_GET['action'] ?? 'index';

switch ($action) {

    case 'create':
        $controller->create();
        break;

    case 'edit':
        $id = intval($_GET['id'] ?? 0);

        $controller->edit($id);
        break;

    default:
        $controller->index();
        break;
}
