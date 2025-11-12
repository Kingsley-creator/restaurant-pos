<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
check_login();

$order_code = $_GET['order_code'] ?? null;

if (!$order_code) {
    die("<h3 style='text-align:center;color:red;'>Invalid receipt request — no order code provided.</h3>");
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            p.pay_code, p.pay_method, p.pay_amt, p.created_at,
            o.order_code, o.prod_name, o.prod_price, o.prod_qty,
            c.customer_name, c.customer_id
        FROM rpos_payments p
        INNER JOIN rpos_orders o ON o.order_code = p.order_code
        INNER JOIN rpos_customers c ON c.customer_id = p.customer_id
        WHERE p.order_code = :ocode
        LIMIT 1
    ");
    $stmt->execute(['ocode' => $order_code]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receipt) {
        die("<h3 style='text-align:center;color:red;'>Receipt not found for Order Code: {$order_code}</h3>");
    }
} catch (PDOException $e) {
    die("Error loading receipt: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipt #<?php echo htmlspecialchars($receipt['order_code']); ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <style>
    body {
      background: #f4f4f4;
      font-family: 'Courier New', monospace;
    }
    .receipt-container {
      background: white;
      width: 350px;
      margin: 40px auto;
      padding: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.15);
      border-radius: 8px;
    }
    .receipt-header {
      text-align: center;
      border-bottom: 2px dashed #999;
      margin-bottom: 15px;
    }
    .receipt-header h4 {
      margin-bottom: 3px;
    }
    .receipt-section {
      margin: 10px 0;
    }
    .receipt-footer {
      text-align: center;
      border-top: 2px dashed #999;
      margin-top: 15px;
      padding-top: 10px;
      font-size: 12px;
    }
    .btn-print {
      display: block;
      margin: 20px auto;
    }
    @media print {
      .btn-print {
        display: none;
      }
      body {
        background: white;
      }
    }
  </style>
</head>
<body onload="window.print()">

<div class="receipt-container">
  <div class="receipt-header">
    <h4><strong>Restaurant POS System</strong></h4>
    <small>Official Payment Receipt</small><br>
    <small>Date: <?php echo date('d M Y, h:i A', strtotime($receipt['created_at'])); ?></small>
  </div>

  <div class="receipt-section">
    <p><strong>Customer:</strong> <?php echo htmlspecialchars($receipt['customer_name']); ?></p>
    <p><strong>Customer ID:</strong> <?php echo htmlspecialchars($receipt['customer_id']); ?></p>
    <p><strong>Order Code:</strong> <?php echo htmlspecialchars($receipt['order_code']); ?></p>
    <p><strong>Payment Code:</strong> <?php echo htmlspecialchars($receipt['pay_code']); ?></p>
  </div>

  <hr>

  <div class="receipt-section">
    <table class="table table-sm table-borderless">
      <thead>
        <tr>
          <th>Item</th>
          <th>Qty</th>
          <th>Unit ($)</th>
          <th>Total ($)</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo htmlspecialchars($receipt['prod_name']); ?></td>
          <td><?php echo htmlspecialchars($receipt['prod_qty']); ?></td>
          <td><?php echo number_format($receipt['prod_price'], 2); ?></td>
          <td><?php echo number_format($receipt['prod_price'] * $receipt['prod_qty'], 2); ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <hr>

  <div class="receipt-section">
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($receipt['pay_method']); ?></p>
    <p><strong>Amount Paid:</strong> $<?php echo number_format($receipt['pay_amt'], 2); ?></p>
  </div>

  <div class="receipt-footer">
    <p>Thank you for your purchase!</p>
    <p><em>Powered by CoreLink Technologies</em></p>
  </div>

  <button class="btn btn-success btn-print" onclick="window.print()">
    <i class="fas fa-print"></i> Print Receipt
  </button>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js"></script>
</body>
</html>
