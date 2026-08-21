<?php

$host = 'localhost';
$database = 'webpos_test';
$username = 'root';
$password = '';

$conn = new PDO("mysql:host={$host};dbname={$database}", $username, $password);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

return $conn;
