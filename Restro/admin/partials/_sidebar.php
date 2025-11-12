<?php
require_once(__DIR__ . '/../../includes/config.php');

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_name = 'Administrator'; // Default fallback

if (isset($_SESSION['admin_id'])) {
    $stmt = $pdo->prepare("SELECT admin_name FROM rpos_admin WHERE admin_id = :id");
    $stmt->execute(['id' => $_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $admin_name = $admin['admin_name'];
    }
}
?>

<!-- Sidebar HTML Starts -->
<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
  <div class="container-fluid">
    <a class="navbar-brand pt-0" href="dashboard.php">
      <h4 class="text-primary">Welcome, <?php echo htmlspecialchars($admin_name); ?></h4>
    </a>
    <hr class="my-3">

    <ul class="navbar-nav">
      <!-- Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="dashboard.php">
          <i class="ni ni-tv-2 text-primary"></i> Dashboard
        </a>
      </li>

      <!-- Products Section -->
      <li class="nav-item">
        <a class="nav-link" href="products.php">
          <i class="ni ni-bag-17 text-orange"></i> Products
        </a>
      </li>

      <!-- 🔥 New Stock Reports Link -->
      <li class="nav-item">
        <a class="nav-link" href="stock_reports.php">
          <i class="fas fa-boxes text-yellow"></i> Stock Reports
        </a>
      </li>

      <!-- Orders -->
      <li class="nav-item">
        <a class="nav-link" href="orders.php">
          <i class="ni ni-cart text-green"></i> Orders
        </a>
      </li>

      <!-- Payments -->
      <li class="nav-item">
        <a class="nav-link" href="payments.php">
          <i class="ni ni-credit-card text-red"></i> Payments
        </a>
      </li>

      <!-- Logout -->
      <li class="nav-item">
        <a class="nav-link" href="logout.php">
          <i class="ni ni-button-power text-dark"></i> Logout
        </a>
      </li>
    </ul>
  </div>
</nav>
<!-- Sidebar HTML Ends -->
