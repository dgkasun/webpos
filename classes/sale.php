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

    public function getTodaySummary()
    {
        $saleQuery = $this->conn->query(
            'SELECT COUNT(*) AS sale_count,     COALESCE(SUM(total_amount), 0) AS sales_total
            FROM sales
            WHERE DATE(created_at) = CURDATE()'
        );
        return $saleQuery->fetch(PDO::FETCH_ASSOC);
    }

    /* checkout */
    public function create(int $userId, array $items, float $totalAmount, string $paymentMethod, ?float $cashReceived, ?float $changeAmount)
    {
        try {
            $this->conn->beginTransaction();

            // Create sale record.
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

                // Check the stock before completing.
                $product = $productQuery->fetch(PDO::FETCH_ASSOC);
                if (!$product) {
                    throw new Exception('Product not found.');
                }
                if ($quantity > $product['stock_quantity']) {
                    throw new Exception('Not enough stock available.');
                }

                // Save.
                $saleItemQuery->execute([
                    'sale_id' => $saleId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock.
                $stockQuery->execute([
                    'quantity' => $quantity,
                    'product_id' => $productId,
                ]);
            }

            $this->conn->commit();

            return $saleId;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            throw $e;
        }
    }

    /* sale report */
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

    /* sales filter */
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

    /* report */
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
