<?php

/**
 * Displays and manages the Edit Category page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Get the category ID
$categoryId = (int) ($_GET['id'] ?? 0);

// Redirect if the category ID is invalid
if ($categoryId <= 0) {
    header('Location: categories.php');
    exit;
}

// Create the category model and controller
$categoryManager = new Category($conn);
$categoryController = new CategoryController($categoryManager);

// Display the Edit Category page
$categoryController->edit($categoryId);
