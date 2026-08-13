<?php
require_once __DIR__ . '/config/load.php';

$userManager = new User($conn);
$authController = new AuthController($userManager);

$authController->login();
