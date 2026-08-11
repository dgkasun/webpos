<?php

/** @var array $sale */
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">
    <div class="container">

        <h1>Sale Completed</h1>
        <table>
            <tr>
                <th>Sale ID</th>
                <td><?= $sale['id'] ?></td>
            </tr>
            <tr>
                <th>Total</th>
                <td>Rs. <?= number_format($sale['total_amount'], 2) ?></td>
            </tr>
            <tr>
                <th>Payment</th>
                <td><?= ucfirst($sale['payment_method']) ?></td>
            </tr>
            <tr>
                <th>Date</th>
                <td><?= $sale['created_at'] ?></td>
            </tr>
        </table>

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

        <br>

        <a class="button" href="pos.php">New Sale</a>
        <button onclick="window.print();">Print Receipt</button>

</main>

<?php include 'includes/footer.php'; ?>