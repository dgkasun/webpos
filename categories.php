<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/category.php';
require_once __DIR__ . '/controllers/category-controller.php';

$categoryManager = new Category($conn);

$categoryController = new CategoryController(
    $categoryManager
);

$categoryController->index();
