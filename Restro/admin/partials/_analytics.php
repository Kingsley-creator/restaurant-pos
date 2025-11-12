<?php
require_once(__DIR__ . '/../../includes/config.php');

// Total Orders
$orderCount = $pdo->query("SELECT COUNT(*) FROM rpos_orders")->fetchColumn();

// Total Products
$productCount = $pdo->query("SELECT COUNT(*) FROM rpos_products")->fetchColumn();

// Total Customers
$customerCount = $pdo->query("SELECT COUNT(*) FROM rpos_customers")->fetchColumn();

// Total Payments
$paymentCount = $pdo->query("SELECT COUNT(*) FROM rpos_payments")->fetchColumn();
?>
