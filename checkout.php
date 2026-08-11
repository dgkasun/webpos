<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Cart.php';
require_once __DIR__ . '/classes/Sale.php';
require_once __DIR__ . '/controllers/checkout-controller.php';

$cart = new Cart();
$saleManager = new Sale($conn);

$checkoutController = new CheckoutController($cart, $saleManager);

$checkoutController->index();
