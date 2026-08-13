<?php

require_once __DIR__ . '/config/load.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}

exit;
