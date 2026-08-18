<?php

/**
 * Handles settings data and database operations.
 * The database connection is passed through the constructor.
 *
 * Ref: Fowler, M. (2004)
 * Ref: PHP PDO Transactions - https://www.php.net/manual/en/pdo.transactions.php
 */

class Setting
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    // Get shop settings
    public function get()
    {
        $settingQuery = $this->conn->query(
            'SELECT shop_name, address, phone, print_footer
             FROM settings
             LIMIT 1'
        );

        return $settingQuery->fetch(PDO::FETCH_ASSOC);
    }

    // Save shop settings
    public function save(string $shopName, string $address, string $phone, string $printtFooter)
    {
        $settings = $this->get();

        if ($settings) {
            $settingQuery = $this->conn->prepare(
                'UPDATE settings
                 SET
                    shop_name = :shop_name,
                    address = :address,
                    phone = :phone,
                    print_footer = :print_footer'
            );
        } else {
            $settingQuery = $this->conn->prepare(
                'INSERT INTO settings ( shop_name, address, phone, print_footer)
                 VALUES (:shop_name, :address, :phone, :print_footer)'
            );
        }

        $settingQuery->execute([
            'shop_name' => $shopName,
            'address' => $address !== '' ? $address : null,
            'phone' => $phone !== '' ? $phone : null,
            'print_footer' => $printtFooter !== '' ? $printtFooter : null,
        ]);
    }
}
