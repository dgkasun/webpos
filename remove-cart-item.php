<?php

require_once __DIR__ . '/config/load.php';
require_once __DIR__ . '/includes/auth.php';

$productId = (int) ($_POST['remove_product'] ?? 0);

if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
    unset($_SESSION['cart'][$productId]);
}

header('Location: pos.php');
exit;
