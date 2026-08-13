<?php
require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$saleManager = new Sale($conn);
$productManager = new Product($conn);
$categoryManager = new Category($conn);

$dashboardController = new DashboardController($saleManager, $productManager, $categoryManager);

$dashboardController->index();
