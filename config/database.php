<?php

/**
 * Creates the database connection.
 * 
 * Ref: PHP PDO - https://www.php.net/manual/en/book.pdo.php
 */

$host = 'localhost';
$database = 'webpos';
$username = 'root';
$password = 'root';

try {
    // Connect to the MySQL database
    $conn = new PDO("mysql:host={$host};dbname={$database}", $username, $password);

    // Throw an exception if an error occurs
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit('Database connection failed.');
}
