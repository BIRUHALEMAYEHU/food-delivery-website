<?php
$host = "localhost";
$port = "3306"; // Default MySQL port
$dbname = "food_delivery";
$username = "root";
$password = "";

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}
