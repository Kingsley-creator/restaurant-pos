<?php
$host = 'localhost';
$db   = 'rposystem';     // your PostgreSQL DB name
$user = 'postgres';      // PostgreSQL username
$pass = 'otiu2025.'; // PostgreSQL password
$port = '5432';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
