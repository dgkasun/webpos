<?php

/**
 * Creates the database connection.
 * 
 * Ref: PHP PDO - https://www.php.net/manual/en/book.pdo.php
 */

$config = require __DIR__ . '/config.php';

$host = $config['database']['host'];
$database = $config['database']['name'];
$username = $config['database']['username'];
$password = $config['database']['password'];

try {
    // Connect to the MySQL database
    $conn = new PDO("mysql:host={$host};dbname={$database}", $username, $password);

    // Throw an exception if an error occurs
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set MySQL to Sri Lanka time
    $conn->exec("SET time_zone = '+05:30'");
} catch (PDOException $e) {
    exit('Database connection failed.');
}
