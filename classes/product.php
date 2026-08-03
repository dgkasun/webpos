<?php

/**
 * Handles product related database operations.
 * The database connection is provided through constructor injection,
 * Ref: Fowler, M. (2004) - https://martinfowler.com/articles/injection.html
 */

class Product
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll(): array
    {
        $productQuery = $this->conn->query(
            'SELECT id, products.name, products.barcode, products.cost_price, products.selling_price, products.stock_quantity, products.is_active, categories.name AS category_name
             FROM products
             INNER JOIN categories
                ON categories.id = products.category_id
             ORDER BY products.name ASC'
        );
        return $productQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActiveCategories(): array
    {
        $categoryQuery = $this->conn->query(
            'SELECT id, name
             FROM categories
             WHERE is_active = 1
             ORDER BY name ASC'
        );
        return $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(int $categoryId, string $name, float $costPrice, float $sellingPrice, int $stockQuantity): void
    {
        $productQuery = $this->conn->prepare(
            'INSERT INTO products (category_id, name, cost_price, selling_price, stock_quantity) 
             VALUES (:category_id, :name, :cost_price, :selling_price, :stock_quantity)'
        );
        $productQuery->execute([
            'category_id' => $categoryId,
            'name' => $name,
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock_quantity' => $stockQuantity,
        ]);
        $productId = $this->conn->lastInsertId();

        $barcode = 'WEB' . str_pad($productId, 8, '0', STR_PAD_LEFT);
        $barcodeQuery = $this->conn->prepare(
            'UPDATE products
             SET barcode = :barcode
             WHERE id = :id'
        );
        $barcodeQuery->execute([
            'barcode' => $barcode,
            'id' => $productId,
        ]);
    }

    public function find(int $id): array|false
    {
        $productQuery = $this->conn->prepare(
            'SELECT id, category_id, name, barcode, cost_price, selling_price, stock_quantity, is_active
             FROM products
             WHERE id = :id'
        );
        $productQuery->execute([
            'id' => $id,
        ]);
        return $productQuery->fetch(PDO::FETCH_ASSOC);
    }

    public function update(int $id, int $categoryId, string $name, float $costPrice, float $sellingPrice, int $stockQuantity, int $isActive): void
    {
        $productQuery = $this->conn->prepare(
            'UPDATE products
             SET
                category_id = :category_id,
                name = :name,
                cost_price = :cost_price,
                selling_price = :selling_price,
                stock_quantity = :stock_quantity,
                is_active = :is_active
             WHERE id = :id'
        );
        $productQuery->execute([
            'category_id' => $categoryId,
            'name' => $name,
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock_quantity' => $stockQuantity,
            'is_active' => $isActive,
            'id' => $id,
        ]);
    }
}
