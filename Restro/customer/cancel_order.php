<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
check_login();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$order_code = $_GET['order_code'] ?? '';

if (empty($order_code)) {
    die("<h3 style='color:red;text-align:center;'>Invalid request — missing order code.</h3>");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM rpos_orders WHERE order_code = :ocode AND customer_id = :cid LIMIT 1");
    $stmt->execute(['ocode' => $order_code, 'cid' => $customer_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("<h3 style='color:red;text-align:center;'>Order not found or access denied.</h3>");
    }

    if ($order['order_status'] === 'Paid') {
        die("<h3 style='color:red;text-align:center;'>You cannot cancel a paid order.</h3>");
    }

    $update = $pdo->prepare("UPDATE rpos_orders SET order_status = 'Cancelled' WHERE order_code = :ocode");
    $update->execute(['ocode' => $order_code]);

    header("Location: index.php?msg=cancelled");
    exit();
} catch (PDOException $e) {
    die("<h3 style='color:red;text-align:center;'>Database error: " . htmlspecialchars($e->getMessage()) . "</h3>");
}
?>
