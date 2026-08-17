<?php

/**
 * Manages the Dashboard page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Create the required models
$saleManager = new Sale($conn);
$productManager = new Product($conn);
$categoryManager = new Category($conn);

// Create the dashboard controller
$dashboardController = new DashboardController($saleManager, $productManager, $categoryManager);

// Display the Dashboard page
$dashboardController->index();
