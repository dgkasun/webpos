<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$saleId = (int) ($_GET['id'] ?? 0);

if ($saleId <= 0) {
    header('Location: sales-history.php');
    exit;
}

$saleManager = new Sale($conn);
$saleController = new SaleController($saleManager);

$saleController->receipt($saleId);
