<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

// Cancel Order
if (isset($_GET['cancel'])) {
    $id = $_GET['cancel'];

    try {
        $stmt = $pdo->prepare("DELETE FROM rpos_orders WHERE order_id = :id");
        $stmt->execute(['id' => $id]);

        if ($stmt->rowCount() > 0) {
            $success = "Deleted";
            header("refresh:1; url=payments.php");
            exit();
        } else {
            $err = "No record deleted. Try again.";
        }
    } catch (PDOException $e) {
        $err = "Error deleting order: " . $e->getMessage();
    }
}

require_once('partials/_head.php');
?>

<body>
  <!-- Sidebar -->
  <?php require_once('partials/_sidebar.php'); ?>

  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body"></div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <a href="orders.php" class="btn btn-outline-success">
                <i class="fas fa-plus"></i> <i class="fas fa-utensils"></i>
                Make A New Order
              </a>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Code</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Product</th>
                    <th scope="col">Total Price</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                      // Fetch unpaid orders
                      $query = "SELECT * FROM rpos_orders WHERE order_status = '' ORDER BY created_at DESC";
                      $stmt = $pdo->query($query);

                      foreach ($stmt as $order) {
                          $total = $order['prod_price'] * $order['prod_qty'];
                          echo "
                            <tr>
                              <th class='text-success'>{$order['order_code']}</th>
                              <td>{$order['customer_name']}</td>
                              <td>{$order['prod_name']}</td>
                              <td>$" . number_format($total, 2) . "</td>
                              <td>" . date('d/M/Y g:i', strtotime($order['created_at'])) . "</td>
                              <td>
                                <a href='pay_order.php?order_code={$order['order_code']}&customer_id={$order['customer_id']}&order_status=Paid'>
                                  <button class='btn btn-sm btn-success'>
                                    <i class='fas fa-handshake'></i> Pay Order
                                  </button>
                                </a>

                                <a href='payments.php?cancel={$order['order_id']}' onclick=\"return confirm('Are you sure you want to cancel this order?');\">
                                  <button class='btn btn-sm btn-danger'>
                                    <i class='fas fa-window-close'></i> Cancel Order
                                  </button>
                                </a>
                              </td>
                            </tr>
                          ";
                      }
                  } catch (PDOException $e) {
                      echo "<tr><td colspan='6'>Error loading orders: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
