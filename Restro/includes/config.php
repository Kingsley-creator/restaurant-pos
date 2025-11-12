<?php
/**
 * Restaurant POS - Unified Database Configuration
 * Works both locally and in Render cloud deployment.
 * Author: O. Kingsley
 */

# ----------------------------------------------------------
# Load environment variables (Render provides them automatically)
# Fallbacks are your local PostgreSQL setup.
# ----------------------------------------------------------
$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '5432';
$db   = getenv('DB_NAME') ?: 'rposystem';
$user = getenv('DB_USER') ?: 'postgres';
$pass = getenv('DB_PASS') ?: 'otiu2025.';

# ----------------------------------------------------------
# Error Reporting (Turn OFF on production)
# ----------------------------------------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

# ----------------------------------------------------------
# Initialize PostgreSQL PDO Connection
# ----------------------------------------------------------
try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Optional: You can uncomment this line to verify the connection locally
    // echo "<p style='color:green;text-align:center;'>✅ Database Connected Successfully</p>";

} catch (PDOException $e) {
    // Safe error message (no credentials exposed)
    echo "<h3 style='color:red; text-align:center;'>⚠️ Database connection failed.</h3>";
    // Uncomment line below for debugging only (remove after testing)
    // echo "<pre>" . $e->getMessage() . "</pre>";
    exit();
}
?>
