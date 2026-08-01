<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$saleId = $_GET['id'] ?? 0;

if ($saleId <= 0) {
    header('Location: pos.php');
    exit;
}

$saleQuery = $conn->prepare(
    'SELECT
        total_amount,
        payment_method,
        created_at
     FROM sales
     WHERE id = :id'
);

$saleQuery->execute([
    'id' => $saleId,
]);

$sale = $saleQuery->fetch(PDO::FETCH_ASSOC);

if (!$sale) {
    exit('Sale not found.');
}

$pageTitle = 'Sale Complete';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">
    <div class="container">

        <h1>Sale Completed</h1>
        <table>
            <tr>
                <th>Sale ID</th>
                <td><?= $saleId ?></td>
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

        <br>

        <a class="button" href="pos.php">New Sale</a>
        <button onclick="window.print();">Print Receipt</button>

</main>

<?php include 'includes/footer.php'; ?>