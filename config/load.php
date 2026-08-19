<?php

/**
 * Loads the files required by the application.
 */

// Set application timezone
date_default_timezone_set('Asia/Colombo');

// Start the session
session_start();

// Load database connection
require_once __DIR__ . '/database.php';

// Show PHP errors
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Load classes
require_once __DIR__ . '/../classes/category.php';
require_once __DIR__ . '/../classes/product.php';
require_once __DIR__ . '/../classes/sale.php';
require_once __DIR__ . '/../classes/cart.php';
require_once __DIR__ . '/../classes/user.php';
require_once __DIR__ . '/../classes/settings.php';

// Load controllers
require_once __DIR__ . '/../controllers/category-controller.php';
require_once __DIR__ . '/../controllers/product-controller.php';
require_once __DIR__ . '/../controllers/sale-controller.php';
require_once __DIR__ . '/../controllers/dashboard-controller.php';
require_once __DIR__ . '/../controllers/pos-controller.php';
require_once __DIR__ . '/../controllers/checkout-controller.php';
require_once __DIR__ . '/../controllers/auth-controller.php';
require_once __DIR__ . '/../controllers/setting-controller.php';
