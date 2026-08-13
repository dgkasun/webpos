<?php

/** @var array $todaySales */
/** @var int $productCount */
/** @var int $categoryCount */
/** @var int $lowStockCount */
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>

    <div class="container">
        <div class="page-header">
            <div>
                <h1>Dashboard</h1>
                <p>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>.</p>
            </div>

        </div>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h2>Today's Sales</h2>

                <p class="dashboard-number">
                    Rs. <?= number_format($todaySales['sales_total'], 2) ?>
                </p>
            </div>

            <div class="dashboard-card">
                <h2>Transactions Today</h2>
                <p class="dashboard-number">
                    <?= $todaySales['sale_count'] ?>
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