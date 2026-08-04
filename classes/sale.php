<?php

/**
 * Handles sale-related database operations.
 * The database connection is provided through constructor injection,
 * Ref: Fowler, M. (2004) - https://martinfowler.com/articles/injection.html
 */

class Sale
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getAll(): array
    {
        $salesQuery = $this->conn->query(
            'SELECT sales.id, sales.total_amount, sales.payment_method, sales.created_at, users.name AS cashier_name
             FROM sales
             INNER JOIN users
                ON users.id = sales.user_id
             ORDER BY sales.created_at DESC'
        );
        return $salesQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): array|false
    {
        $saleQuery = $this->conn->prepare(
            'SELECT sales.id, sales.total_amount, sales.payment_method, sales.cash_received, sales.change_amount, sales.created_at, users.name AS cashier_name
             FROM sales
             INNER JOIN users
                ON users.id = sales.user_id
             WHERE sales.id = :sale_id'
        );
        $saleQuery->execute([
            'sale_id' => $id,
        ]);
        return $saleQuery->fetch(PDO::FETCH_ASSOC);
    }

    public function getItems(int $saleId): array
    {
        $saleItemsQuery = $this->conn->prepare(
            'SELECT products.name AS product_name, sale_items.quantity, sale_items.unit_price, sale_items.subtotal
             FROM sale_items
             INNER JOIN products
                ON products.id = sale_items.product_id
             WHERE sale_items.sale_id = :sale_id
             ORDER BY sale_items.id ASC'
        );
        $saleItemsQuery->execute([
            'sale_id' => $saleId,
        ]);
        return $saleItemsQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}
