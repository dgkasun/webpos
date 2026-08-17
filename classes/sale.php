<?php

/**
 * Handles sale data and database operations.
 * The database connection is passed through the constructor.
 * 
 * Ref: Fowler, M. (2004)
 * Ref: PHP PDO Transactions - https://www.php.net/manual/en/pdo.transactions.php
 */

class Sale
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Find a sale by ID
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

    // Get items for a sale
    public function getItems(int $saleId)
    {
        $saleItemsQuery = $this->conn->prepare(
            'SELECT products.name AS product_name, products.sale_unit, sale_items.quantity, sale_items.unit_price, sale_items.subtotal
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

    // Get today's sales summary
    public function getTodaySummary()
    {
        $saleQuery = $this->conn->query(
            'SELECT COUNT(*) AS sale_count,     COALESCE(SUM(total_amount), 0) AS sales_total
            FROM sales
            WHERE DATE(created_at) = CURDATE()'
        );
        return $saleQuery->fetch(PDO::FETCH_ASSOC);
    }

    // Create a sale and update product stock
    public function create(int $userId, array $items, float $totalAmount, string $paymentMethod, ?float $cashReceived, ?float $changeAmount)
    {
        try {
            // Start the transaction
            $this->conn->beginTransaction();

            // Create sale record
            $saleQuery = $this->conn->prepare(
                'INSERT INTO sales (user_id, total_amount, payment_method, cash_received, change_amount
                ) VALUES (:user_id, :total_amount, :payment_method, :cash_received, :change_amount)'
            );

            $saleQuery->execute([
                'user_id' => $userId,
                'total_amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'cash_received' => $cashReceived,
                'change_amount' => $changeAmount,
            ]);

            $saleId = $this->conn->lastInsertId();

            // Lock the product while checking stock
            $productQuery = $this->conn->prepare(
                'SELECT stock_quantity
                FROM products
                WHERE id = :product_id
                FOR UPDATE'
            );

            $saleItemQuery = $this->conn->prepare(
                'INSERT INTO sale_items ( sale_id, product_id, quantity, unit_price, subtotal
                ) VALUES (:sale_id, :product_id, :quantity, :unit_price, :subtotal)'
            );

            $stockQuery = $this->conn->prepare(
                'UPDATE products
                SET stock_quantity = stock_quantity - :quantity
                WHERE id = :product_id'
            );

            foreach ($items as $item) {
                $productId = $item['id'];
                $quantity = $item['quantity'];
                $unitPrice = $item['price'];

                $subtotal = $unitPrice * $quantity;

                $productQuery->execute([
                    'product_id' => $productId,
                ]);

                // Check available stock
                $product = $productQuery->fetch(PDO::FETCH_ASSOC);

                if (!$product) {
                    throw new Exception('Product not found.');
                }
                if ($quantity > $product['stock_quantity']) {
                    throw new Exception('Not enough stock available.');
                }

                // Save the sale item
                $saleItemQuery->execute([
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Reduce product stock
                $stockQuery->execute([
                    'quantity' => $quantity,
                    'product_id' => $productId,
                ]);
            }

            // Complete the transaction
            $this->conn->commit();

            return $saleId;
        } catch (Exception $e) {
            // Roll back if the sale fails
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    // Get sales within a date range
    public function getByDateRange(string $fromDate, string $toDate)
    {
        $salesQuery = $this->conn->prepare(
            'SELECT sales.id, sales.total_amount, sales.payment_method, sales.created_at, users.name AS cashier_name
            FROM sales
            INNER JOIN users ON users.id = sales.user_id
            WHERE DATE(sales.created_at) BETWEEN :from_date AND :to_date
            ORDER BY sales.created_at DESC'
        );
        $salesQuery->execute([
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);
        return $salesQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get sales summary for a date range
    public function getReportSummary(string $fromDate, string $toDate)
    {
        $salesQuery = $this->conn->prepare(
            'SELECT COUNT(*) AS sale_count, COALESCE(SUM(total_amount), 0) AS sales_total
            FROM sales
            WHERE DATE(created_at) BETWEEN :from_date AND :to_date'
        );

        $salesQuery->execute([
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        $summary = $salesQuery->fetch(PDO::FETCH_ASSOC);

        $itemsQuery = $this->conn->prepare(
            'SELECT COALESCE(SUM(sale_items.quantity), 0) AS items_sold
            FROM sale_items
            INNER JOIN sales ON sales.id = sale_items.sale_id
            WHERE DATE(sales.created_at) BETWEEN :from_date AND :to_date'
        );

        $itemsQuery->execute([
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        $items = $itemsQuery->fetch(PDO::FETCH_ASSOC);

        $summary['items_sold'] = $items['items_sold'];

        return $summary;
    }

    // Get filtered sales
    public function getFiltered(string $fromDate, string $toDate, int $saleId, int $limit, int $offset)
    {
        $sql =
            'SELECT sales.id, sales.total_amount, sales.payment_method, sales.created_at, users.name AS cashier_name
            FROM sales
            INNER JOIN users
                ON users.id = sales.user_id
            WHERE 1=1';

        $params = [];

        if ($fromDate !== '') {
            $sql .= ' AND DATE(sales.created_at) >= :from_date';
            $params['from_date'] = $fromDate;
        }

        if ($toDate !== '') {
            $sql .= ' AND DATE(sales.created_at) <= :to_date';
            $params['to_date'] = $toDate;
        }

        if ($saleId > 0) {
            $sql .= ' AND sales.id = :sale_id';
            $params['sale_id'] = $saleId;
        }

        $sql .= " ORDER BY sales.created_at DESC
                LIMIT $limit OFFSET $offset";

        $salesQuery = $this->conn->prepare($sql);
        $salesQuery->execute($params);

        return $salesQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the number of filtered sales
    public function getFilteredCount(string $fromDate, string $toDate, int $saleId)
    {
        $sql = 'SELECT COUNT(*) 
                FROM sales 
                WHERE 1=1';

        $params = [];

        if ($fromDate !== '') {
            $sql .= ' AND DATE(created_at) >= :from_date';
            $params['from_date'] = $fromDate;
        }

        if ($toDate !== '') {
            $sql .= ' AND DATE(created_at) <= :to_date';
            $params['to_date'] = $toDate;
        }

        if ($saleId > 0) {
            $sql .= ' AND id = :sale_id';
            $params['sale_id'] = $saleId;
        }

        $salesQuery = $this->conn->prepare($sql);
        $salesQuery->execute($params);

        return $salesQuery->fetchColumn();
    }

    // Get the top five best selling products
    public function getBestSellingProducts(string $fromDate, string $toDate)
    {
        $productQuery = $this->conn->prepare(
            'SELECT products.name AS product_name,
                SUM(sale_items.quantity) AS quantity_sold,
                SUM(sale_items.subtotal) AS sales_amount
            FROM sale_items
            INNER JOIN sales
                ON sales.id = sale_items.sale_id
            INNER JOIN products
                ON products.id = sale_items.product_id
            WHERE DATE(sales.created_at) BETWEEN :from_date AND :to_date
            GROUP BY products.id, products.name
            ORDER BY quantity_sold DESC
            LIMIT 5'
        );

        $productQuery->execute([
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        return $productQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}
