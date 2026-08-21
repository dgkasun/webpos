<?php

/**
 * Unit tests for the Product class.
 *
 * Ref: PHPUnit Documentation - https://docs.phpunit.de/
 * Ref: Backend Tea, "Mastering PHPUnit: Using Mocks and Stubs" - https://backendtea.com/post/phpunit-mock-and-stub/
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/product.php';

class ProductTest extends TestCase
{
    // Test getting active categories
    public function testGetActiveCategories(): void
    {
        // Example active categories
        $categoryData = [
            ['id' => 1, 'name' => 'Shirts'],
            ['id' => 2, 'name' => 'Trousers']
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchAll')->willReturn($categoryData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Get active categories
        $result = $product->getActiveCategories();

        // Check that two categories are returned
        $this->assertCount(2, $result);
    }

    // Test creating a product and generating a barcode
    public function testCreateProduct(): void
    {
        // Mock the insert query
        $insertQuery = $this->createMock(PDOStatement::class);

        $insertQuery->expects($this->once())
            ->method('execute')
            ->with([
                'category_id' => 1,
                'name' => 'Blue Shirt',
                'cost_price' => 1500,
                'selling_price' => 2000,
                'stock_quantity' => 10,
                'sale_unit' => 'item'
            ]);

        // Mock the barcode update query
        $barcodeQuery = $this->createMock(PDOStatement::class);

        $barcodeQuery->expects($this->once())
            ->method('execute')
            ->with(['barcode' => 'WEB00000001', 'id' => '1']);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);

        $conn->method('prepare')->willReturnOnConsecutiveCalls($insertQuery, $barcodeQuery);

        $conn->method('lastInsertId')->willReturn('1');

        // Create the Product
        $product = new Product($conn);

        // Create a new product
        $product->create(1, 'Blue Shirt', 1500, 2000, 10, 'item');

        // Confirm the test completed
        $this->assertTrue(true);
    }


    // Test finding a product by ID
    public function testFindProduct(): void
    {
        // Example product
        $productData = [
            'id' => 1,
            'category_id' => 1,
            'name' => 'Blue Shirt',
            'barcode' => 'WEB00000001',
            'cost_price' => 1500,
            'selling_price' => 2000,
            'stock_quantity' => 10,
            'sale_unit' => 'item',
            'is_active' => 1
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn($productData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Find the example product
        $result = $product->find(1);

        // Check that the correct product is returned
        $this->assertEquals('Blue Shirt', $result['name']);
    }

    // Test when a product cannot be found
    public function testFindProductReturnsFalse(): void
    {
        // Mock the database query and return no product
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn(false);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Search for a product that does not exist
        $result = $product->find(999);

        // Check that false is returned
        $this->assertFalse($result);
    }

    // Test updating a product
    public function testUpdateProduct(): void
    {
        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with([
                'category_id' => 2,
                'name' => 'Casual Blue Shirt',
                'cost_price' => 1600,
                'selling_price' => 2200,
                'stock_quantity' => 15,
                'is_active' => 1,
                'id' => 1,
                'sale_unit' => 'item'
            ]);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Update the product
        $product->update(1, 2, 'Casual Blue Shirt', 1600, 2200, 15, 'item', 1);

        // Confirm the test completed
        $this->assertTrue(true);
    }

    // Test getting the total number of products
    public function testGetCountProduct(): void
    {
        // Mock the database query and return product count
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchColumn')->willReturn(8);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Get the product count
        $result = $product->getCount();

        // Check the product count
        $this->assertEquals(8, $result);
    }

    // Test getting the number of low-stock products
    public function testGetLowStockCount(): void
    {
        // Mock the database query and return low-stock count
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchColumn')->willReturn(3);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Get the low-stock product count
        $result = $product->getLowStockCount(5);

        // Check the product count
        $this->assertEquals(3, $result);
    }

    // Test getting products available for sale
    public function testGetAvailableForSale(): void
    {
        // Example available products
        $productData = [
            ['id' => 1, 'name' => 'Blue Shirt', 'selling_price' => 2000, 'stock_quantity' => 10, 'barcode' => 'WEB00000001', 'sale_unit' => 'item'],
            ['id' => 2, 'name' => 'Black Trousers', 'selling_price' => 3500, 'stock_quantity' => 5, 'barcode' => 'WEB00000002', 'sale_unit' => 'item']
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchAll')->willReturn($productData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Get products available for sale
        $result = $product->getAvailableForSale();

        // Check the number of products
        $this->assertCount(2, $result);
    }


    // Test finding an active product by ID
    public function testFindAvailableProduct(): void
    {
        // Example available product
        $productData = ['id' => 1, 'name' => 'Blue Shirt', 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item'];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn($productData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Find the available product
        $result = $product->findAvailable(1);

        // Check the returned product
        $this->assertEquals('Blue Shirt', $result['name']);
    }


    // Test when an available product cannot be found
    public function testFindAvailableProductReturnsFalse(): void
    {
        // Mock the database query and return no product
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn(false);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Search for a product that is not available
        $result = $product->findAvailable(999);

        // Check that false is returned
        $this->assertFalse($result);
    }


    // Test getting paginated products without search
    public function testGetPaginatedProducts(): void
    {
        // Example products
        $productData = [
            ['id' => 1, 'name' => 'Blue Shirt', 'barcode' => 'WEB00000001', 'cost_price' => 1500, 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item', 'is_active' => 1, 'category_name' => 'Shirts'],
            ['id' => 2, 'name' => 'Black Trousers', 'barcode' => 'WEB00000002', 'cost_price' => 2500, 'selling_price' => 3500, 'stock_quantity' => 5, 'sale_unit' => 'item', 'is_active' => 1, 'category_name' => 'Trousers']
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchAll')->willReturn($productData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Get paginated products
        $result = $product->getPaginated('', 10, 0);

        // Check that two products are returned
        $this->assertCount(2, $result);
    }


    // Test getting paginated products with search
    public function testGetPaginatedProductsWithSearch(): void
    {
        // Example search result
        $productData = [
            ['id' => 1, 'name' => 'Blue Shirt', 'barcode' => 'WEB00000001', 'cost_price' => 1500, 'selling_price' => 2000, 'stock_quantity' => 10, 'sale_unit' => 'item', 'is_active' => 1, 'category_name' => 'Shirts']
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['search' => '%Blue%']);

        $query->method('fetchAll')->willReturn($productData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Search paginated products
        $result = $product->getPaginated('Blue', 10, 0);

        // Check that one product is returned
        $this->assertCount(1, $result);
    }

    // Test getting the filtered product count without search
    public function testGetFilteredCount(): void
    {
        // Mock the database query and return product count
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchColumn')->willReturn(8);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Get the product count without search
        $result = $product->getFilteredCount('');

        // Check that the correct count is returned
        $this->assertEquals(8, $result);
    }


    // Test getting the filtered product count with search
    public function testGetFilteredCountWithSearch(): void
    {
        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['search' => '%Blue%']);

        $query->method('fetchColumn')->willReturn(1);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Product
        $product = new Product($conn);

        // Get the filtered product count
        $result = $product->getFilteredCount('Blue');

        // Check that the correct count is returned
        $this->assertEquals(1, $result);
    }
}
