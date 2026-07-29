<?php
session_start();

// Prevent unauthorised users
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Load the db
require_once __DIR__ . '/config/database.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve and validate
    $productId = (int) ($_POST['product_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $paymentMethod = $_POST['payment_method'] ?? 'cash';

    if ($productId <= 0) {
        $error = 'Please select a product';
    } else {
        try {
            // Begin a transaction
            $conn->beginTransaction();

            // Retrieve the selected product
            $productQuery = $conn->prepare(
                'SELECT id, name, selling_price, stock_quantity
                 FROM products
                 WHERE id = :id
                 FOR UPDATE'
            );

            $productQuery->execute([
                'id' => $productId,
            ]);

            $product = $productQuery->fetch(PDO::FETCH_ASSOC);

            // Check stock
            if ((int) $product['stock_quantity'] < $quantity) {
                throw new RuntimeException(
                    'Not enough stock available. Current stock: ' .
                        (int) $product['stock_quantity']
                );
            }

            // Calculate the sale
            $unitPrice = (float) $product['selling_price'];
            $subtotal = $unitPrice * $quantity;

            // Create a sale record.
            $saleQuery = $conn->prepare(
                'INSERT INTO sales (
                    user_id,
                    total_amount,
                    payment_method
                ) VALUES (
                    :user_id,
                    :total_amount,
                    :payment_method
                )'
            );

            $saleQuery->execute([
                'user_id' => $_SESSION['user_id'],
                'total_amount' => $subtotal,
                'payment_method' => $paymentMethod,
            ]);

            // Get the ID of the created sale
            $saleId = (int) $conn->lastInsertId();

            // Save the purchased in the sale_items
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

            $itemQuery->execute([
                'sale_id' => $saleId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            // Reduce stock
            $stockQuery = $conn->prepare(
                'UPDATE products
                 SET stock_quantity = stock_quantity - :quantity
                 WHERE id = :product_id'
            );

            $stockQuery->execute([
                'quantity' => $quantity,
                'product_id' => $productId,
            ]);

            // Commit all db changes
            $conn->commit();

            $message = 'Sale completed successfully. Sale ID: ' . $saleId;
        } catch (PDOException $e) {
            // Undo all db changes if an error happen
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $error = 'Unable to complete the sale.';
        }
    }
}

// Load products that currently have stock
$productQ = $conn->query(
    'SELECT id, name, selling_price, stock_quantity
     FROM products
     WHERE stock_quantity > 0
     ORDER BY name ASC'
);

$products = $productQ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <main class="container">
        <div class="page-header">
            <div>
                <h1>New Sale</h1>
                <p>Record a new sales transaction.</p>
            </div>

            <a href="dashboard.php">Back to Dashboard</a>
        </div>

        <!-- Success message -->
        <?php if ($message !== ''): ?>
            <p class="success">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <!-- Error message -->
        <?php if ($error !== ''): ?>
            <p class="error">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <p>No products with available stock were found.</p>
        <?php else: ?>
            <form method="post">
                <div class="form-group">
                    <label for="product_id">Product</label>

                    <select id="product_id" name="product_id" required>
                        <option value="">Select a product</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?= (int) $product['id'] ?>">
                                <?= $product['name'] ?>
                                -
                                Rs.
                                <?= number_format(
                                    (float) $product['selling_price'],
                                    2
                                ) ?>
                                (<?= (int) $product['stock_quantity'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="quantity">Quantity</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" required>
                </div>

                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                    </select>
                </div>

                <button type="submit">Complete Sale</button>
            </form>
        <?php endif; ?>
    </main>

</body>

</html>