<?php

/**
 * Manage the POS page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Create the product model and cart
$productManager = new Product($conn);
$cart = new Cart();

// Create the POS controller
$posController = new PosController($productManager, $cart);

// Display the POS page
$posController->index();
