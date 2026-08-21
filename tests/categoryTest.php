<?php

/**
 * Unit tests for the Category class.
 *
 * Ref: PHPUnit Documentation - https://docs.phpunit.de/
 * Ref: Backend Tea, "Mastering PHPUnit: Using Mocks and Stubs" - https://backendtea.com/post/phpunit-mock-and-stub/
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/category.php';

class CategoryTest extends TestCase
{
    // Test getting all categories
    public function testGetAllCategories(): void
    {
        // Example categories
        $categoryData = [
            ['id' => 1, 'name' => 'Shirts', 'description' => 'Men shirts', 'is_active' => 1],
            ['id' => 2, 'name' => 'Trousers', 'description' => 'Men trousers', 'is_active' => 1],
            ['id' => 3, 'name' => 'Denim', 'description' => '', 'is_active' => 1],
            ['id' => 4, 'name' => 'Belts', 'description' => '', 'is_active' => 0]
        ];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchAll')->willReturn($categoryData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Get all categories
        $result = $category->getAll();

        // Check that two categories are returned
        $this->assertCount(4, $result);
    }

    // Test creating a category
    public function testCreateCategory(): void
    {
        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['name' => 'Shirts', 'description' => 'Men shirts']);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Create a new category
        $category->create('Shirts', 'Men shirts');

        // Confirm the test reached this point
        $this->assertTrue(true);
    }

    // Test creating a category without a description
    public function testCreateCategoryWithEmptyDescription(): void
    {
        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['name' => 'Shirts', 'description' => null]);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Create a category without a description
        $category->create('Shirts', '');

        // Confirm the test reached this point
        $this->assertTrue(true);
    }

    // Test finding a category by ID
    public function testFindCategory(): void
    {
        // Example category
        $categoryData = ['id' => 1, 'name' => 'Shirts', 'description' => 'Men shirts', 'is_active' => 1];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn($categoryData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Find the example category
        $result = $category->find(1);

        // Check that the correct category is returned
        $this->assertEquals('Shirts', $result['name']);
    }

    // Test when a category cannot be found
    public function testFindCategoryReturnsFalse(): void
    {
        // Mock the database query and return no category
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn(false);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Search for a category that does not exist
        $result = $category->find(999);

        // Check that false is returned
        $this->assertFalse($result);
    }

    // Test updating a category
    public function testUpdateCategory(): void
    {
        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['name' => 'Casual Shirts', 'description' => 'Updated description', 'is_active' => 1, 'id' => 1]);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Update the category
        $category->update(1, 'Casual Shirts', 'Updated description', 1);

        // Confirm the test reached this point
        $this->assertTrue(true);
    }

    // Test updating a category without a description
    public function testUpdateCategoryWithEmptyDescription(): void
    {
        // Mock the database query
        $query = $this->createMock(PDOStatement::class);

        $query->expects($this->once())
            ->method('execute')
            ->with(['name' => 'Casual Shirts', 'description' => null, 'is_active' => 0, 'id' => 1]);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Update the category without a description
        $category->update(1, 'Casual Shirts', '', 0);

        // Confirm the test reached this point
        $this->assertTrue(true);
    }

    // Test getting the total number of categories
    public function testGetCategoryCount(): void
    {
        // Mock the database query and return category count
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetchColumn')->willReturn(5);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Category
        $category = new Category($conn);

        // Get the category count
        $result = $category->getCount();

        // Check that the correct count is returned
        $this->assertEquals(5, $result);
    }
}
