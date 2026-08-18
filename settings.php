<?php

/**
 * Manage the Settings page.
 */

// Load required files and check user login
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

// Create the settings model and controller
$settingManager = new Setting($conn);
$settingController = new SettingController($settingManager);

// Display the Settings page
$settingController->index();
