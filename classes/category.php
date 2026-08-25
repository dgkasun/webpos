<?php

/**
 * Handles category data and database operations.
 * The database connection is passed through the constructor.
 * 
 * Ref: Fowler, M. (2004)
 */

class Category
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Get all categories
    public function getAll(): array
    {
        $categoryQuery = $this->conn->query(
            'SELECT id, name, is_active
             FROM categories
             ORDER BY name ASC'
        );
        return $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new category
    public function create(string $name): void
    {
        $categoryQuery = $this->conn->prepare(
            'INSERT INTO categories (name) VALUES (:name)'
        );
        $categoryQuery->execute([
            'name' => $name,
        ]);
    }

    // Find a category by ID
    public function find(int $id): array|false
    {
        $categoryQuery = $this->conn->prepare(
            'SELECT id, name, is_active
             FROM categories
             WHERE id = :id'
        );

        $categoryQuery->execute([
            'id' => $id,
        ]);

        return $categoryQuery->fetch(PDO::FETCH_ASSOC);
    }

    // Update a category
    public function update(int $id, string $name, int $isActive): void
    {
        $categoryQuery = $this->conn->prepare(
            'UPDATE categories
             SET
                name = :name,
                is_active = :is_active
             WHERE id = :id'
        );

        $categoryQuery->execute([
            'name' => $name,
            'is_active' => $isActive,
            'id' => $id,
        ]);
    }

    // Get the total number of categories
    public function getCount(): int
    {
        $categoryQuery = $this->conn->query(
            'SELECT COUNT(*) FROM categories'
        );
        return (int) $categoryQuery->fetchColumn();
    }
}
