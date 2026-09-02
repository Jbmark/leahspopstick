<?php

require_once 'config/database.php';
require_once 'app/models/Product.php';

$product=new Product($pdo);
$action=$_GET['action'] ?? 'index';

switch($action){

    case 'create':
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $product->create($_POST);
            header("Location: products.php");
            exit;
        }
        include 'app/views/products/create.php';
        break;

    case 'edit':
        $id=$_GET['id'];
        if($_SERVER['REQUEST_METHOD']=='POST'){
            $product->update($id,$_POST);
            header("Location: products.php");
            exit;
        }
        $item=$product->getById($id);
        include 'app/views/products/edit.php';
        break;

    case 'delete':
        $product->deactivate($_GET['id']);
        header("Location: products.php");
        exit;

    default:
        $products=$product->getAll();
        include 'app/views/products/index.php';
}
