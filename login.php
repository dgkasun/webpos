<?php

/**
 * Manages the Login page.
 */

// Load required files
require_once __DIR__ . '/config/load.php';

// Create the user model and auth controller
$userManager = new User($conn);
$authController = new AuthController($userManager);

// Display the Login page
$authController->login();
