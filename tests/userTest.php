<?php

/**
 * Unit tests for the User class.
 *
 * Ref: PHPUnit Documentation - https://docs.phpunit.de/
 * Ref: Backend Tea, "Mastering PHPUnit: Using Mocks and Stubs" - https://backendtea.com/post/phpunit-mock-and-stub/
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/user.php';

class UserTest extends TestCase
{

    // Test finding an active user by username
    public function testFindActiveUser(): void
    {
        // Example user
        $userData = ['id' => 1, 'name' => 'Admin', 'username' => 'admin', 'password' => 'password', 'role' => 'admin'];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn($userData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the User
        $user = new User($conn);

        // Find the example user
        $result = $user->findActiveByUsername('admin');

        // Check that the correct username is returned
        $this->assertEquals('admin', $result['username']);
    }

    // Test when an active user cannot be found
    public function testFindActiveUserReturnsFalse(): void
    {
        // Mock the database query and return no user
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn(false);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('prepare')->willReturn($query);

        // Create the User
        $user = new User($conn);

        // Search for a user that does not exist
        $result = $user->findActiveByUsername('unknown');

        // Check that false is returned
        $this->assertFalse($result);
    }
}
