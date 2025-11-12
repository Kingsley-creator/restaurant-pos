<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

// -----------------------------
// EXPORT TO EXCEL
// -----------------------------
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
  header("Content-Type: application/vnd.ms-excel");
  header("Content-Disposition: attachment; filename=stock_report_" . date('Ymd_His') . ".xls");
  echo "Product\tChange Type\tQuantity\tRemarks\tDate\n";

  $query = "
    SELECT p.prod_name, log.change_type, log.quantity, log.remarks, log.created_at
    FROM rpos_stock_log AS log
    INNER JOIN rpos_products AS p ON p.prod_id = log.prod_id
    ORDER BY log.created_at DESC
  ";
  foreach ($pdo->query($query) as $row) {
    echo "{$row['prod_name']}\t{$row['change_type']}\t{$row['quantity']}\t" .
         str_replace(["\n", "\r"], ' ', $row['remarks']) . "\t" .
         date('d M Y, H:i', strtotime($row['created_at'])) . "\n";
  }
  exit();
}

// -----------------------------
// EXPORT TO PDF (simple HTML → PDF)
// -----------------------------
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
  header("Content-Type: application/octet-stream");
  header("Content-Disposition: attachment; filename=stock_report_" . date('Ymd_His') . ".pdf");

  // Basic HTML structure for PDF export (you can print → Save as PDF)
  echo "<h2>Stock Movement Report</h2>";
  echo "<table border='1' cellspacing='0' cellpadding='5' style='border-collapse: collapse; width: 100%;'>";
  echo "<thead><tr><th>Product</th><th>Type</th><th>Quantity</th><th>Remarks</th><th>Date</th></tr></thead><tbody>";

  $query = "
    SELECT p.prod_name, log.change_type, log.quantity, log.remarks, log.created_at
    FROM rpos_stock_log AS log
    INNER JOIN rpos_products AS p ON p.prod_id = log.prod_id
    ORDER BY log.created_at DESC
  ";
  foreach ($pdo->query($query) as $row) {
    $type = ($row['change_type'] === 'add') ? 'Restocked' : 'Deducted';
    echo "<tr>
            <td>{$row['prod_name']}</td>
            <td>{$type}</td>
            <td>{$row['quantity']}</td>
            <td>" . htmlspecialchars($row['remarks'] ?: '—') . "</td>
            <td>" . date('d M Y, H:i', strtotime($row['created_at'])) . "</td>
          </tr>";
  }

  echo "</tbody></table><br><small>Generated on " . date('d M Y, H:i') . "</small>";
  exit();
}

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
      <div class="container-fluid text-white">
        <h2>📦 Stock Movement Reports</h2>
        <p>Monitor and export your full inventory change history.</p>
      </div>
    </div>

    <!-- Page Content -->
    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow border-0">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h3 class="mb-0"><i class="fas fa-boxes text-primary"></i> Stock Log</h3>
              <div>
                <a href="?export=excel" class="btn btn-sm btn-success">
                  <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="?export=pdf" class="btn btn-sm btn-danger ml-2">
                  <i class="fas fa-file-pdf"></i> Export PDF
                </a>
              </div>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Type</th>
                    <th>Quantity</th>
                    <th>Remarks</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                    $stmt = $pdo->query("
                      SELECT log.*, p.prod_name 
                      FROM rpos_stock_log AS log
                      INNER JOIN rpos_products AS p ON p.prod_id = log.prod_id
                      ORDER BY log.created_at DESC
                    ");
                    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if (count($logs) === 0) {
                      echo "<tr><td colspan='6' class='text-center text-muted'>No stock activity recorded yet.</td></tr>";
                    } else {
                      $count = 1;
                      $added = $deducted = 0;

                      foreach ($logs as $row) {
                        $typeBadge = ($row['change_type'] === 'add')
                          ? "<span class='badge badge-success'>Restocked</span>"
                          : "<span class='badge badge-danger'>Deducted</span>";

                        if ($row['change_type'] === 'add') $added += $row['quantity'];
                        else $deducted += $row['quantity'];

                        echo "
                          <tr>
                            <td>{$count}</td>
                            <td>" . htmlspecialchars($row['prod_name']) . "</td>
                            <td>{$typeBadge}</td>
                            <td>{$row['quantity']}</td>
                            <td>" . htmlspecialchars($row['remarks'] ?: '—') . "</td>
                            <td>" . date('d M Y, H:i', strtotime($row['created_at'])) . "</td>
                          </tr>
                        ";
                        $count++;
                      }
                    }
                  } catch (PDOException $e) {
                    echo "<tr><td colspan='6'>Error loading stock logs: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>

            <?php if (!empty($logs)): ?>
            <div class="card-footer bg-light py-3">
              <div class="row text-center">
                <div class="col-md-6">
                  <h6 class="text-success mb-0">Total Restocked: <?php echo $added; ?> Units</h6>
                </div>
                <div class="col-md-6">
                  <h6 class="text-danger mb-0">Total Deducted: <?php echo $deducted; ?> Units</h6>
                </div>
              </div>
            </div>
            <?php endif; ?>

            <div class="card-footer text-right">
              <a href="products.php" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Products
              </a>
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
