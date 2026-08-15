<?php

/** @var array $sale */
/** @var array $saleItems */
/** @var int $numberOfItems */
/** @var int $totalQuantity */
/** @var string $from */
/** @var string $fromDate */
/** @var string $toDate */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">
    <?php include __DIR__ . '/../../includes/pos-menu.php'; ?>
    <div class="container">

        <?php if ($from === 'history'): ?>
            <a class="button" href="sales-history.php">Back to Sales History</a>
        <?php elseif ($from === 'report'): ?>
            <a class="button" href="sales-report.php">Back to Sales Report</a>
        <?php endif; ?>

        <div class="success-btns">
            <a class="button-link xl-btn" href="pos.php">New Sale</a>
            <button onclick="window.print();" class="xl-btn">Print</button>
        </div>
        <div class="receipt-print">
            <table>
                <tr>
                    <td class="centeritem">
                        POS TEXTILE<br>
                        Colombo, SRI LANKA<br>
                        Phone: 0123456789
                    </td>
                </tr>
                <tr>
                    <td>
                        Invoice No: <?= $sale['id'] ?><br>
                        Date & Time: <?= htmlspecialchars($sale['created_at']) ?>
                    </td>
                </tr>
                <tr>
                    <td class="centeritem bolditem">INVOICE</td>
                </tr>
            </table>

            <div class="table-wrapper">
                <table class="receipt-table product-details-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($saleItems as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['unit_price'], 2) ?></td>
                                <td><?= number_format($item['subtotal'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="emptyrow">
                            <td colspan="4"></td>
                        </tr>
                    </tbody>
                    <tfoot class="total-rows">
                        <tr>
                            <th colspan="3" class="rightitem">Net Total:</th>
                            <th><?= number_format($sale['total_amount'], 2) ?></th>
                        </tr>
                        <?php if ($sale['payment_method'] === 'cash'): ?>
                            <tr>
                                <th colspan="3" class="rightitem">Cash:</th>
                                <th><?= number_format($sale['cash_received'], 2) ?></th>
                            </tr>
                            <tr>
                                <th colspan="3" class="rightitem">Cash Balance:</th>
                                <th><?= number_format($sale['change_amount'], 2) ?></th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th colspan="3"><?= htmlspecialchars(ucfirst($sale['payment_method'])) ?></th>
                                <th><?= number_format($sale['total_amount'], 2) ?></th>
                            </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
            <br>
            <table class="footer-receipt-table">
                <tr>
                    <td>
                        No of Items : <?= $numberOfItems ?>
                    </td>
                    <td>
                        Total Quantity : <?= $totalQuantity ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="centeritem">
                        THANK YOU.. COME AGAIN.!!!
                    </td>
                </tr>
            </table>
        </div>

    </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>