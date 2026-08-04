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

$saleManager = new Sale($conn);
$productManager = new Product($conn);
$categoryManager = new Category($conn);

$todaySales = $saleManager->getTodaySummary();

/*$todaySalesQuery = $conn->query(
    'SELECT
        COUNT(*) AS sale_count,
        COALESCE(SUM(total_amount), 0) AS sales_total
     FROM sales
     WHERE DATE(created_at) = CURDATE()'
);

$todaySales = $todaySalesQuery->fetch(PDO::FETCH_ASSOC);*/

$productCount = $productManager->getCount();
/*$productCountQuery = $conn->query(
    'SELECT COUNT(*) FROM products'
);

$productCount = $productCountQuery->fetchColumn();*/

$categoryCount = $categoryManager->getCount();

/*$categoryCountQuery = $conn->query(
    'SELECT COUNT(*) FROM categories'
);

$categoryCount = $categoryCountQuery->fetchColumn();*/

$lowStockCount = $productManager->getLowStockCount();

/*$lowStockQuery = $conn->query(
    'SELECT COUNT(*)
     FROM products
     WHERE stock_quantity <= 5'
);

$lowStockCount = $lowStockQuery->fetchColumn();*/

$pageTitle = 'Dashboard';
$currentPage = 'Dashboard';

?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>

    <div class="container">
        <div class="page-header">
            <div>
                <h1>Dashboard</h1>
                <p>Welcome, <?= $_SESSION['user_name'] ?>.</p>
            </div>

        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Today's Sales</h2>

                <p class="dashboard-number">
                    Rs.
                    <?= number_format(
                        (float) $todaySales['sales_total'],
                        2
                    ) ?>
                </p>
            </div>

            <div class="dashboard-card">
                <h2>Transactions Today</h2>
                <p class="dashboard-number">
                    <?= (int) $todaySales['sale_count'] ?>
                </p>
            </div>

            <div class="dashboard-card">
                <h2>Total Products</h2>
                <p class="dashboard-number">
                    <?= $productCount ?>
                </p>
            </div>

            <div class="dashboard-card">
                <h2>Total Categories</h2>
                <p class="dashboard-number">
                    <?= $categoryCount ?>
                </p>
            </div>

            <div class="dashboard-card">
                <h2>Low Stock Products</h2>
                <p class="dashboard-number">
                    <?= $lowStockCount ?>
                </p>
            </div>
        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>