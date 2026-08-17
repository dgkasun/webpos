<?php

/**
 * Removes a product from the shopping cart.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Get the product ID
$productId = (int) ($_POST['remove_product'] ?? 0);

// Remove the product from the cart
if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
    unset($_SESSION['cart'][$productId]);
}

// Redirect to the POS page
header('Location: pos.php');
exit;
