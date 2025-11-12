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

try {
    // Fetch all paid orders belonging to the logged-in customer
    $stmt = $pdo->prepare("
        SELECT 
            o.order_code,
            o.prod_name,
            o.prod_qty,
            o.prod_price,
            o.order_status,
            o.created_at,
            p.pay_method,
            p.pay_amt
        FROM rpos_orders o
        LEFT JOIN rpos_payments p ON o.order_code = p.order_code
        WHERE o.customer_id = :cid AND o.order_status = 'Paid'
        ORDER BY o.created_at DESC
    ");
    $stmt->execute(['cid' => $customer_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>

  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div class="header bg-gradient-dark pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body text-white">
          <h2>Receipts</h2>
          <p>View your recent payments and receipts below.</p>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow border-0">
            <div class="card-header border-0">
              <h3 class="mb-0">Payment History</h3>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Receipt Code</th>
                    <th scope="col">Product</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total ($)</th>
                    <th scope="col">Method</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (empty($orders)) {
                      echo "<tr><td colspan='7' class='text-center text-muted'>No receipts found yet.</td></tr>";
                  } else {
                      foreach ($orders as $order) {
                          $total = $order['prod_price'] * $order['prod_qty'];
                          $statusBadge = ($order['order_status'] === 'Paid')
                              ? "<span class='badge badge-success'>Paid</span>"
                              : "<span class='badge badge-warning'>Pending</span>";

                          echo "
                              <tr>
                                  <td>{$order['order_code']}</td>
                                  <td>{$order['prod_name']}</td>
                                  <td>{$order['prod_qty']}</td>
                                  <td>$" . number_format($total, 2) . "</td>
                                  <td>" . htmlspecialchars($order['pay_method'] ?? '—') . "</td>
                                  <td>{$statusBadge}</td>
                                  <td>" . date('d M Y, H:i', strtotime($order['created_at'])) . "</td>
                              </tr>
                          ";
                      }
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
