<?php

/**
 * Displays and manages the Sales History page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Create the sale model and controller
$saleManager = new Sale($conn);
$settingManager = new Setting($conn);
$saleController = new SaleController($saleManager, $settingManager);

// Display the Sales History page
$saleController->history();
