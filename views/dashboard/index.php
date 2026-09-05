<?php

/** @var array $todaySales */
/** @var int $productCount */
/** @var int $categoryCount */
/** @var int $lowStockCount */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">

    <?php include __DIR__ . '/../../includes/menu.php'; ?>

    <div class="container">
        <?php include __DIR__ . '/../../includes/page-header.php'; ?>

        <div class="content">
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
                    <h2>Low Stock Products: <?= $lowStockCount ?></h2>
                    <?php if (!empty($lowStockProducts)): ?>
                        <table>
                            <tbody>
                                <?php foreach ($lowStockProducts as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td>
                                            <?php if ($product['sale_unit'] === 'unit'): ?>
                                                <?= number_format($product['stock_quantity'], 0) ?>
                                            <?php else: ?>
                                                <?= number_format($product['stock_quantity'], 2) ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>




        </div>
    </div>

</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>