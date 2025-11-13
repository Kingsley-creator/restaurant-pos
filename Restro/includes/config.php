<?php
/**
 * Restaurant POS - Universal Database Configuration
 * Works locally and on Render cloud PostgreSQL.
 * Author: O. Kingsley
 */

# ----------------------------------------------------------
# Try using Render's DATABASE_URL first
# ----------------------------------------------------------
$databaseUrl = getenv("DATABASE_URL");

if ($databaseUrl) {
    // Parse Render DATABASE_URL
    $url = parse_url($databaseUrl);

    $host = $url['host'];
    $port = $url['port'];
    $user = $url['user'];
    $pass = $url['pass'];
    $db   = ltrim($url['path'], '/');

} else {
    # ----------------------------------------------------------
    # Fallback to LOCAL ENV settings (for XAMPP / localhost)
    # ----------------------------------------------------------
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '5432';
    $db   = getenv('DB_NAME') ?: 'rposystem';
    $user = getenv('DB_USER') ?: 'postgres';
    $pass = getenv('DB_PASS') ?: 'otiu2025.';
}

# ----------------------------------------------------------
# Enable error reporting (Turn off in production)
# ----------------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

# ----------------------------------------------------------
# Connect using PDO
# ----------------------------------------------------------
try {
    $dsn = "pgsql:host={$host};port={$port};dbname={$db}";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

} catch (PDOException $e) {
    echo "<h3 style='color:red;text-align:center;'>⚠️ Database connection failed.</h3>";
    exit();
}
?>
