<?php

/**
 * Displays and manages the Edit Product page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Get the product ID
$productId = (int) ($_GET['id'] ?? 0);

// Redirect if the product ID is invalid
if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

// Create the product model and controller
$productManager = new Product($conn);
$productController = new ProductController($productManager);

// Display the Edit Product page
$productController->edit($productId);
