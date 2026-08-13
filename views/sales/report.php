<?php

/** @var string $fromDate */
/** @var string $toDate */
/** @var array $summary */
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>

    <div class="container">

        <div class="page-header">
            <div>
                <h1>Sales Report</h1>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <form method="get" class="report-filter">
            <div class="form-grid">
                <div class="form-group">
                    <label for="from_date">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="<?= $fromDate ?>" required>
                </div>
                <div class="form-group">
                    <label for="to_date">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="<?= $toDate ?>" required>
                </div>
            </div>
            <button type="submit">View Report</button>
        </form>

        <hr>

        <div class="dashboard-grid">
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
        <Br>

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
                                <td><?= $product['product_name'] ?></td>
                                <td><?= $product['quantity_sold'] ?></td>
                                <td>Rs. <?= number_format($product['sales_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <Br>

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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?= $sale['id'] ?></td>
                                <td><?= $sale['created_at'] ?></td>
                                <td><?= $sale['cashier_name'] ?></td>
                                <td><?= ucfirst($sale['payment_method']) ?></td>
                                <td>Rs. <?= number_format($sale['total_amount'], 2) ?></td>
                                <td><a href="receipt.php?id=<?= (int) $sale['id'] ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>



    </div>
</main>

<?php include 'includes/footer.php'; ?>