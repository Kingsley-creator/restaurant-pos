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
          <h2 class="text-white">Stock Movement Log</h2>
        </div>
      </div>
    </div>

    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0"><h3>📦 Stock Reports</h3></div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>Change Type</th>
                    <th>Quantity</th>
                    <th>Remarks</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                    $query = "
                      SELECT log.*, p.prod_name 
                      FROM rpos_stock_log AS log
                      INNER JOIN rpos_products AS p ON p.prod_id = log.prod_id
                      ORDER BY log.created_at DESC
                    ";
                    $stmt = $pdo->query($query);
                    $count = 1;
                    if ($stmt->rowCount() === 0) {
                      echo "<tr><td colspan='6' class='text-center text-muted'>No stock activity recorded yet.</td></tr>";
                    } else {
                      foreach ($stmt as $row) {
                        $typeBadge = ($row['change_type'] === 'add')
                          ? "<span class='badge badge-success'>Restock</span>"
                          : "<span class='badge badge-danger'>Deducted</span>";

                        echo "
                          <tr>
                            <td>{$count}</td>
                            <td>" . htmlspecialchars($row['prod_name']) . "</td>
                            <td>{$typeBadge}</td>
                            <td>{$row['quantity']}</td>
                            <td>" . (!empty($row['remarks']) ? htmlspecialchars($row['remarks']) : '<em>—</em>') . "</td>
                            <td>" . date('d M Y, H:i', strtotime($row['created_at'])) . "</td>
                          </tr>
                        ";
                        $count++;
                      }
                    }
                  } catch (PDOException $e) {
                    echo "<tr><td colspan='6'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="card-footer py-4">
              <div class="text-right">
                <a href="products.php" class="btn btn-sm btn-primary">
                  <i class="fas fa-arrow-left"></i> Back to Products
                </a>
              </div>
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
