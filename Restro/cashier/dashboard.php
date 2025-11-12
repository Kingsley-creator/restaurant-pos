<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header pb-8 pt-5 pt-md-8" style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
          <div class="row">
            <?php
            try {
              $customers = $pdo->query("SELECT COUNT(*) FROM rpos_customers")->fetchColumn();
              $products = $pdo->query("SELECT COUNT(*) FROM rpos_products")->fetchColumn();
              $orders = $pdo->query("SELECT COUNT(*) FROM rpos_orders")->fetchColumn();
              $sales = $pdo->query("SELECT COALESCE(SUM(CAST(pay_amt AS NUMERIC)), 0) FROM rpos_payments")->fetchColumn();
            } catch (PDOException $e) {
              $customers = $products = $orders = $sales = 0;
            }
            ?>
            <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Customers</h5>
                      <span class="h2 font-weight-bold mb-0"><?php echo $customers; ?></span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                        <i class="fas fa-users"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Products</h5>
                      <span class="h2 font-weight-bold mb-0"><?php echo $products; ?></span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                        <i class="fas fa-utensils"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Orders</h5>
                      <span class="h2 font-weight-bold mb-0"><?php echo $orders; ?></span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                        <i class="fas fa-shopping-cart"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Sales</h5>
                      <span class="h2 font-weight-bold mb-0">$<?php echo number_format($sales, 2); ?></span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                        <i class="fas fa-dollar-sign"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- row end -->
        </div>
      </div>
    </div>

    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Recent Orders</h3>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                    $stmt = $pdo->query("SELECT * FROM rpos_orders ORDER BY created_at DESC LIMIT 6");
                    foreach ($stmt as $row) {
                      $total = $row['prod_price'] * $row['prod_qty'];
                      $status = empty($row['order_status'])
                        ? "<span class='badge badge-danger'>Not Paid</span>"
                        : "<span class='badge badge-success'>{$row['order_status']}</span>";

                      echo "<tr>
                            <td>{$row['order_code']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['prod_name']}</td>
                            <td>{$row['prod_qty']}</td>
                            <td>\${$row['prod_price']}</td>
                            <td>\${$total}</td>
                            <td>{$status}</td>
                            <td>" . date('d M Y, H:i', strtotime($row['created_at'])) . "</td>
                          </tr>";
                    }
                  } catch (PDOException $e) {
                    echo "<tr><td colspan='8'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
