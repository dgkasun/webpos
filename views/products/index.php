<?php

/** @var string $message */
/** @var string $error */
/** @var array $products */
/** @var array $categories */
/** @var string $search */
/** @var int $page */
/** @var int $totalPages */
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="wrap">

    <?php include __DIR__ . '/../../includes/menu.php'; ?>
    <div class="container">
        <?php include __DIR__ . '/../../includes/page-header.php'; ?>

        <div class="content">
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

            <?php if (empty($categories)): ?>
                <p class="error">
                    Please create at least one active category before
                    adding products.
                </p>
            <?php else: ?>
                <div class="card mb30">
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
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cost_price">Cost Price</label>
                                <input type="number" id="cost_price" name="cost_price" min="0" step="0.01" value="0.00" required>
                            </div>
                            <div class="form-group">
                                <label for="selling_price">Selling Price</label>
                                <input type="number" id="selling_price" name="selling_price" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="stock_quantity">Opening Stock</label>
                                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="sale_unit">Selling Unit</label>
                                <select id="sale_unit" name="sale_unit" required>
                                    <option value="unit">Unit</option>
                                    <option value="metre">Metre</option>
                                    <option value="yard">Yard</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit">Add Product</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="all-products">
                    <div>
                        <h2>Existing Products</h2>
                    </div>
                    <div class="product-search">
                        <form method="get" class="product-search-form">
                            <div class="form-group">
                                <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search Product, barcode or category">
                            </div>
                            <div><button type="submit">Search</button></div>
                            <?php if ($search !== ''): ?>
                                <div><a class="button-link" href="products.php">Clear</a></div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <?php if ($search !== ''): ?>
                        <p>No products found for "<?= htmlspecialchars($search) ?>".</p>
                    <?php else: ?>
                        <p>No products have been added.</p>
                    <?php endif; ?>
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
                                    <tr>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                                        <td>Rs. <?= number_format($product['cost_price'], 2) ?></td>
                                        <td>Rs. <?= number_format($product['selling_price'], 2) ?></td>
                                        <td>
                                            <?php if ($product['sale_unit'] === 'unit'): ?>
                                                <?= number_format($product['stock_quantity'], 0) ?>
                                            <?php else: ?>
                                                <?= number_format($product['stock_quantity'], 2) ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $product['is_active'] ? 'Active' : 'Inactive' ?></td>
                                        <td><a href="edit-product.php?id=<?= $product['id'] ?>">Edit</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <?php if ($totalPages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="products.php?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">
                                        Previous
                                    </a>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <span class="active">
                                            <?= $i ?>
                                        </span>
                                    <?php else: ?>
                                        <a href="products.php?search=<?= urlencode($search) ?>&page=<?= $i ?>">
                                            <?= $i ?>
                                        </a>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <?php if ($page < $totalPages): ?>
                                    <a href="products.php?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">
                                        Next
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>