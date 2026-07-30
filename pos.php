<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $productId = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;

        if ($productId <= 0 || $quantity <= 0) {
            $error = 'Please select a product and valid quantity.';
        } else {
            $productQuery = $conn->prepare(
                'SELECT id, name, selling_price, stock_quantity
                 FROM products
                 WHERE id = :id'
            );

            $productQuery->execute([
                'id' => $productId,
            ]);

            $product = $productQuery->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                $error = 'Product not found.';
            } else {
                $existingQuantity =
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
                }
            }
        }
    }

    if ($action === 'update') {
        $quantities = $_POST['quantities'] ?? [];

        foreach ($quantities as $productId => $quantity) {
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
        }
    }

    if ($action === 'remove') {
        $productId = ($_POST['product_id'] ?? 0);

        unset($_SESSION['cart'][$productId]);
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
    }
}

$productQ = $conn->query(
    'SELECT id, name, selling_price, stock_quantity
     FROM products
     WHERE stock_quantity > 0
     ORDER BY name ASC'
);

$products = $productQ->fetchAll(PDO::FETCH_ASSOC);

$cartTotal = 0;

foreach ($_SESSION['cart'] as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS</title>
    <link rel="stylesheet" href="assets/css/style.css?v=1">
</head>

<body>

    <main class="container">
        <div class="page-header">
            <div>
                <h1>POS</h1>
            </div>

            <a href="dashboard.php">Back to Dashboard</a>
        </div>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= $error ?>
            </p>
        <?php endif; ?>

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
        </form>

        <hr>

        <h2>Current Sale</h2>

        <?php if (empty($_SESSION['cart'])): ?>
            <p>No products have been added.</p>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="action" value="update">

                <div class="table-wrapper">
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
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <?php $subtotal = $item['price'] * $item['quantity']; ?>
                                <tr>
                                    <td>
                                        <?= $item['name'] ?>
                                    </td>
                                    <td>
                                        Rs.
                                        <?= number_format($item['price'], 2) ?>
                                    </td>
                                    <td>
                                        <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>">
                                    </td>
                                    <td>
                                        Rs. <?= number_format($subtotal, 2) ?>
                                    </td>
                                    <td>
                                        <button type="submit" name="remove_product" formaction="remove-cart-item.php" value="<?= $item['id'] ?>">
                                            Remove
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total</th>
                                <th colspan="2">
                                    Rs. <?= number_format($cartTotal, 2) ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="submit">Update Cart</button>
            </form>

            <div class="cart-actions">
                <form method="post">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit">Clear Cart</button>
                </form>

                <a class="button-link" href="checkout.php">Payment</a>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>