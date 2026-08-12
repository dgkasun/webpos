<?php

/**
 * Handles product requests between the model and views.
 */
class ProductController
{
    private Product $productManager;

    // Receive the Product model.
    public function __construct(Product $productManager)
    {
        $this->productManager = $productManager;
    }

    public function index(): void
    {
        $message = '';
        $error = '';

        $search = trim($_GET['search'] ?? '');

        $categories = $this->productManager->getActiveCategories();

        // Process the add product request.
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $categoryId = $_POST['category_id'] ?? 0;
            $costPrice = $_POST['cost_price'] ?? '';
            $sellingPrice = $_POST['selling_price'] ?? '';
            $stockQuantity = $_POST['stock_quantity'] ?? '';

            if (
                $name === '' ||
                $categoryId <= 0 ||
                $sellingPrice === '' ||
                $costPrice === '' ||
                $stockQuantity === ''
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
                    $this->productManager->create($categoryId, $name, $costPrice, $sellingPrice, $stockQuantity);

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

        if ($search !== '') {
            // Search products
            $products = $this->productManager->search($search);
        } else {
            // Get product data.
            $products = $this->productManager->getAll();
        }

        $pageTitle = 'Products';
        $currentPage = 'Products';

        // Load the view.
        require __DIR__ . '/../views/products/index.php';
    }


    public function edit(int $productId): void
    {
        $message = '';
        $error = '';

        // Get product and category data.
        $categories = $this->productManager->getActiveCategories();
        $product = $this->productManager->find($productId);

        if (!$product) {
            exit('Product not found.');
        }

        // Process the update product request.
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
                try {
                    $this->productManager->update($productId, $categoryId, $name, $costPrice, $sellingPrice, $stockQuantity, $isActive);
                    $message = 'Product updated successfully.';
                    $product = $this->productManager->find($productId);
                } catch (PDOException $e) {
                    if ($e->getCode() === '23000') {
                        $error = 'This product already exists.';
                    } else {
                        $error = 'Unable to update the product.';
                    }
                }
            }
        }

        $pageTitle = 'Edit Product';
        $currentPage = 'Products';

        // Load the edit view.
        require __DIR__ . '/../views/products/edit.php';
    }
}
