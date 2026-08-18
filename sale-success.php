<?php

/**
 * Displays the sale confirmation page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Get the sale ID
$saleId = (int) ($_GET['id'] ?? 0);

// Redirect if the sale ID is invalid
if ($saleId <= 0) {
    header('Location: pos.php');
    exit;
}

// Create the sale model and controller
$saleManager = new Sale($conn);
$settingManager = new Setting($conn);
$saleController = new SaleController($saleManager, $settingManager);

// Display the sale confirmation page
$saleController->success($saleId);
