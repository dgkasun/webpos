<?php

/**
 * Unit tests for the Sale class.
 *
 * Ref: PHPUnit Documentation - https://docs.phpunit.de/
 * Ref: Backend Tea, "Mastering PHPUnit: Using Mocks and Stubs" - https://backendtea.com/post/phpunit-mock-and-stub/
 * Ref: Fowler, M., "Patterns of Enterprise Application Architecture", 2002.
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/sale.php';

class SaleTest extends TestCase
{
    // Test finding a sale by ID
    public function testFindSale(): void
    {
        // Example sale
        $saleData = ['id' => 1, 'total_amount' => 7000, 'payment_method' => 'cash', 'cash_received' => 8000, 'change_amount' => 1000, 'created_at' => '2026-08-21 10:30:00', 'cashier_name' => 'Admin'];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn($saleData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Find the sale
        $result = $sale->find(1);

        // Check the correct sale is returned
        $this->assertEquals(7000, $result['total_amount']);
        $this->assertEquals('Admin', $result['cashier_name']);
    }

    // Test when a sale cannot be found
    public function testFindSaleReturnsFalse(): void
    {
        // Mock the database query and return no sale
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn(false);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Search for a sale that does not exist
        $result = $sale->find(999);

        // Check that false is returned
        $this->assertFalse($result);
    }

    // Test getting items for a sale
    public function testGetSaleItems(): void
    {
        // Example sale items
        $saleItems = [
            ['product_name' => 'Blue Shirt', 'sale_unit' => 'item', 'quantity' => 2, 'unit_price' => 2000, 'subtotal' => 4000],
            ['product_name' => 'Black Trousers', 'sale_unit' => 'item', 'quantity' => 1, 'unit_price' => 3000, 'subtotal' => 3000]
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchAll')->willReturn($saleItems);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Get sale items
        $result = $sale->getItems(1);

        // Check that two items are returned
        $this->assertCount(2, $result);

        // Check the first product
        $this->assertEquals('Blue Shirt', $result[0]['product_name']);
    }

    // Test getting today's sales summary
    public function testGetTodaySummary(): void
    {
        // Example sales summary
        $summaryData = ['sale_count' => 3, 'sales_total' => 15000];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn($summaryData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Get today's sales summary
        $result = $sale->getTodaySummary();

        // Check the sale count
        $this->assertEquals(3, $result['sale_count']);

        // Check the sales total
        $this->assertEquals(15000, $result['sales_total']);
    }

    // Test creating a sale successfully
    public function testCreateSale(): void
    {
        // Example cart item
        $items = [
            ['id' => 1, 'name' => 'Blue Shirt', 'price' => 2000, 'quantity' => 2]
        ];

        // Mock the sale insert query
        $saleQuery = $this->createMock(PDOStatement::class);

        // Mock the product stock query
        $productQuery = $this->createMock(PDOStatement::class);
        $productQuery->method('fetch')->willReturn(['stock_quantity' => 10]);

        // Mock the sale item query
        $saleItemQuery = $this->createMock(PDOStatement::class);

        // Mock the stock update query
        $stockQuery = $this->createMock(PDOStatement::class);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);

        $conn->expects($this->once())->method('beginTransaction');

        $conn->method('prepare')->willReturnOnConsecutiveCalls($saleQuery, $productQuery, $saleItemQuery, $stockQuery);

        $conn->method('lastInsertId')->willReturn('10');

        $conn->expects($this->once())->method('commit');

        // Create the Sale
        $sale = new Sale($conn);

        // Create the sale
        $result = $sale->create(1, $items, 4000, 'cash', 5000, 1000, '2026-08-21 10:00:00');

        // Check the new sale ID
        $this->assertEquals('10', $result);
    }

    // Test creating a sale when there is not enough stock
    public function testCreateSaleFailsWhenStockIsNotEnough(): void
    {
        // Example cart item
        $items = [
            ['id' => 1, 'name' => 'Blue Shirt', 'price' => 2000, 'quantity' => 6]
        ];

        // Mock the sale insert query
        $saleQuery = $this->createMock(PDOStatement::class);

        // Mock the product stock query
        $productQuery = $this->createMock(PDOStatement::class);
        $productQuery->method('fetch')->willReturn(['stock_quantity' => 5]);

        // Mock unused queries
        $saleItemQuery = $this->createMock(PDOStatement::class);
        $stockQuery = $this->createMock(PDOStatement::class);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);

        $conn->expects($this->once())->method('beginTransaction');

        $conn->method('prepare')->willReturnOnConsecutiveCalls($saleQuery, $productQuery, $saleItemQuery, $stockQuery);

        $conn->method('lastInsertId')->willReturn('10');

        // Simulate an active transaction
        $conn->method('inTransaction')->willReturn(true);

        // Check that rollback happens
        $conn->expects($this->once())->method('rollBack');

        // Commit should not happen
        $conn->expects($this->never())->method('commit');

        // Expect the stock error
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not enough stock available.');

        // Create the Sale
        $sale = new Sale($conn);

        // Try to create the sale
        $sale->create(1, $items, 12000, 'cash', 12000, 0, '2026-08-21 10:00:00');
    }

    // Test creating a sale when the product cannot be found
    public function testCreateSaleFailsWhenProductNotFound(): void
    {
        // Example cart item
        $items = [
            ['id' => 999, 'name' => 'Unknown Product', 'price' => 2000, 'quantity' => 1]
        ];

        // Mock the sale insert query
        $saleQuery = $this->createMock(PDOStatement::class);

        // Mock the product query and return no product
        $productQuery = $this->createMock(PDOStatement::class);
        $productQuery->method('fetch')->willReturn(false);

        // Mock unused queries
        $saleItemQuery = $this->createMock(PDOStatement::class);
        $stockQuery = $this->createMock(PDOStatement::class);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);

        $conn->expects($this->once())->method('beginTransaction');

        $conn->method('prepare')->willReturnOnConsecutiveCalls($saleQuery, $productQuery, $saleItemQuery, $stockQuery);

        $conn->method('lastInsertId')->willReturn('10');

        // Simulate an active transaction
        $conn->method('inTransaction')->willReturn(true);

        // Check that rollback happens
        $conn->expects($this->once())->method('rollBack');

        // Commit should not happen
        $conn->expects($this->never())->method('commit');

        // Expect the product error
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found.');

        // Create the Sale
        $sale = new Sale($conn);

        // Try to create the sale
        $sale->create(1, $items, 2000, 'cash', 2000, 0, '2026-08-21 10:00:00');
    }

    // Test getting sales within a date range
    public function testGetSalesByDateRange(): void
    {
        // Example sales
        $saleData = [
            ['id' => 1, 'total_amount' => 7000, 'payment_method' => 'cash', 'created_at' => '2026-08-20 10:30:00', 'cashier_name' => 'Admin', 'transaction_time' => 120],
            ['id' => 2, 'total_amount' => 4500, 'payment_method' => 'card', 'created_at' => '2026-08-21 11:00:00', 'cashier_name' => 'Admin', 'transaction_time' => 90]
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['from_date' => '2026-08-20', 'to_date' => '2026-08-21']);

        $query->method('fetchAll')->willReturn($saleData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Get sales within the date range
        $result = $sale->getByDateRange('2026-08-20', '2026-08-21');

        // Check that two sales are returned
        $this->assertCount(2, $result);
    }

    // Test getting the sales report summary
    public function testGetReportSummary(): void
    {
        // Example sales summary
        $summaryData = ['sale_count' => 3, 'sales_total' => 15000];

        // Example items sold summary
        $itemsData = ['items_sold' => 8];

        // Mock the sales summary query
        $salesQuery = $this->createMock(PDOStatement::class);

        $salesQuery->expects($this->once())
            ->method('execute')
            ->with(['from_date' => '2026-08-20', 'to_date' => '2026-08-21']);

        $salesQuery->method('fetch')->willReturn($summaryData);

        // Mock the items sold query
        $itemsQuery = $this->createMock(PDOStatement::class);

        $itemsQuery->expects($this->once())
            ->method('execute')
            ->with(['from_date' => '2026-08-20', 'to_date' => '2026-08-21']);

        $itemsQuery->method('fetch')->willReturn($itemsData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);

        $conn->method('prepare')->willReturnOnConsecutiveCalls($salesQuery, $itemsQuery);

        // Create the Sale
        $sale = new Sale($conn);

        // Get the report summary
        $result = $sale->getReportSummary('2026-08-20', '2026-08-21');

        // Check the sale count
        $this->assertEquals(3, $result['sale_count']);

        // Check the sales total
        $this->assertEquals(15000, $result['sales_total']);

        // Check the total items sold
        $this->assertEquals(8, $result['items_sold']);
    }


    // Test getting filtered sales
    public function testGetFilteredSales(): void
    {
        // Example filtered sale
        $saleData = [
            ['id' => 5, 'total_amount' => 7000, 'payment_method' => 'cash', 'created_at' => '2026-08-21 10:30:00', 'cashier_name' => 'Admin']
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['from_date' => '2026-08-20', 'to_date' => '2026-08-21', 'sale_id' => 5]);

        $query->method('fetchAll')->willReturn($saleData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Get filtered sales
        $result = $sale->getFiltered('2026-08-20', '2026-08-21', 5, 10, 0);

        // Check that one sale is returned
        $this->assertCount(1, $result);

        // Check the sale ID
        $this->assertEquals(5, $result[0]['id']);
    }

    // Test getting the number of filtered sales
    public function testGetFilteredCount(): void
    {
        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['from_date' => '2026-08-20', 'to_date' => '2026-08-21', 'sale_id' => 5]);

        $query->method('fetchColumn')->willReturn(1);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Get the filtered sale count
        $result = $sale->getFilteredCount('2026-08-20', '2026-08-21', 5);

        // Check the filtered count
        $this->assertEquals(1, $result);
    }


    // Test getting the best selling products
    public function testGetBestSellingProducts(): void
    {
        // Example best selling products
        $productData = [
            ['product_name' => 'Blue Shirt', 'quantity_sold' => 12, 'sales_amount' => 24000],
            ['product_name' => 'Black Trousers', 'quantity_sold' => 8, 'sales_amount' => 28000]
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['from_date' => '2026-08-20', 'to_date' => '2026-08-21']);

        $query->method('fetchAll')->willReturn($productData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Sale
        $sale = new Sale($conn);

        // Get the best selling products
        $result = $sale->getBestSellingProducts('2026-08-20', '2026-08-21');

        // Check that two products are returned
        $this->assertCount(2, $result);

        // Check the first product
        $this->assertEquals('Blue Shirt', $result[0]['product_name']);

        // Check the quantity sold
        $this->assertEquals(12, $result[0]['quantity_sold']);
    }
}
