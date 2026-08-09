<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/product.php';
require_once __DIR__ . '/controllers/product-controller.php';

$productId = $_GET['id'] ?? 0;

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

$productManager = new Product($conn);
$productController = new ProductController($productManager);

$productController->edit($productId);
