<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$salesQuery = $conn->query(
    'SELECT
        sales.id,
        sales.total_amount,
        sales.payment_method,
        sales.created_at,
        users.name AS cashier_name
     FROM sales
     INNER JOIN users
        ON users.id = sales.user_id
     ORDER BY sales.created_at DESC'
);

$sales = $salesQuery->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Sales History';
$currentPage = 'Sales History';
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

        <?php if (empty($sales)): ?>
            <p>No sales have been recorded.</p>
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
                                <td><a href="receipt.php?id=<?= $sale['id'] ?>">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>