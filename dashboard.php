<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/sale.php';
require_once __DIR__ . '/classes/product.php';
require_once __DIR__ . '/classes/category.php';

require_once __DIR__ . '/controllers/dashboard-controller.php';

$saleManager = new Sale($conn);
$productManager = new Product($conn);
$categoryManager = new Category($conn);

$dashboardController = new DashboardController($saleManager, $productManager, $categoryManager);

$dashboardController->index();
