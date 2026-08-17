<?php

/**
 * Logs out the current user.
 */

// Load required files
require_once __DIR__ . '/config/load.php';

// Clear and destroy the session
session_unset();
session_destroy();

// Redirect to the Login page
header('Location: login.php');
exit;
