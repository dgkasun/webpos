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
            'SELECT id, name, description, is_active
             FROM categories
             ORDER BY name ASC'
        );
        return $categoryQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new category
    public function create(string $name, string $description): void
    {
        $categoryQuery = $this->conn->prepare(
            'INSERT INTO categories (name, description) VALUES (:name, :description)'
        );
        $categoryQuery->execute([
            'name' => $name,
            'description' => $description !== '' ? $description : null,
        ]);
    }

    // Find a category by ID
    public function find(int $id): array|false
    {
        $categoryQuery = $this->conn->prepare(
            'SELECT id, name, description, is_active
             FROM categories
             WHERE id = :id'
        );

        $categoryQuery->execute([
            'id' => $id,
        ]);

        return $categoryQuery->fetch(PDO::FETCH_ASSOC);
    }

    // Update a category
    public function update(int $id, string $name, string $description, int $isActive): void
    {
        $categoryQuery = $this->conn->prepare(
            'UPDATE categories
             SET
                name = :name,
                description = :description,
                is_active = :is_active
             WHERE id = :id'
        );

        $categoryQuery->execute([
            'name' => $name,
            'description' => $description !== '' ? $description : null,
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
