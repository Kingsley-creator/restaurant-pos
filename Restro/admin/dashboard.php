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

    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" 
         class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">

          <?php
          // Fetch dashboard stats
          try {
            $customers = $pdo->query("SELECT COUNT(*) FROM rpos_customers")->fetchColumn();
            $products = $pdo->query("SELECT COUNT(*) FROM rpos_products")->fetchColumn();
            $orders = $pdo->query("SELECT COUNT(*) FROM rpos_orders")->fetchColumn();
            $sales = $pdo->query("SELECT COALESCE(SUM(CAST(pay_amt AS NUMERIC)), 0) FROM rpos_payments")->fetchColumn();
          } catch (PDOException $e) {
            $customers = $products = $orders = $sales = 0;
          }

          // Low stock detection
          try {
            $lowStockQuery = $pdo->query("SELECT prod_name, prod_stock FROM rpos_products WHERE prod_stock < 5 ORDER BY prod_stock ASC");
            $lowStockItems = $lowStockQuery->fetchAll(PDO::FETCH_ASSOC);
            $lowStockCount = count($lowStockItems);
          } catch (PDOException $e) {
            $lowStockItems = [];
            $lowStockCount = 0;
          }
          ?>

          <!-- Low stock alert -->
          <?php if ($lowStockCount > 0): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong>⚠️ Low Stock Alert:</strong> <?php echo $lowStockCount; ?> product<?php echo $lowStockCount > 1 ? 's' : ''; ?> need restocking.
              <ul class="mb-0">
                <?php foreach ($lowStockItems as $item): ?>
                  <li><?php echo htmlspecialchars($item['prod_name']); ?> — Stock: <?php echo intval($item['prod_stock']); ?></li>
                <?php endforeach; ?>
              </ul>
              <a href="products.php" class="alert-link">Restock Now</a>.
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>

          <!-- Stats Cards -->
          <div class="row">
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
                      <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
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
                      <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                        <i class="fas fa-dollar-sign"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> <!-- end stats row -->

        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--7">
      <!-- Recent Orders -->
      <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col"><h3 class="mb-0">Recent Orders</h3></div>
                <div class="col text-right"><a href="orders_reports.php" class="btn btn-sm btn-primary">See all</a></div>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>Code</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query = "SELECT * FROM rpos_orders ORDER BY created_at DESC LIMIT 7";
                  foreach ($pdo->query($query) as $order) {
                      $total = $order['prod_price'] * $order['prod_qty'];
                      echo "
                        <tr>
                          <td>{$order['order_code']}</td>
                          <td>{$order['customer_name']}</td>
                          <td>{$order['prod_name']}</td>
                          <td>\${$order['prod_price']}</td>
                          <td>{$order['prod_qty']}</td>
                          <td>\$$total</td>
                          <td>" . ($order['order_status'] == '' 
                            ? "<span class='badge badge-danger'>Not Paid</span>" 
                            : "<span class='badge badge-success'>{$order['order_status']}</span>") . "</td>
                          <td>" . date('d/M/Y g:i', strtotime($order['created_at'])) . "</td>
                        </tr>
                      ";
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
