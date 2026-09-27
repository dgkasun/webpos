<?php

/**
 * Handles product data and database operations.
 * The database connection is passed through the constructor.
 * 
 * Ref: Fowler, M. (2004)
 */

class Product
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Get active categories
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

    // Create a new product
    public function create(int $categoryId, string $name, float $costPrice, float $sellingPrice, float $stockQuantity, string $saleUnit): void
    {
        $productQuery = $this->conn->prepare(
            'INSERT INTO products (category_id, name, cost_price, selling_price, stock_quantity, sale_unit) 
             VALUES (:category_id, :name, :cost_price, :selling_price, :stock_quantity, :sale_unit)'
        );
        $productQuery->execute([
            'category_id' => $categoryId,
            'name' => $name,
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock_quantity' => $stockQuantity,
            'sale_unit' => $saleUnit,
        ]);

        // Generate the barcode using the new product ID
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

    // Find a product by ID
    public function find(int $id): array|false
    {
        $productQuery = $this->conn->prepare(
            'SELECT id, category_id, name, barcode, cost_price, selling_price, stock_quantity, sale_unit, is_active
             FROM products
             WHERE id = :id'
        );
        $productQuery->execute([
            'id' => $id,
        ]);
        return $productQuery->fetch(PDO::FETCH_ASSOC);
    }

    // Update a product
    public function update(int $id, int $categoryId, string $name, float $costPrice, float $sellingPrice, float $stockQuantity, string $saleUnit, int $isActive): void
    {
        $productQuery = $this->conn->prepare(
            'UPDATE products
             SET
                category_id = :category_id,
                name = :name,
                cost_price = :cost_price,
                selling_price = :selling_price,
                stock_quantity = :stock_quantity,
                is_active = :is_active,
                sale_unit = :sale_unit
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
            'sale_unit' => $saleUnit,
        ]);
    }

    // Get the total number of products
    public function getCount()
    {
        $productQuery = $this->conn->query(
            'SELECT COUNT(*) FROM products'
        );
        return $productQuery->fetchColumn();
    }

    // Get the number of low-stock products
    public function getLowStockCount($level = 5)
    {
        $productQuery = $this->conn->prepare(
            'SELECT COUNT(*) 
            FROM products 
            WHERE stock_quantity <= :level AND is_active = 1'
        );
        $productQuery->execute([
            'level' => $level,
        ]);
        return $productQuery->fetchColumn();
    }

    // Get active products available for sale
    public function getAvailableForSale()
    {
        $productQuery = $this->conn->query(
            'SELECT p.id, p.name, p.selling_price, p.stock_quantity, p.barcode, p.sale_unit, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1 AND p.stock_quantity > 0
            ORDER BY p.name ASC'
        );
        return $productQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    // Find an active product by ID
    public function findAvailable(int $id)
    {
        $productQuery = $this->conn->prepare(
            'SELECT p.id, p.name, p.selling_price, p.stock_quantity, p.sale_unit, c.name AS category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id AND p.is_active = 1'
        );
        $productQuery->execute([
            'id' => $id,
        ]);
        return $productQuery->fetch(PDO::FETCH_ASSOC);
    }

    // Get products for the current page
    public function getPaginated(string $search, int $limit, int $offset)
    {

        $sql = 'SELECT products.id, products.name, products.barcode, products.cost_price, products.selling_price, products.stock_quantity, products.sale_unit, products.is_active, categories.name AS category_name
                FROM products
                INNER JOIN categories ON categories.id = products.category_id';

        if ($search !== '') {
            $sql .= ' WHERE products.name LIKE :search
                    OR products.barcode LIKE :search
                    OR categories.name LIKE :search';
        }

        $sql .= " ORDER BY products.name ASC
                LIMIT $limit OFFSET $offset";

        $productQuery = $this->conn->prepare($sql);

        if ($search !== '') {
            $productQuery->execute(['search' => '%' . $search . '%',]);
        } else {
            $productQuery->execute();
        }

        return $productQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the number of products matching the search
    public function getFilteredCount(string $search)
    {
        $sql = 'SELECT COUNT(*)
                FROM products
                INNER JOIN categories
                ON categories.id = products.category_id';

        if ($search !== '') {
            $sql .= ' WHERE products.name LIKE :search
                    OR products.barcode LIKE :search
                    OR categories.name LIKE :search';
        }

        $productQuery = $this->conn->prepare($sql);

        if ($search !== '') {
            $productQuery->execute(['search' => '%' . $search . '%',]);
        } else {
            $productQuery->execute();
        }

        return $productQuery->fetchColumn();
    }


    // Get low-stock products
    public function getLowStockProducts($level = 5)
    {
        $productQuery = $this->conn->prepare(
            'SELECT  products.id, products.name, products.stock_quantity, products.sale_unit, categories.name AS category_name
            FROM products
            INNER JOIN categories
                ON categories.id = products.category_id
            WHERE products.stock_quantity <= :level
                AND products.is_active = 1
            ORDER BY products.stock_quantity ASC, products.name ASC'
        );

        $productQuery->execute([
            'level' => $level,
        ]);

        return $productQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}
