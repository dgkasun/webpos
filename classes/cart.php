<?php

/**
 * Handles shopping cart operations for the POS system.
 * The cart is stored in the current user session.
 */

class Cart
{
    public function __construct()
    {
        // Create an empty cart if one does not exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    // Add a product to the cart
    public function add(array $product, float $quantity)
    {
        $productId = $product['id'];

        $existingQuantity = $_SESSION['cart'][$productId]['quantity'] ?? 0;

        $newQuantity = $existingQuantity + $quantity;

        if ($newQuantity > $product['stock_quantity']) {
            return 'Not enough stock available.';
        }

        // Start the sale timer
        if (empty($_SESSION['cart'])) {
            $_SESSION['sale_started_at'] = date('Y-m-d H:i:s');
        }

        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $product['name'],
            'category_name' => $product['category_name'],
            'price' => $product['selling_price'],
            'quantity' => $newQuantity,
            'stock_quantity' => $product['stock_quantity'],
            'sale_unit' => $product['sale_unit'],
        ];

        return '';
    }

    // Update product quantities in the cart
    public function update(array $quantities)
    {
        $error = '';

        foreach ($quantities as $productId => $quantity) {

            if (!isset($_SESSION['cart'][$productId])) {
                continue;
            }

            $availableStock = $_SESSION['cart'][$productId]['stock_quantity'];

            if ($quantity <= 0) {
                unset($_SESSION['cart'][$productId]);
            } elseif ($quantity <= $availableStock) {
                $_SESSION['cart'][$productId]['quantity'] = $quantity;
            } else {
                $error = 'One or more quantities exceed available stock.';
            }
        }

        return $error;
    }

    // Remove a product from the cart
    public function remove(int $productId)
    {
        unset($_SESSION['cart'][$productId]);
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['sale_started_at']);
        }
    }

    // Clear all products from the cart
    public function clear()
    {
        $_SESSION['cart'] = [];
        unset($_SESSION['sale_started_at']);
    }

    // Get all cart items
    public function getItems()
    {
        return $_SESSION['cart'];
    }

    // Calculate the cart total
    public function getTotal()
    {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    // Check if the cart is empty
    public function isEmpty()
    {
        return empty($_SESSION['cart']);
    }

    // Get the number of cart items
    public function getItemCount()
    {
        return count($_SESSION['cart'] ?? []);
    }

    // Get the total quantity in the cart
    public function getTotalQuantity()
    {
        $totalQuantity = 0.0;

        foreach ($_SESSION['cart'] ?? [] as $item) {
            $totalQuantity += $item['quantity'];
        }

        return $totalQuantity;
    }
}
