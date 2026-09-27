<?php

/**
 * Unit tests for the Cart class.
 *
 * Ref: PHPUnit Documentation - https://docs.phpunit.de/
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/cart.php';

class CartTest extends TestCase
{
    protected function setUp(): void
    {
        // Reset the cart before each test
        $_SESSION['cart'] = [];
        unset($_SESSION['sale_started_at']);
    }

    // Test adding a product to the cart
    public function testAddProduct(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add product to the cart
        $result = $cart->add($productData, 2);

        // Get cart items
        $items = $cart->getItems();

        // Check that there is no error
        $this->assertEquals('', $result);

        // Check the product quantity
        $this->assertEquals(2, $items[1]['quantity']);

        // Check the product name
        $this->assertEquals('Blue Shirt', $items[1]['name']);

        // Check the product category
        $this->assertEquals('Shirts', $items[1]['category_name']);
    }

    // Test adding the same product more than once
    public function testAddSameProductTwice(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add the same product twice
        $cart->add($productData, 2);
        $cart->add($productData, 3);

        // Get cart items
        $items = $cart->getItems();

        // Check the total quantity
        $this->assertEquals(5, $items[1]['quantity']);

        // Check that there is still only one cart item
        $this->assertCount(1, $items);
    }

    // Test adding more quantity than available stock
    public function testAddProductExceedsStock(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'selling_price' => 2000, 'stock_quantity' => 5, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Try to add more than available stock
        $result = $cart->add($productData, 6);

        // Get cart items
        $items = $cart->getItems();

        // Check the error message
        $this->assertEquals('Not enough stock available.', $result);

        // Check that the product was not added
        $this->assertEmpty($items);
    }

    // Test updating product quantity
    public function testUpdateProductQuantity(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add product to the cart
        $cart->add($productData, 2);

        // Update the quantity
        $result = $cart->update([1 => 5]);

        // Get cart items
        $items = $cart->getItems();

        // Check that there is no error
        $this->assertEquals('', $result);

        // Check the updated quantity
        $this->assertEquals(5, $items[1]['quantity']);
    }

    // Test updating quantity above available stock
    public function testUpdateProductExceedsStock(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 5, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add product to the cart
        $cart->add($productData, 2);

        // Try to update above available stock
        $result = $cart->update([1 => 6]);

        // Get cart items
        $items = $cart->getItems();

        // Check the error message
        $this->assertEquals('One or more quantities exceed available stock.', $result);

        // Check that the original quantity is unchanged
        $this->assertEquals(2, $items[1]['quantity']);
    }

    // Test removing a product by updating quantity to zero
    public function testUpdateProductQuantityToZero(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add product to the cart
        $cart->add($productData, 2);

        // Update the quantity to zero
        $result = $cart->update([1 => 0]);

        // Get cart items
        $items = $cart->getItems();

        // Check that there is no error
        $this->assertEquals('', $result);

        // Check that the product was removed
        $this->assertEmpty($items);
    }

    // Test removing a product from the cart
    public function testRemoveProduct(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add product to the cart
        $cart->add($productData, 2);

        // Remove the product
        $cart->remove(1);

        // Get cart items
        $items = $cart->getItems();

        // Check that the cart is empty
        $this->assertEmpty($items);

        // Check that the sale timer is removed
        $this->assertArrayNotHasKey('sale_started_at', $_SESSION);
    }

    // Test clearing all products from the cart
    public function testClearCart(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add product to the cart
        $cart->add($productData, 2);

        // Clear the cart
        $cart->clear();

        // Check that the cart is empty
        $this->assertEmpty($cart->getItems());

        // Check that the sale timer is removed
        $this->assertArrayNotHasKey('sale_started_at', $_SESSION);
    }

    // Test calculating the cart total
    public function testGetTotal(): void
    {
        // Example products
        $productOne = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];
        $productTwo = ['id' => 2, 'name' => 'Black Trousers', 'category_name' => 'Shirts', 'selling_price' => 3000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add products to the cart
        $cart->add($productOne, 2);
        $cart->add($productTwo, 1);

        // Get the cart total
        $result = $cart->getTotal();

        // Check the calculated total
        $this->assertEquals(7000, $result);
    }

    // Test checking if the cart is empty
    public function testIsEmpty(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Check that a new cart is empty
        $this->assertTrue($cart->isEmpty());

        // Add product to the cart
        $cart->add($productData, 1);

        // Check that the cart is not empty
        $this->assertFalse($cart->isEmpty());
    }

    // Test getting the number of cart items
    public function testGetItemCount(): void
    {
        // Example products
        $productOne = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];
        $productTwo = ['id' => 2, 'name' => 'Black Trousers', 'category_name' => 'Shirts', 'selling_price' => 3000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add two different products
        $cart->add($productOne, 2);
        $cart->add($productTwo, 3);

        // Get the number of cart items
        $result = $cart->getItemCount();

        // Check the item count
        $this->assertEquals(2, $result);
    }

    // Test getting the total product quantity
    public function testGetTotalQuantity(): void
    {
        // Example products
        $productOne = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];
        $productTwo = ['id' => 2, 'name' => 'Black Trousers', 'category_name' => 'Shirts', 'selling_price' => 3000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Add products with different quantities
        $cart->add($productOne, 2);
        $cart->add($productTwo, 3);

        // Get the total quantity
        $result = $cart->getTotalQuantity();

        // Check the total quantity
        $this->assertEquals(5, $result);
    }

    // Test starting the sale timer when first product is added
    public function testSaleTimerStartsWhenFirstProductAdded(): void
    {
        // Example product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'category_name' => 'Shirts', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Create the Cart
        $cart = new Cart();

        // Check that the timer does not exist before adding a product
        $this->assertArrayNotHasKey('sale_started_at', $_SESSION);

        // Add the first product
        $cart->add($productData, 1);

        // Check that the sale timer was created
        $this->assertArrayHasKey('sale_started_at', $_SESSION);
    }
}
