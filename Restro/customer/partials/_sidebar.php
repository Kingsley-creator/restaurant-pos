<?php
// Ensure session and config are loaded
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../config/config.php');

// Fetch logged-in customer details
$customer = null;
if (isset($_SESSION['customer_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT customer_name, customer_email FROM rpos_customers WHERE customer_id = :cid");
        $stmt->execute(['cid' => $_SESSION['customer_id']]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fail silently but log or show fallback later
        $customer = null;
    }
}
?>

<!-- Sidebar -->
<nav class="navbar navbar-vertical fixed-left navbar-expand-md navbar-light bg-white" id="sidenav-main">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="navbar-brand pt-0" href="index.php">
      <h3 class="text-success font-weight-bold">Restaurant POS</h3>
    </a>

    <!-- User info -->
    <ul class="navbar-nav align-items-center d-md-none">
      <li class="nav-item dropdown">
        <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle bg-success text-white">
              <i class="fas fa-user"></i>
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm font-weight-bold">
                <?= htmlspecialchars($customer['customer_name'] ?? 'Customer'); ?>
              </span>
            </div>
          </div>
        </a>
      </li>
    </ul>

    <!-- Nav items -->
    <div class="collapse navbar-collapse" id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-home text-primary"></i> Dashboard
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="orders.php">
            <i class="fas fa-receipt text-success"></i> My Orders
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="receipts.php">
            <i class="fas fa-file-invoice-dollar text-info"></i> Receipts
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">
            <i class="fas fa-user-circle text-warning"></i> Profile
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">
            <i class="fas fa-sign-out-alt text-danger"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
