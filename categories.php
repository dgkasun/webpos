<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$categoryManager = new Category($conn);
$categoryController = new CategoryController($categoryManager);

$categoryController->index();
