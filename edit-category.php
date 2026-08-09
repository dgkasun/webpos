<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/category.php';
require_once __DIR__ . '/controllers/category-controller.php';

$categoryId = $_GET['id'] ?? 0;

if ($categoryId <= 0) {
    header('Location: categories.php');
    exit;
}

$categoryManager = new Category($conn);
$categoryController = new CategoryController($categoryManager);

$categoryController->edit($categoryId);
