<?php

/** @var string $error */
/** @var array $products */
/** @var float $cartTotal */
/** @var array $cartItems */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">
    <div class="container">
        <div class="page-header">
            <div>
                <h1>POS</h1>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <section class="pos-search">
            <div class="form-group product-search-wrapper">
                <label for="product_search">Search Product</label>
                <input type="text" id="product_search" placeholder="Type product name" autocomplete="off" autofocus>
                <div id="product_results" class="product-results"></div>
            </div>
        </section>

        <form method="post" id="add_product_form" style="display: none;">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" id="selected_product_id">
            <input type="hidden" name="quantity" value="1">
        </form>

        <hr>

        <h2>Current Sale</h2>

        <?php if (empty($cartItems)): ?>
            <p>No products have been added.</p>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="update">

                <div class="table-wrapper mb20">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <?php $subtotal = $item['price'] * $item['quantity']; ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td>Rs. <?= number_format($item['price'], 2) ?></td>
                                    <td><input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>"></td>
                                    <td>Rs. <?= number_format($subtotal, 2) ?></td>
                                    <td><button type="submit" name="remove_product" formaction="remove-cart-item.php" value="<?= $item['id'] ?>">Remove</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th colspan="2">Rs. <?= number_format($cartTotal, 2) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="submit">Update Cart</button>
            </form>

            <div class="cart-actions alignright">
                <form method="post">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit">Clear Cart</button>
                </form>
                <a class="button-link" href="checkout.php">Payment</a>
            </div>
        <?php endif; ?>
    </div>
</main>


<script>
    const products = <?= json_encode($products) ?>;
    const searchInput = document.getElementById('product_search');
    const productResults = document.getElementById('product_results');
    const productIdInput = document.getElementById('selected_product_id');
    const addProductForm = document.getElementById('add_product_form');

    searchInput.addEventListener('input', function() {
        const searchText = this.value.toLowerCase().trim();

        productResults.innerHTML = '';

        if (searchText === '') {
            return;
        }

        const matches = products.filter(function(product) {
            return product.name.toLowerCase().includes(searchText);
        });

        matches.forEach(function(product) {
            const result = document.createElement('div');

            result.classList.add('product-result');

            result.innerHTML =
                '<strong>' + product.name + '</strong>' +
                '<span>Rs. ' + parseFloat(product.selling_price).toFixed(2) + ' | Stock: ' + product.stock_quantity + '</span>';

            result.addEventListener('click', function() {
                addProduct(product.id);
            });

            productResults.appendChild(result);

        });
    });

    searchInput.addEventListener('keydown', function(event) {
        if (event.key !== 'Enter') {
            return;
        }

        event.preventDefault();

        const scannedBarcode = this.value.trim();

        const product = products.find(function(product) {
            return product.barcode === scannedBarcode;
        });

        if (product) {
            addProduct(product.id);
        }
    });

    function addProduct(productId) {
        productIdInput.value = productId;
        addProductForm.submit();
    }
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>