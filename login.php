<?php
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/user.php';
require_once __DIR__ . '/controllers/auth-controller.php';

$userManager = new User($conn);
$authController = new AuthController($userManager);

$authController->login();
