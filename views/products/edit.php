<?php

/** @var string $error */
/** @var array $product */
/** @var array $categories */
/** @var string $message */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">

    <?php include __DIR__ . '/../../includes/menu.php'; ?>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>Edit Product</h1>
            </div>
            <a href="products.php">Back to Products</a>
        </div>

        <?php if ($message !== ''): ?>
            <p class="success">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" maxlength="150" value="<?= htmlspecialchars($product['name']) ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= $product['category_id'] === $category['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
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

<?php include __DIR__ . '/../../includes/footer.php'; ?>