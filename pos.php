<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/product.php';
require_once __DIR__ . '/classes/cart.php';
require_once __DIR__ . '/controllers/pos-controller.php';

$productManager = new Product($conn);
$cart = new Cart();

$posController = new PosController($productManager, $cart);

$posController->index();
