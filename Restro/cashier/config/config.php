<?php
$host = 'localhost';
$user = 'postgres';
$pass = 'otiu2025.';
$dbname = 'rposystem';

try {
    $pdo = new PDO("pgsql:host=$host;port=5432;dbname=$dbname;options='--client_encoding=UTF8'", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PostgreSQL connection failed: " . $e->getMessage());
}
?>
