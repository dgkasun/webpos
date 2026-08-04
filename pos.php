<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/classes/product.php';
require_once __DIR__ . '/classes/cart.php';

$productManager = new Product($conn);
$cart = new Cart();

/*if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}*/

$error = '';

$products = $productManager->getAvailableForSale();

/*$productQ = $conn->query(
    'SELECT id, name, selling_price, stock_quantity, barcode
     FROM products
     WHERE is_active = 1
       AND stock_quantity > 0
     ORDER BY name ASC'
);

$products = $productQ->fetchAll(PDO::FETCH_ASSOC);*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $productId = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;

        if ($productId <= 0 || $quantity <= 0) {
            $error = 'Please select a product and valid quantity.';
        } else {
            $product = $productManager->findAvailable($productId);
            /*$productQuery = $conn->prepare(
                'SELECT id, name, selling_price, stock_quantity
                 FROM products
                 WHERE id = :id'
            );

            $productQuery->execute([
                'id' => $productId,
            ]);

            $product = $productQuery->fetch(PDO::FETCH_ASSOC);*/

            if (!$product) {
                $error = 'Product not found.';
            } else {
                $error = $cart->add($product, $quantity);

                /*$existingQuantity =
                    $_SESSION['cart'][$productId]['quantity'] ?? 0;

                $newQuantity = $existingQuantity + $quantity;

                if ($newQuantity > $product['stock_quantity']) {
                    $error = 'Not enough stock available.';
                } else {
                    $_SESSION['cart'][$productId] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['selling_price'],
                        'quantity' => $newQuantity,
                        'stock_quantity' =>
                        $product['stock_quantity'],
                    ];
                }*/
            }
        }
    }

    if ($action === 'update') {
        $quantities = $_POST['quantities'] ?? [];
        $error = $cart->update($quantities);

        /*foreach ($quantities as $productId => $quantity) {
            $productId = $productId;
            $quantity = $quantity;

            if (!isset($_SESSION['cart'][$productId])) {
                continue;
            }

            $availableStock =
                $_SESSION['cart'][$productId]['stock_quantity'];

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
            } elseif ($quantity <= $availableStock) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            } else {
                $error = 'One or more quantities exceed available stock.';
            }
        }*/
    }

    if ($action === 'remove') {
        $productId = ($_POST['product_id'] ?? 0);
        $cart->remove($productId);
        //unset($_SESSION['cart'][$productId]);
    }

    if ($action === 'clear') {
        $cart->clear();
        //$_SESSION['cart'] = [];
    }
}

/*$productQ = $conn->query(
    'SELECT id, name, selling_price, stock_quantity
     FROM products
     WHERE stock_quantity > 0
     ORDER BY name ASC'
);

$products = $productQ->fetchAll(PDO::FETCH_ASSOC);*/

/*$cartTotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}*/

$cartItems = $cart->getItems();
$cartTotal = $cart->getTotal();

$pageTitle = 'POS';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">
    <div class="container">
        <div class="page-header">
            <div>
                <h1>POS</h1>
            </div>
        </div>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= $error ?>
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

        <!--
        <form method="post">
            <input type="hidden" name="action" value="add">

            <div class="form-grid">
                <div class="form-group">
                    <label for="product_id">Product</label>

                    <select id="product_id" name="product_id" required>
                        <option value="">Select a product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= $product['id'] ?>">
                                <?= $product['name'] ?>
                                - Rs. <?= number_format($product['selling_price'], 2) ?>
                                (Stock: <?= $product['stock_quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                </div>
            </div>

            <button type="submit">Add to Cart</button>
        </form> -->

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
                                    <td><?= $item['name'] ?></td>
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

<?php include 'includes/footer.php'; ?>