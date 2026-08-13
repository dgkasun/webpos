<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$productManager = new Product($conn);
$productController = new ProductController($productManager);

$productController->index();
