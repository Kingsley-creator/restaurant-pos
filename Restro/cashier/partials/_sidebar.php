<?php
// partials/_sidebar.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// default username
$display_name = 'Welcome';

// determine whether this is admin or staff
$uid = null;
$tbl = null;
$colName = null;

if (isset($_SESSION['admin_id'])) {
    $uid = $_SESSION['admin_id'];
    $tbl = 'rpos_admin';
    $colName = 'admin_name';
} elseif (isset($_SESSION['staff_id'])) {
    $uid = $_SESSION['staff_id'];
    $tbl = 'rpos_staff';
    $colName = 'staff_name';
}

if ($uid && $tbl) {
    try {
        $stmt = $pdo->prepare("SELECT {$colName} FROM {$tbl} WHERE {$tbl}." . ($colName === 'admin_name' ? "admin_id" : "staff_id") . " = :id LIMIT 1");
        $stmt->execute(['id' => $uid]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row[$colName])) {
            $display_name = $row[$colName];
        }
    } catch (PDOException $e) {
        error_log("Sidebar fetch user error: " . $e->getMessage());
    }
}
?>
<!-- Sidebar HTML Starts -->
<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
  <div class="container-fluid">
    <a class="navbar-brand pt-0" href="dashboard.php">
      <h4 class="text-primary">Welcome, <?php echo htmlspecialchars($display_name); ?></h4>
    </a>
    <hr class="my-3">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
          <i class="ni ni-tv-2 text-primary"></i> Dashboard
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="products.php">
          <i class="ni ni-bag-17 text-orange"></i> Products
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="orders.php">
          <i class="ni ni-cart text-green"></i> Orders
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="payments.php">
          <i class="ni ni-credit-card text-red"></i> Payments
        </a>
      </li>

      <!-- Stock Reports link you asked to include -->
      <li class="nav-item">
        <a class="nav-link" href="stock_reports.php">
          <i class="fas fa-boxes text-yellow"></i> Stock Reports
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <i class="ni ni-button-power text-dark"></i> Logout
        </a>
      </li>
    </ul>
  </div>
</nav>
<!-- Sidebar HTML Ends -->
