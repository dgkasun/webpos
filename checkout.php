<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

if (empty($_SESSION['cart'])) {
    header('Location: pos.php');
    exit;
}

$error = '';
$cartTotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = $_POST['payment_method'] ?? 'cash';
    $cashReceived = $_POST['cash_received'] ?? '';

    if (!in_array($paymentMethod, ['cash', 'card'], true)) {
        $error = 'Please select a valid payment method.';
    } elseif (
        $paymentMethod === 'cash' &&
        (
            $cashReceived === '' ||
            !is_numeric($cashReceived) ||
            (float) $cashReceived < $cartTotal
        )
    ) {
        $error = 'Cash received must be equal to or greater than the total.';
    } else {
        try {
            $conn->beginTransaction();

            $cashReceivedAmount = null;
            $changeAmount = null;

            if ($paymentMethod === 'cash') {
                $cashReceivedAmount = $cashReceived;
                $changeAmount = $cashReceivedAmount - $cartTotal;
            }

            // Create sale record.
            $saleQuery = $conn->prepare(
                'INSERT INTO sales (
                    user_id,
                    total_amount,
                    payment_method,
                    cash_received,
                    change_amount
                ) VALUES (
                    :user_id,
                    :total_amount,
                    :payment_method,
                    :cash_received,
                    :change_amount
                )'
            );

            $saleQuery->execute([
                'user_id' => $_SESSION['user_id'],
                'total_amount' => $cartTotal,
                'payment_method' => $paymentMethod,
                'cash_received' => $cashReceivedAmount,
                'change_amount' => $changeAmount,
            ]);

            $saleId = $conn->lastInsertId();

            // Prepare the queries needed for each item.
            $productQuery = $conn->prepare(
                'SELECT stock_quantity
                 FROM products
                 WHERE id = :product_id'
            );

            $itemQuery = $conn->prepare(
                'INSERT INTO sale_items (
                    sale_id,
                    product_id,
                    quantity,
                    unit_price,
                    subtotal
                ) VALUES (
                    :sale_id,
                    :product_id,
                    :quantity,
                    :unit_price,
                    :subtotal
                )'
            );

            $stockQuery = $conn->prepare(
                'UPDATE products
                 SET stock_quantity = stock_quantity - :quantity
                 WHERE id = :product_id'
            );

            foreach ($_SESSION['cart'] as $item) {
                $productId = $item['id'];
                $quantity = $item['quantity'];
                $unitPrice = $item['price'];
                $subtotal = $unitPrice * $quantity;

                // Perform one final stock check before completing the sale.
                $productQuery->execute([
                    'product_id' => $productId,
                ]);

                // Save.
                $itemQuery->execute([
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock.
                $stockQuery->execute([
                    'quantity' => $quantity,
                    'product_id' => $productId,
                ]);
            }

            $conn->commit();

            $_SESSION['cart'] = [];

            header('Location: sale-success.php?id=' . $saleId);
            exit;
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $error = $e->getMessage();
        }
    }
}

$pageTitle = 'Checkout';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Checkout</h1>
            </div>
            <a href="pos.php">Back to POS</a>
        </div>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <div class="table-wrapper mb20">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; ?>
                        <tr>
                            <td><?= $item['name'] ?></td>
                            <td>Rs. <?= number_format($item['price'], 2) ?>
                            </td>
                            <td><?= $item['quantity'] ?></td>
                            <td>Rs. <?= number_format($subtotal, 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th>Rs. <?= number_format($cartTotal, 2) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <form method="post">
            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select id="payment_method" name="payment_method" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                </select>
            </div>

            <div class="form-group" id="cash_received_group">
                <label for="cash_received">Cash Received</label>
                <input type="number" id="cash_received" name="cash_received" min="<?= $cartTotal ?>" step="0.01" placeholder="Amount customer gives">
            </div>

            <div class="alignright">
                <button type="submit">Complete Sale</button>
            </div>
        </form>
    </div>
</main>

<script>
    const paymentMethod = document.getElementById('payment_method');
    const cashReceivedGroup = document.getElementById('cash_received_group');
    const cashReceived = document.getElementById('cash_received');

    function updatePaymentFields() {
        if (paymentMethod.value === 'cash') {
            cashReceivedGroup.style.display = 'block';
            cashReceived.required = true;
        } else {
            cashReceivedGroup.style.display = 'none';
            cashReceived.required = false;
            cashReceived.value = '';
        }
    }

    paymentMethod.addEventListener('change', updatePaymentFields);

    updatePaymentFields();
</script>

<?php include 'includes/footer.php'; ?>