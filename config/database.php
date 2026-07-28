<?php
$host = 'localhost';
$database = 'webpos';
$username = 'root';
$password = 'root';

try {
    $conn = new PDO("mysql:host={$host};dbname={$database}", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected succesfully";
} catch (PDOException $e) {
    exit('Database connection failed.');
}
