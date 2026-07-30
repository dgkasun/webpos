<?php

session_start();

$productId = $_POST['remove_product'] ?? 0;

if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
    unset($_SESSION['cart'][$productId]);
}

header('Location: pos.php');
exit;
