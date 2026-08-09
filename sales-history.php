<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/Sale.php';
require_once __DIR__ . '/controllers/sale-controller.php';

$saleManager = new Sale($conn);
$saleController = new SaleController($saleManager);

$saleController->history();
