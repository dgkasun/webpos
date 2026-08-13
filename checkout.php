<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$cart = new Cart();
$saleManager = new Sale($conn);

$checkoutController = new CheckoutController($cart, $saleManager);

$checkoutController->index();
