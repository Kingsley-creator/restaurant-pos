<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
check_login();

$customer_id = $_SESSION['customer_id'];

try {
    $stmt = $pdo->prepare("
        SELECT * FROM rpos_orders 
        WHERE customer_id = :cid 
        ORDER BY created_at DESC
    ");
    $stmt->execute(['cid' => $customer_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<h3 style='color:red;text-align:center;'>Database error: " . htmlspecialchars($e->getMessage()) . "</h3>");
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header bg-gradient-dark pb-8 pt-5 pt-md-8 text-white">
      <div class="container-fluid">
        <h2>My Order History</h2>
        <p>Track your previous and current orders below.</p>
      </div>
    </div>

    <div class="container-fluid mt--7">
      <div class="card shadow border-0">
        <div class="card-header border-0"><h3>Order Records</h3></div>

        <div class="card-body table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th scope="col">Order Code</th>
                <th scope="col">Product</th>
                <th scope="col">Quantity</th>
                <th scope="col">Price ($)</th>
                <th scope="col">Total ($)</th>
                <th scope="col">Status / Action</th>
                <th scope="col">Date</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($orders)): ?>
                <tr><td colspan="7" class="text-center text-muted">No orders found.</td></tr>
              <?php else: ?>
                <?php foreach ($orders as $order): ?>
                  <tr>
                    <td><?= htmlspecialchars($order['order_code']); ?></td>
                    <td><?= htmlspecialchars($order['prod_name']); ?></td>
                    <td><?= htmlspecialchars($order['prod_qty']); ?></td>
                    <td>$<?= number_format($order['prod_price'], 2); ?></td>
                    <td>$<?= number_format($order['prod_price'] * $order['prod_qty'], 2); ?></td>
                    <td>
                      <?php if ($order['order_status'] === 'Paid'): ?>
                        <span class="badge badge-success">Paid</span>

                      <?php elseif ($order['order_status'] === 'Cancelled'): ?>
                        <span class="badge badge-danger">Cancelled</span>

                      <?php else: ?>
                        <span class="badge badge-warning">Pending</span><br>
                        <a href="pay_order.php?order_code=<?= urlencode($order['order_code']); ?>&customer_id=<?= $customer_id; ?>"
                           class="btn btn-sm btn-primary mt-2">Pay Now</a>
                        <a href="cancel_order.php?order_code=<?= urlencode($order['order_code']); ?>"
                           class="btn btn-sm btn-danger mt-2"
                           onclick="return confirm('Are you sure you want to cancel this order? This action cannot be undone.');">
                           Cancel
                        </a>
                      <?php endif; ?>
                    </td>
                    <td><?= date('d M Y, H:i', strtotime($order['created_at'])); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
