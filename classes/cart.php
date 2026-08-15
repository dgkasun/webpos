<?php

/**
 * Handles shopping cart operations for the POS system.
 * The cart is stored in the current user session.
 * Ref: Fowler, M. (2004) - https://martinfowler.com/articles/injection.html
 */
class Cart
{
    public function __construct()
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add(array $product, float $quantity)
    {
        $productId = $product['id'];

        $existingQuantity = $_SESSION['cart'][$productId]['quantity'] ?? 0;

        $newQuantity = $existingQuantity + $quantity;

        if ($newQuantity > $product['stock_quantity']) {
            return 'Not enough stock available.';
        }

        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $product['name'],
            'price' => $product['selling_price'],
            'quantity' => $newQuantity,
            'stock_quantity' => $product['stock_quantity'],
            'sale_unit' => $product['sale_unit'],
        ];

        return '';
    }

    public function update(array $quantities)
    {
        $error = '';

        foreach ($quantities as $productId => $quantity) {
            $productId = $productId;
            $quantity = $quantity;

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

    public function remove(int $productId)
    {
        unset($_SESSION['cart'][$productId]);
    }

    public function clear()
    {
        $_SESSION['cart'] = [];
    }

    public function getItems()
    {
        return $_SESSION['cart'];
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public function isEmpty()
    {
        return empty($_SESSION['cart']);
    }

    public function getItemCount()
    {
        return count($_SESSION['cart'] ?? []);
    }

    public function getTotalQuantity()
    {
        $totalQuantity = 0.0;

        foreach ($_SESSION['cart'] ?? [] as $item) {
            $totalQuantity += $item['quantity'];
        }

        return $totalQuantity;
    }
}
