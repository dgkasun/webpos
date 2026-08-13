<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$productId = (int) ($_GET['id'] ?? 0);

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

$productManager = new Product($conn);
$productController = new ProductController($productManager);

$productController->edit($productId);
