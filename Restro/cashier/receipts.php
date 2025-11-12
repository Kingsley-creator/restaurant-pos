<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" 
         class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
          <h2 class="text-white">Receipts & Payment History</h2>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
              <h3>All Receipts</h3>
              <a href="payments.php" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Payments
              </a>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>Receipt #</th>
                    <th>Order Code</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Payment Method</th>
                    <th>Amount ($)</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                    $query = "
                      SELECT 
                        p.pay_code, p.order_code, p.pay_method, p.pay_amt, p.created_at,
                        c.customer_name, o.prod_name
                      FROM rpos_payments p
                      INNER JOIN rpos_orders o ON o.order_code = p.order_code
                      INNER JOIN rpos_customers c ON c.customer_id = p.customer_id
                      ORDER BY p.created_at DESC
                    ";
                    $stmt = $pdo->query($query);
                    $count = 1;

                    if ($stmt->rowCount() === 0) {
                      echo "<tr><td colspan='8' class='text-center text-muted'>No receipts found yet.</td></tr>";
                    } else {
                      foreach ($stmt as $row) {
                        echo "
                          <tr>
                            <td>{$count}</td>
                            <td>" . htmlspecialchars($row['order_code']) . "</td>
                            <td>" . htmlspecialchars($row['customer_name']) . "</td>
                            <td>" . htmlspecialchars($row['prod_name']) . "</td>
                            <td>" . htmlspecialchars($row['pay_method']) . "</td>
                            <td>$" . number_format($row['pay_amt'], 2) . "</td>
                            <td>" . date('d M Y, h:i A', strtotime($row['created_at'])) . "</td>
                            <td>
                              <a href='print_receipt.php?order_code=" . urlencode($row['order_code']) . "' class='btn btn-sm btn-success'>
                                <i class='fas fa-print'></i> Print
                              </a>
                            </td>
                          </tr>
                        ";
                        $count++;
                      }
                    }
                  } catch (PDOException $e) {
                    echo "<tr><td colspan='8'>Error loading receipts: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
