<?php

/**
 * Handles POS requests.
 * 
 * Ref: Fowler, M. (2003) - https://sar.ac.id/stmik_ebook/prog_file_file/EFCofwzsj0.pdf
 */

class PosController
{
    private Product $productManager;
    private Cart $cart;

    public function __construct(
        Product $productManager,
        Cart $cart
    ) {
        $this->productManager = $productManager;
        $this->cart = $cart;
    }

    public function index(): void
    {
        $error = '';

        // Get products available for sale
        $products = $this->productManager->getAvailableForSale();

        // Process POS actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'add') {
                $productId = $_POST['product_id'] ?? 0;
                $quantity = $_POST['quantity'] ?? 1;

                if ($productId <= 0 || $quantity <= 0) {
                    $error = 'Please select a product and valid quantity.';
                } else {
                    $product = $this->productManager->findAvailable($productId);
                    if (!$product) {
                        $error = 'Product not found.';
                    } else {
                        $error = $this->cart->add($product, $quantity);
                    }
                }
            }

            if ($action === 'update') {
                $quantities = $_POST['quantities'] ?? [];
                $error = $this->cart->update($quantities);
            }

            if ($action === 'remove') {
                $productId = ($_POST['product_id'] ?? 0);
                $this->cart->remove($productId);
            }

            if ($action === 'clear') {
                $this->cart->clear();
            }
        }

        // Get cart data
        $cartItems = $this->cart->getItems();
        $cartTotal = $this->cart->getTotal();

        $pageTitle = 'POS';

        // Load the POS view
        require __DIR__ . '/../views/pos/index.php';
    }
}
