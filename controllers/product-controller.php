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

        // Get product data.
        $products = $this->productManager->getAll();

        $pageTitle = 'Products';
        $currentPage = 'Products';

        // Load the view.
        require __DIR__ . '/../views/products/index.php';
    }
}
