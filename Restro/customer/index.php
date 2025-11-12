<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');

// Ensure the customer is logged in
check_login();

$customer_id = $_SESSION['customer_id'];

try {
    // Fetch customer details
    $stmt = $pdo->prepare("SELECT * FROM rpos_customers WHERE customer_id = :cid LIMIT 1");
    $stmt->execute(['cid' => $customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        session_destroy();
        header("Location: login.php?error=notfound");
        exit();
    }

    // Fetch customer’s 5 most recent orders
    $ordersStmt = $pdo->prepare("
        SELECT order_code, prod_name, prod_qty, prod_price, order_status, created_at
        FROM rpos_orders
        WHERE customer_id = :cid
        ORDER BY created_at DESC
        LIMIT 5
    ");
    $ordersStmt->execute(['cid' => $customer_id]);
    $orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<h3 style='color:red;text-align:center;'>Database error: " . htmlspecialchars($e->getMessage()) . "</h3>");
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
          <h2>Welcome, <?= htmlspecialchars($customer['customer_name']); ?> 👋</h2>
          <p>Here’s a quick summary of your recent activity.</p>
        </div>
      </div>
    </div>

    <!-- Page Content -->
    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow border-0">
            <div class="card-header border-0">
              <h3 class="mb-0">Recent Orders</h3>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Order Code</th>
                    <th scope="col">Product</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Price ($)</th>
                    <th scope="col">Status / Action</th>
                    <th scope="col">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($orders)): ?>
                    <tr>
                      <td colspan="6" class="text-center text-muted">No recent orders found.</td>
                    </tr>
                  <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                      <tr>
                        <td><?= htmlspecialchars($order['order_code']); ?></td>
                        <td><?= htmlspecialchars($order['prod_name']); ?></td>
                        <td><?= htmlspecialchars($order['prod_qty']); ?></td>
                        <td>$<?= number_format($order['prod_price'], 2); ?></td>
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
        </div>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
