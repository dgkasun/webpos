<?php

/**
 * Manage the Checkout page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Create the cart and sale model
$cart = new Cart();
$saleManager = new Sale($conn);

// Create the checkout controller
$checkoutController = new CheckoutController($cart, $saleManager);

// Display the Checkout page
$checkoutController->index();
