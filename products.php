<?php

/**
 * Displays and manages the Product page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Create the Product model and controller
$productManager = new Product($conn);
$productController = new ProductController($productManager);

// Display the Product page
$productController->index();
