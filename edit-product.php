<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$productId = $_GET['id'] ?? 0;

if ($productId <= 0) {
    header('Location: products.php');
    exit;
}

$message = '';
$error = '';

$categoriesQuery = $conn->query(
    'SELECT id, name
     FROM categories
     WHERE is_active = 1
     ORDER BY name ASC'
);

$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

$productQuery = $conn->prepare(
    'SELECT id, category_id, name, cost_price, selling_price, stock_quantity, is_active
     FROM products
     WHERE id = :id'
);

$productQuery->execute([
    'id' => $productId,
]);

$product = $productQuery->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    exit('Product not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $categoryId = $_POST['category_id'] ?? 0;
    $costPrice = $_POST['cost_price'] ?? '';
    $sellingPrice = $_POST['selling_price'] ?? '';
    $stockQuantity = $_POST['stock_quantity'] ?? '';
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if (
        $name === '' ||
        $categoryId <= 0 ||
        $costPrice === '' ||
        $sellingPrice === '' ||
        $stockQuantity === ''
    ) {
        $error = 'Please complete all fields.';
    } elseif (
        !is_numeric($costPrice) ||
        !is_numeric($sellingPrice) ||
        !is_numeric($stockQuantity)
    ) {
        $error = 'Prices and stock must be numeric.';
    } elseif (
        $costPrice < 0 ||
        $sellingPrice < 0 ||
        $stockQuantity < 0
    ) {
        $error = 'Prices and stock cannot be negative.';
    } else {
        $updateProductQuery = $conn->prepare(
            'UPDATE products
             SET
                category_id = :category_id,
                name = :name,
                cost_price = :cost_price,
                selling_price = :selling_price,
                stock_quantity = :stock_quantity,
                is_active = :is_active
             WHERE id = :id'
        );

        $updateProductQuery->execute([
            'category_id' => $categoryId,
            'name' => $name,
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock_quantity' => $stockQuantity,
            'is_active' => $isActive,
            'id' => $productId,
        ]);

        $message = 'Product updated successfully.';

        $productQuery->execute([
            'id' => $productId,
        ]);

        $product = $productQuery->fetch(PDO::FETCH_ASSOC);
    }
}

$pageTitle = 'Edit Product';
$currentPage = 'Products';
?>

<?php include 'includes/header.php'; ?>

<main class="wrap">

    <?php include 'includes/menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Edit Product</h1>
            </div>
            <a href="products.php">Back to Products</a>
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

        <form method="post">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" maxlength="150" value="<?= $product['name'] ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $product['category_id'] === $category['id'] ? 'selected' : '' ?>>
                            <?= $category['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="cost_price">Cost Price</label>
                <input type="number" id="cost_price" name="cost_price" min="0" step="0.01" value="<?= $product['cost_price'] ?>" required>
            </div>

            <div class="form-group">
                <label for="selling_price">Selling Price</label>
                <input type="number" id="selling_price" name="selling_price" min="0" step="0.01" value="<?= $product['selling_price'] ?>" required>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Stock Quantity</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="<?= $product['stock_quantity'] ?>" required>
            </div>

            <div class="form-group">
                <label for="is_active">
                    <input type="checkbox" id="is_active" name="is_active" value="1" <?= $product['is_active'] == 1 ? 'checked' : '' ?>> Active
                </label>
            </div>

            <button type="submit">Update Product</button>
        </form>
    </div>
</main>

<?php include 'includes/footer.php'; ?>