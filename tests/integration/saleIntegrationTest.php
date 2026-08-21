<?php

/**
 * Integration tests for the Sale class.
 *
 * Ref: PHPUnit Documentation - https://docs.phpunit.de/
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../classes/sale.php';

class SaleIntegrationTest extends TestCase
{
    private PDO $conn;

    private int $userId;
    private int $categoryId;
    private int $productId;
    private int $saleId;

    protected function setUp(): void
    {
        // Connect to the test database
        $this->conn = require __DIR__ . '/database.php';

        // Add a test user
        $userQuery = $this->conn->prepare(
            'INSERT INTO users (name, username, password, role, is_active)
             VALUES (:name, :username, :password, :role, :is_active)'
        );

        $userQuery->execute([
            'name' => 'Test User',
            'username' => 'testuser',
            'password' => 'password',
            'role' => 'admin',
            'is_active' => 1
        ]);

        $this->userId = $this->conn->lastInsertId();

        // Add a test category
        $categoryQuery = $this->conn->prepare(
            'INSERT INTO categories (name, description, is_active)
             VALUES (:name, :description, :is_active)'
        );

        $categoryQuery->execute([
            'name' => 'Test Category',
            'description' => 'Integration test category',
            'is_active' => 1
        ]);

        $this->categoryId = $this->conn->lastInsertId();

        // Add a test product
        $productQuery = $this->conn->prepare(
            'INSERT INTO products (category_id, name, cost_price, selling_price, stock_quantity, sale_unit, is_active)
            VALUES (:category_id, :name, :cost_price, :selling_price, :stock_quantity, :sale_unit, :is_active)'
        );

        $productQuery->execute([
            'category_id' => $this->categoryId,
            'name' => 'Test Shirt',
            'cost_price' => 1000,
            'selling_price' => 1500,
            'stock_quantity' => 10,
            'sale_unit' => 'item',
            'is_active' => 1
        ]);

        $this->productId = $this->conn->lastInsertId();

        $this->saleId = 0;
    }

    public function testCreateSale(): void
    {
        // Example sale item
        $items = [['id' => $this->productId, 'price' => 1500, 'quantity' => 2]];

        // Create the Sale
        $sale = new Sale($this->conn);

        // Create a sale
        $this->saleId = $sale->create($this->userId, $items, 3000, 'cash', 3000, 0, date('Y-m-d H:i:s'));

        // Check that a sale ID was created
        $this->assertGreaterThan(0, $this->saleId);

        // Check the sale record
        $saleQuery = $this->conn->prepare(
            'SELECT id, total_amount, payment_method
             FROM sales
             WHERE id = :id'
        );

        $saleQuery->execute(['id' => $this->saleId]);

        $saleData = $saleQuery->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($saleData);
        $this->assertEquals(3000, $saleData['total_amount']);
        $this->assertEquals('cash', $saleData['payment_method']);

        // Check the sale item
        $itemQuery = $this->conn->prepare(
            'SELECT product_id, quantity, unit_price, subtotal
             FROM sale_items
             WHERE sale_id = :sale_id'
        );

        $itemQuery->execute([
            'sale_id' => $this->saleId
        ]);

        $itemData = $itemQuery->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($itemData);
        $this->assertEquals($this->productId, $itemData['product_id']);
        $this->assertEquals(2, $itemData['quantity']);
        $this->assertEquals(1500, $itemData['unit_price']);
        $this->assertEquals(3000, $itemData['subtotal']);

        // Check that product stock was reduced
        $stockQuery = $this->conn->prepare(
            'SELECT stock_quantity
             FROM products
             WHERE id = :id'
        );

        $stockQuery->execute([
            'id' => $this->productId
        ]);

        $stock = $stockQuery->fetchColumn();

        // Stock should change from 10 to 8
        $this->assertEquals(8, $stock);
    }

    protected function tearDown(): void
    {
        // Remove sale items and sale
        if ($this->saleId > 0) {
            $deleteItems = $this->conn->prepare(
                'DELETE FROM sale_items
                 WHERE sale_id = :sale_id'
            );

            $deleteItems->execute(['sale_id' => $this->saleId]);

            $deleteSale = $this->conn->prepare(
                'DELETE FROM sales
                 WHERE id = :id'
            );

            $deleteSale->execute(['id' => $this->saleId]);
        }

        // Remove test product
        if (isset($this->productId)) {
            $deleteProduct = $this->conn->prepare(
                'DELETE FROM products
                 WHERE id = :id'
            );

            $deleteProduct->execute(['id' => $this->productId]);
        }

        // Remove test category
        if (isset($this->categoryId)) {
            $deleteCategory = $this->conn->prepare(
                'DELETE FROM categories
                 WHERE id = :id'
            );

            $deleteCategory->execute(['id' => $this->categoryId]);
        }

        // Remove test user
        if (isset($this->userId)) {
            $deleteUser = $this->conn->prepare(
                'DELETE FROM users
                 WHERE id = :id'
            );

            $deleteUser->execute(['id' => $this->userId]);
        }
    }

    // Test sale rollback when stock is not enough
    public function testCreateSaleRollsBackWhenStockIsNotEnough(): void
    {
        // Example sale item with quantity above available stock
        $items = [['id' => $this->productId, 'price' => 1500, 'quantity' => 20]];

        // Create the Sale
        $sale = new Sale($this->conn);

        try {
            $sale->create($this->userId, $items, 30000, 'cash', 30000, 0, date('Y-m-d H:i:s'));

            $this->fail('Expected an exception because stock is not enough.');
        } catch (Exception $e) {
            // Check the correct error
            $this->assertEquals('Not enough stock available.', $e->getMessage());
        }

        // Check that no sale was created
        $saleQuery = $this->conn->query(
            'SELECT COUNT(*) FROM sales'
        );

        $saleCount = $saleQuery->fetchColumn();

        $this->assertEquals(0, $saleCount);

        // Check that no sale item was created
        $itemQuery = $this->conn->query(
            'SELECT COUNT(*) FROM sale_items'
        );

        $itemCount = $itemQuery->fetchColumn();

        $this->assertEquals(0, $itemCount);

        // Check that stock is still unchanged
        $stockQuery = $this->conn->prepare(
            'SELECT stock_quantity
            FROM products
            WHERE id = :id'
        );

        $stockQuery->execute(['id' => $this->productId]);

        $stock = $stockQuery->fetchColumn();

        $this->assertEquals(10, $stock);
    }


    // Test sale rollback when the product cannot be found
    public function testCreateSaleRollsBackWhenProductNotFound(): void
    {
        // Example sale item with a product ID that does not exist
        $items = [['id' => 999999, 'price' => 1500, 'quantity' => 1]];

        // Create the Sale
        $sale = new Sale($this->conn);

        try {
            $sale->create($this->userId, $items, 1500, 'cash', 1500, 0, date('Y-m-d H:i:s'));

            $this->fail('Expected an exception because the product was not found.');
        } catch (Exception $e) {
            // Check the correct error
            $this->assertEquals('Product not found.', $e->getMessage());
        }

        // Check that no sale was created
        $saleQuery = $this->conn->query(
            'SELECT COUNT(*) FROM sales'
        );

        $saleCount = $saleQuery->fetchColumn();

        $this->assertEquals(0, $saleCount);

        // Check that no sale item was created
        $itemQuery = $this->conn->query(
            'SELECT COUNT(*) FROM sale_items'
        );

        $itemCount = $itemQuery->fetchColumn();

        $this->assertEquals(0, $itemCount);

        // Check that the original test product stock is unchanged
        $stockQuery = $this->conn->prepare(
            'SELECT stock_quantity
            FROM products
            WHERE id = :id'
        );

        $stockQuery->execute(['id' => $this->productId]);

        $stock = $stockQuery->fetchColumn();

        $this->assertEquals(10, $stock);
    }
}
