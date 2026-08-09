<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/sale.php';
require_once __DIR__ . '/controllers/sale-controller.php';

$saleId = $_GET['id'] ?? 0;

if ($saleId <= 0) {
    header('Location: sales-history.php');
    exit;
}

$saleManager = new Sale($conn);
$saleController = new SaleController($saleManager);

$saleController->receipt($saleId);
