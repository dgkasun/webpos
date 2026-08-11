<?php

/** @var array $cartItems */
/** @var float $cartTotal */
/** @var string $error */
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
                    <?php foreach ($cartItems as $item): ?>
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