<?php

/** @var array $sale */
/** @var array $saleItems */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">

    <?php include __DIR__ . '/../../includes/menu.php'; ?>
    <div class="container">

        <div class="receipt-actions">
            <a href="sales-history.php">Back to Sales History</a>

            <button type="button" onclick="window.location.href='sale-success.php?id=<?= $sale['id'] ?>'">Print Receipt</button>
        </div>

        <div class="receipt-header">
            <h1>Sales Receipt</h1>
        </div>

        <div class="receipt-details">
            <p>
                <strong>Sale ID:</strong>
                <?= $sale['id'] ?>
            </p>

            <p>
                <strong>Date:</strong>
                <?= htmlspecialchars($sale['created_at']) ?>
            </p>

            <p>
                <strong>Cashier:</strong>
                <?= htmlspecialchars($sale['cashier_name']) ?>
            </p>

            <p>
                <strong>Payment:</strong>
                <?= htmlspecialchars(ucfirst($sale['payment_method'])) ?>
            </p>
            <?php if ($sale['payment_method'] === 'cash'): ?>
                <p>
                    <strong>Cash Received:</strong>
                    Rs. <?= number_format($sale['cash_received'], 2) ?>
                </p>

                <p>
                    <strong>Change:</strong>
                    Rs. <?= number_format($sale['change_amount'], 2) ?>
                </p>
            <?php endif; ?>

        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($saleItems as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>Rs.<?= number_format($item['unit_price'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rs. <?= number_format($item['subtotal'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th>Rs. <?= number_format($sale['total_amount'], 2) ?>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>