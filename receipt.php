<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$saleId = $_GET['id'] ?? 0;

if ($saleId <= 0) {
    header('Location: sales-history.php');
    exit;
}

$saleQuery = $conn->prepare(
    'SELECT
        sales.id,
        sales.total_amount,
        sales.payment_method,
        sales.created_at,
        users.name AS cashier_name
     FROM sales
     INNER JOIN users
        ON users.id = sales.user_id
     WHERE sales.id = :sale_id'
);

$saleQuery->execute([
    'sale_id' => $saleId,
]);

$sale = $saleQuery->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    exit('Sale not found.');
}

$saleItemsQuery = $conn->prepare(
    'SELECT
        products.name AS product_name,
        sale_items.quantity,
        sale_items.unit_price,
        sale_items.subtotal
     FROM sale_items
     INNER JOIN products
        ON products.id = sale_items.product_id
     WHERE sale_items.sale_id = :sale_id
     ORDER BY sale_items.id ASC'
);

$saleItemsQuery->execute([
    'sale_id' => $saleId,
]);

$saleItems = $saleItemsQuery->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Receipt';
$currentPage = 'Sales History';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>
    <div class="container">

        <div class="receipt-actions">
            <a href="sales-history.php">Back to Sales History</a>

            <button type="button" onclick="window.print()">
                Print Receipt
            </button>
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
                <?= $sale['created_at'] ?>
            </p>

            <p>
                <strong>Cashier:</strong>
                <?= $sale['cashier_name'] ?>
            </p>

            <p>
                <strong>Payment:</strong>
                <?= ucfirst($sale['payment_method']) ?>
            </p>
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
                            <td><?= $item['product_name'] ?></td>
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

<?php include 'includes/footer.php'; ?>