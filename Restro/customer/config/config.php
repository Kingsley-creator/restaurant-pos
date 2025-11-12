<?php
// PostgreSQL Database Configuration for Customer Module
$host = 'localhost';
$user = 'postgres'; // your PostgreSQL username
$pass = 'otiu2025.'; // your PostgreSQL password
$dbname = 'rposystem'; // confirm this name matches your database

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$dbname;", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "✅ Connected successfully to PostgreSQL!";
} catch (PDOException $e) {
    die("❌ PostgreSQL connection failed: " . $e->getMessage());
}
?>
