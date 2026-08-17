<?php

/**
 * Redirects the user to the appropriate page.
 */

// Load required files
require_once __DIR__ . '/config/load.php';

// Redirect based on login
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}

exit;
