<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$message = '';
$error = '';

$categoryStmt = $conn->query(
    'SELECT id, name
     FROM categories
     WHERE is_active = 1
     ORDER BY name ASC'
);

$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = $_POST['category_id'] ?? 0;
    $costPrice = $_POST['cost_price'] ?? '';
    $sellingPrice = $_POST['selling_price'] ?? '';
    $stockQuantity = $_POST['stock_quantity'] ?? '';

    if (
        $name === '' ||
        $categoryId <= 0 ||
        $sellingPrice === ''
    ) {
        $error = 'Please complete all required fields.';
    } elseif (
        !is_numeric($costPrice) ||
        !is_numeric($sellingPrice) ||
        !is_numeric($stockQuantity)
    ) {
        $error = 'Prices and stock values must be numeric.';
    } elseif (
        $costPrice < 0 ||
        $sellingPrice < 0 ||
        $stockQuantity < 0
    ) {
        $error = 'Prices and stock values cannot be negative.';
    } else {
        try {
            $stmt = $conn->prepare(
                'INSERT INTO products (
                    category_id, name, cost_price, selling_price, stock_quantity
                ) VALUES (
                    :category_id, :name, :cost_price, :selling_price, :stock_quantity
                )'
            );

            $stmt->execute([
                'category_id' => $categoryId,
                'name' => $name,
                'cost_price' => $costPrice,
                'selling_price' => $sellingPrice,
                'stock_quantity' => $stockQuantity
            ]);

            $message = 'Product added successfully.';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'This product already exists.';
            } else {
                $error = 'Unable to add the product.';
            }
        }
    }
}

$productStmt = $conn->query(
    'SELECT products.id, products.name, products.cost_price, products.selling_price, products.stock_quantity, products.is_active, categories.name AS category_name
     FROM products
     INNER JOIN categories
        ON categories.id = products.category_id
     ORDER BY products.name ASC'
);

$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Products';
$currentPage = 'Products';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Products</h1>
            </div>
        </div>

        <?php if ($message !== ''): ?>
            <p class="success">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= $error ?>
            </p>
        <?php endif; ?>

        <?php if (empty($categories)): ?>
            <p class="error">
                Please create at least one active category before
                adding products.
            </p>
        <?php else: ?>
            <form method="post" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" maxlength="150" required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>">
                                    <?= $category['name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="cost_price">Cost Price</label>
                        <input type="number" id="cost_price" name="cost_price" min="0" step="0.01 value=" 0.00" required>
                    </div>
                    <div class="form-group">
                        <label for="selling_price">Selling Price</label>
                        <input type="number" id="selling_price" name="selling_price" min="0" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock_quantity">Opening Stock</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="0" required>
                    </div>
                </div>
                <button type="submit">Add Product</button>
            </form>
        <?php endif; ?>

        <hr>

        <h2>Existing Products</h2>

        <?php if (empty($products)): ?>
            <p>No products have been added.</p>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Cost Price</th>
                            <th>Selling Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $isLowStock =
                                $product['stock_quantity'] <=
                                $product['low_stock_level'];
                            ?>
                            <tr>
                                <td><?= ($product['name']) ?></td>
                                <td><?= $product['category_name'] ?></td>
                                <td>Rs. <?= number_format($product['cost_price'], 2) ?></td>
                                <td>Rs. <?= number_format($product['selling_price'], 2) ?></td>
                                <td><?= $product['stock_quantity'] ?></td>
                                <td><?= $product['is_active'] ? 'Active' : 'Inactive' ?></td>
                                <td><a href="edit-product.php?id=<?= $product['id'] ?>">Edit</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include 'includes/footer.php'; ?>