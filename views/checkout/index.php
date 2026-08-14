<?php

/** @var array $cartItems */
/** @var float $cartTotal */
/** @var string $error */
/** @var int $totalItems */
/** @var int $totalQuantity */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">
    <?php include __DIR__ . '/../../includes/pos-menu.php'; ?>
    <div class="container">

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <div class="back-link"><a href="pos.php">Back</a></div>

        <div class="checkout-page">

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
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td>Rs. <?= number_format($item['price'], 2) ?>
                                </td>
                                <td><?= $item['quantity'] ?></td>
                                <td>Rs. <?= number_format($subtotal, 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="2">Total Items: <?= $totalItems ?></th>
                            <th>Qty: <?= $totalQuantity ?></th>
                            <th>Rs. <?= number_format($cartTotal, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>

            </div>

            <div class="checkout-form">
                <form method="post">
                    <div class="form-group">
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <span>Cash</span>
                            </label>

                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="card">
                                <span>Card</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group" id="cash_received_group">
                        <label for="cash_received">Cash Received</label>
                        <input type="number" id="cash_received" name="cash_received" min="<?= $cartTotal ?>" step="0.01" placeholder="0.00">

                        <div class="number-pad">
                            <button type="button" data-number="1">1</button>
                            <button type="button" data-number="2">2</button>
                            <button type="button" data-number="3">3</button>

                            <button type="button" data-number="4">4</button>
                            <button type="button" data-number="5">5</button>
                            <button type="button" data-number="6">6</button>

                            <button type="button" data-number="7">7</button>
                            <button type="button" data-number="8">8</button>
                            <button type="button" data-number="9">9</button>

                            <button type="button" data-action="clear">C</button>
                            <button type="button" data-number="0">0</button>
                            <button type="button" data-number=".">.</button>
                        </div>
                    </div>

                    <div class="alignright">
                        <button type="submit" class="xl-btn">Complete Sale</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<script>
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const cashReceivedGroup = document.getElementById('cash_received_group');
    const cashReceived = document.getElementById('cash_received');

    function updatePaymentFields() {
        const selectedPaymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (selectedPaymentMethod.value === 'cash') {
            cashReceivedGroup.style.display = 'block';
            cashReceived.required = true;
        } else {
            cashReceivedGroup.style.display = 'none';
            cashReceived.required = false;
            cashReceived.value = '';
        }
    }

    paymentMethods.forEach(function(paymentMethod) {
        paymentMethod.addEventListener('change', updatePaymentFields);
    });

    updatePaymentFields();


    /* num pad */
    const cashInput =
        document.getElementById('cash_received');

    const numberPad =
        document.querySelector('.number-pad');


    numberPad.addEventListener('click', function(event) {

        const button = event.target.closest('button');

        if (!button) {
            return;
        }

        if (button.dataset.action === 'clear') {
            cashInput.value = '';
            return;
        }

        const number = button.dataset.number;

        if (
            number === '.' &&
            cashInput.value.includes('.')
        ) {
            return;
        }

        cashInput.value += number;
    });
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>