<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$saleManager = new Sale($conn);
$saleController = new SaleController($saleManager);

$saleController->history();
