<?php

session_start();

require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../classes/category.php';
require_once __DIR__ . '/../classes/product.php';
require_once __DIR__ . '/../classes/sale.php';
require_once __DIR__ . '/../classes/cart.php';
require_once __DIR__ . '/../classes/user.php';

require_once __DIR__ . '/../controllers/category-controller.php';
require_once __DIR__ . '/../controllers/product-controller.php';
require_once __DIR__ . '/../controllers/sale-controller.php';
require_once __DIR__ . '/../controllers/dashboard-controller.php';
require_once __DIR__ . '/../controllers/pos-controller.php';
require_once __DIR__ . '/../controllers/checkout-controller.php';
require_once __DIR__ . '/../controllers/auth-controller.php';
