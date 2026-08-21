<?php

/**
 * Unit tests for the Setting class.
 *
 * Ref: PHPUnit Documentation - https://docs.phpunit.de/
 * Ref: Backend Tea, "Mastering PHPUnit: Using Mocks and Stubs" - https://backendtea.com/post/phpunit-mock-and-stub/
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/settings.php';

class SettingTest extends TestCase
{
    // Test getting shop settings
    public function testGetSettings(): void
    {
        // Example shop settings
        $settingData = ['shop_name' => 'Kasun Textiles', 'address' => 'Gampaha', 'phone' => '0771234567', 'print_footer' => 'Thank you'];

        // Mock the database query
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn($settingData);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Setting
        $setting = new Setting($conn);

        // Get shop settings
        $result = $setting->get();

        // Check the returned shop name
        $this->assertEquals('Kasun Textiles', $result['shop_name']);
    }

    // Test when shop settings are not available
    public function testGetSettingsReturnsFalse(): void
    {
        // Mock the database query and return no settings
        $query = $this->createMock(PDOStatement::class);
        $query->method('fetch')->willReturn(false);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);
        $conn->method('query')->willReturn($query);

        // Create the Setting
        $setting = new Setting($conn);

        // Get shop settings
        $result = $setting->get();

        // Check that false is returned
        $this->assertFalse($result);
    }

    // Test updating shop settings
    public function testSaveUpdatesSettings(): void
    {
        // Existing settings
        $existingSettings = ['shop_name' => 'Old Shop', 'address' => 'Old Address', 'phone' => '0111111111', 'print_footer' => 'Old footer'];

        // Mock the get() query
        $getQuery = $this->createMock(PDOStatement::class);
        $getQuery->method('fetch')->willReturn($existingSettings);

        // Mock the update query
        $updateQuery = $this->createMock(PDOStatement::class);

        $updateQuery->expects($this->once())
            ->method('execute')
            ->with(['shop_name' => 'Kasun Textiles', 'address' => 'Gampaha', 'phone' => '0771234567', 'print_footer' => 'Thank you']);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);

        $conn->method('query')->willReturn($getQuery);

        $conn->method('prepare')->willReturn($updateQuery);

        // Create the Setting
        $setting = new Setting($conn);

        // Save updated settings
        $setting->save('Kasun Textiles', 'Gampaha', '0771234567', 'Thank you');
    }

    // Test inserting settings for the first time
    public function testSaveInsertsSettings(): void
    {
        // Mock the get() query and return no settings
        $getQuery = $this->createMock(PDOStatement::class);
        $getQuery->method('fetch')->willReturn(false);

        // Mock the insert query
        $insertQuery = $this->createMock(PDOStatement::class);

        $insertQuery->expects($this->once())
            ->method('execute')
            ->with(['shop_name' => 'Kasun Textiles', 'address' => 'Gampaha', 'phone' => '0771234567', 'print_footer' => 'Thank you']);

        // Mock the database connection
        $conn = $this->createMock(PDO::class);

        $conn->method('query')->willReturn($getQuery);

        $conn->method('prepare')->willReturn($insertQuery);

        // Create the Setting
        $setting = new Setting($conn);

        // Save new settings
        $setting->save('Kasun Textiles', 'Gampaha', '0771234567', 'Thank you');
    }
}
