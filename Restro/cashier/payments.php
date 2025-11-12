<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

// Cancel order
if (isset($_GET['cancel'])) {
  try {
    $stmt = $pdo->prepare("DELETE FROM rpos_orders WHERE order_id = :id");
    $stmt->execute(['id' => $_GET['cancel']]);
    $success = "Order deleted successfully.";
    header("refresh:1; url=payments.php");
  } catch (PDOException $e) {
    $err = "Error deleting order: " . htmlspecialchars($e->getMessage());
  }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>
    <div class="header pb-8 pt-5 pt-md-8" style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;">
      <span class="mask bg-gradient-dark opacity-8"></span>
    </div>

    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <a href="orders.php" class="btn btn-outline-success">
                <i class="fas fa-plus"></i> <i class="fas fa-utensils"></i> Make A New Order
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                    $stmt = $pdo->query("SELECT * FROM rpos_orders WHERE order_status = '' ORDER BY created_at DESC");
                    foreach ($stmt as $order) {
                      $total = $order['prod_price'] * $order['prod_qty'];
                      echo "<tr>
                            <th class='text-success'>{$order['order_code']}</th>
                            <td>{$order['customer_name']}</td>
                            <td>{$order['prod_name']}</td>
                            <td>\${$total}</td>
                            <td>" . date('d/M/Y g:i', strtotime($order['created_at'])) . "</td>
                            <td>
                              <a href='pay_order.php?order_code={$order['order_code']}&customer_id={$order['customer_id']}&order_status=Paid'>
                                <button class='btn btn-sm btn-success'><i class='fas fa-handshake'></i> Pay Order</button>
                              </a>
                              <a href='payments.php?cancel={$order['order_id']}'>
                                <button class='btn btn-sm btn-danger'><i class='fas fa-window-close'></i> Cancel</button>
                              </a>
                            </td>
                          </tr>";
                    }
                  } catch (PDOException $e) {
                    echo "<tr><td colspan='6'>Error loading payments: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
