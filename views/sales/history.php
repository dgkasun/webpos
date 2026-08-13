<?php

/** @var array $sales */
/** @var string $fromDate */
/** @var string $toDate */
/** @var int $saleId */
/** @var int $page */
/** @var int $totalPages */
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Sales History</h1>
            </div>
        </div>

        <form method="get" class="sales-filter">
            <div class="form-grid">
                <div class="form-group">
                    <label for="from_date">Date From</label>
                    <input type="date" id="from_date" name="from_date" value="<?= htmlspecialchars($fromDate) ?>">
                </div>
                <div class="form-group">
                    <label for="to_date">Date To</label>
                    <input type="date" id="to_date" name="to_date" value="<?= htmlspecialchars($toDate) ?>">
                </div>
                <div class="form-group">
                    <label for="sale_id">Sale ID</label>
                    <input type="number" id="sale_id" name="sale_id" min="1" value="<?= $saleId > 0 ? $saleId : '' ?>" placeholder="Sale ID">
                </div>
            </div>
            <button type="submit">Filter</button>
            <?php if ($fromDate !== '' || $toDate !== '' || $saleId > 0): ?>
                <a class="button-link" href="sales-history.php"> Clear </a>
            <?php endif; ?>
        </form>

        <hr>

        <?php if (empty($sales)): ?>
            <p>No sales found.</p>
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
                                <td><?= htmlspecialchars($sale['created_at']) ?></td>
                                <td><?= htmlspecialchars($sale['cashier_name']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($sale['payment_method'])) ?></td>
                                <td>Rs. <?= number_format($sale['total_amount'], 2) ?></td>
                                <td><a href="receipt.php?id=<?= $sale['id'] ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="sales-history.php?from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&sale_id=<?= $saleId ?>&page=<?= $page - 1 ?>">
                                Previous
                            </a>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="active">
                                    <?= $i ?>
                                </span>
                            <?php else: ?>
                                <a href="sales-history.php?from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&sale_id=<?= $saleId ?>&page=<?= $i ?>">
                                    <?= $i ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <a href="sales-history.php?from_date=<?= urlencode($fromDate) ?>&to_date=<?= urlencode($toDate) ?>&sale_id=<?= $saleId ?>&page=<?= $page + 1 ?>">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>