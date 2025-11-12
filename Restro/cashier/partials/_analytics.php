<?php
// partials/_analytics.php
// Safe session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// This partial computes summary numbers and exports them as variables:
// $customers, $products, $orders, $sales
$customers = $products = $orders = $sales = 0;

try {
    // customers count
    $customers = (int) $pdo->query("SELECT COUNT(*) FROM rpos_customers")->fetchColumn();

    // products count
    $products = (int) $pdo->query("SELECT COUNT(*) FROM rpos_products")->fetchColumn();

    // orders count
    $orders = (int) $pdo->query("SELECT COUNT(*) FROM rpos_orders")->fetchColumn();

    // sales sum (pay_amt stored as text in your dump — cast to numeric safely)
    $stmt = $pdo->query("SELECT COALESCE(SUM(CASE WHEN pay_amt ~ '^[0-9]+([.][0-9]+)?$' THEN pay_amt::numeric ELSE 0 END), 0) FROM rpos_payments");
    $sales = $stmt->fetchColumn();
    if ($sales === false) $sales = 0;
} catch (PDOException $e) {
    // fail silently but log error to server log
    error_log("Analytics partial error: " . $e->getMessage());
    $customers = $products = $orders = 0;
    $sales = 0;
}
