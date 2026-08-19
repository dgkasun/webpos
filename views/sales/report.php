<?php

/** @var string $error */
/** @var string $fromDate */
/** @var string $toDate */
/** @var array $summary */
/** @var array $bestSellingProducts */
/** @var array $sales */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">

    <?php include __DIR__ . '/../../includes/menu.php'; ?>

    <div class="container">

        <?php include __DIR__ . '/../../includes/page-header.php'; ?>
        <div class="content">

            <?php if ($error !== ''): ?>
                <p class="error">
                    <?= htmlspecialchars($error) ?>
                </p>
            <?php endif; ?>

            <div class="card mb30">
                <form method="get" class="report-filter">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="from_date">From Date</label>
                            <input type="date" id="from_date" name="from_date" value="<?= htmlspecialchars($fromDate) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="to_date">To Date</label>
                            <input type="date" id="to_date" name="to_date" value="<?= htmlspecialchars($toDate) ?>" required>
                        </div>
                    </div>
                    <button type="submit">View Report</button>
                </form>
            </div>

            <div class="dashboard-grid mb30">
                <div class="dashboard-card">
                    <h2>Total Sales</h2>
                    <p class="dashboard-number">Rs. <?= number_format($summary['sales_total'], 2) ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Transactions</h2>
                    <p class="dashboard-number"><?= $summary['sale_count'] ?></p>
                </div>
                <div class="dashboard-card">
                    <h2>Items Sold</h2>
                    <p class="dashboard-number"><?= $summary['items_sold'] ?></p>
                </div>
            </div>


            <div class="card mb30">
                <h2>Best Selling Products</h2>
                <?php if (empty($bestSellingProducts)): ?>
                    <p>No product sales found for this date range.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity Sold</th>
                                    <th>Sales Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bestSellingProducts as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['product_name']) ?></td>
                                        <td><?= $product['quantity_sold'] ?></td>
                                        <td>Rs. <?= number_format($product['sales_amount'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card">
                <h2>Sales</h2>
                <?php if (empty($sales)): ?>
                    <p>No sales found for this date range.</p>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Date</th>
                                    <th>Cashier</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Avg Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td><?= $sale['id'] ?></td>
                                        <td><?= htmlspecialchars($sale['created_at']) ?></td>
                                        <td><?= htmlspecialchars($sale['cashier_name']) ?></td>
                                        <td><?= htmlspecialchars(ucfirst($sale['payment_method'])) ?></td>
                                        <td>Rs. <?= number_format($sale['total_amount'], 2) ?></td>
                                        <td>
                                            <?php if ($sale['transaction_time'] !== null): ?>
                                                <?= $sale['transaction_time'] ?>s
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td><a href="sale-success.php?id=<?= $sale['id'] ?>&from=report">View</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>


    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>