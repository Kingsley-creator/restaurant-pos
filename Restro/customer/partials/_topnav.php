<?php
// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../config/config.php');

// Fetch logged-in customer info
$customer_name = "Customer";
if (isset($_SESSION['customer_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT customer_name FROM rpos_customers WHERE customer_id = :cid");
        $stmt->execute(['cid' => $_SESSION['customer_id']]);
        $cust = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($cust) {
            $customer_name = $cust['customer_name'];
        }
    } catch (PDOException $e) {
        $customer_name = "Customer";
    }
}
?>

<!-- Top navigation bar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark bg-dark" id="navbar-main">
  <div class="container-fluid">
    <!-- Brand -->
    <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="index.php">Customer Dashboard</a>
    
    <!-- User section -->
    <ul class="navbar-nav align-items-center ml-auto ml-md-0">
      <li class="nav-item dropdown">
        <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle bg-success text-white">
              <i class="fas fa-user"></i>
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm font-weight-bold"><?= htmlspecialchars($customer_name); ?></span>
            </div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="profile.php" class="dropdown-item">
            <i class="fas fa-user text-primary"></i> My Profile
          </a>
          <a href="receipts.php" class="dropdown-item">
            <i class="fas fa-file-invoice-dollar text-success"></i> Receipts
          </a>
          <div class="dropdown-divider"></div>
          <a href="logout.php" class="dropdown-item">
            <i class="fas fa-sign-out-alt text-danger"></i> Logout
          </a>
        </div>
      </li>
    </ul>
  </div>
</nav>
