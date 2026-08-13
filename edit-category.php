<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$categoryId = (int) ($_GET['id'] ?? 0);

if ($categoryId <= 0) {
    header('Location: categories.php');
    exit;
}

$categoryManager = new Category($conn);
$categoryController = new CategoryController($categoryManager);

$categoryController->edit($categoryId);
