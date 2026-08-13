<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$productManager = new Product($conn);
$cart = new Cart();

$posController = new PosController($productManager, $cart);

$posController->index();
