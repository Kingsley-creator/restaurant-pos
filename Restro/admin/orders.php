<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>

  <div class="main-content">
    <!-- Top Navbar -->
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Orders</h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3 class="mb-0">All Orders</h3>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Order Code</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Product</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
                    <th scope="col">Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                      $query = "SELECT * FROM rpos_orders ORDER BY created_at DESC";
                      $stmt = $pdo->query($query);
                      foreach ($stmt as $order) {
                          $total = $order['prod_price'] * $order['prod_qty'];
                          $status = empty($order['order_status'])
                              ? "<span class='badge badge-danger'>Not Paid</span>"
                              : "<span class='badge badge-success'>{$order['order_status']}</span>";

                          echo "
                            <tr>
                              <td>{$order['order_code']}</td>
                              <td>{$order['customer_name']}</td>
                              <td>{$order['prod_name']}</td>
                              <td>\${$order['prod_price']}</td>
                              <td>{$order['prod_qty']}</td>
                              <td>\$$total</td>
                              <td>$status</td>
                              <td>" . date('d M Y, H:i', strtotime($order['created_at'])) . "</td>
                            </tr>";
                      }
                  } catch (PDOException $e) {
                      echo "<tr><td colspan='8'>Error loading orders: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <div class="card-footer py-4">
              <nav aria-label="...">
                <ul class="pagination justify-content-end mb-0">
                  <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">
                      <i class="fas fa-angle-left"></i>
                      <span class="sr-only">Previous</span>
                    </a>
                  </li>
                  <li class="page-item active">
                    <a class="page-link" href="#">1</a>
                  </li>
                  <li class="page-item">
                    <a class="page-link" href="#">2 <i class="fas fa-angle-right"></i></a>
                  </li>
                </ul>
              </nav>
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
