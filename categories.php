<?php

/**
 * Displays and manages the Categories page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Create the category model and controller
$categoryManager = new Category($conn);
$categoryController = new CategoryController($categoryManager);

// Display the Categories page
$categoryController->index();
