<?php
require_once(__DIR__ . '/../../includes/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin_name = 'Admin'; // Default fallback name

if (isset($_SESSION['admin_id'])) {
    $stmt = $pdo->prepare("SELECT admin_name FROM rpos_admin WHERE admin_id = :id");
    $stmt->execute(['id' => $_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $admin_name = $admin['admin_name'];
    }
}
?>
<!-- Top Navigation Bar -->
<nav class="navbar navbar-top navbar-expand-md navbar-dark bg-primary" id="navbar-main">
  <div class="container-fluid">
    <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="dashboard.php">
      Welcome, <?php echo htmlspecialchars($admin_name); ?>
    </a>
    <ul class="navbar-nav align-items-center d-none d-md-flex">
      <li class="nav-item dropdown">
        <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle">
              <img alt="Admin Image" src="../assets/img/theme/team-4.jpg">
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm font-weight-bold"><?php echo htmlspecialchars($admin_name); ?></span>
            </div>
          </div>
        </a>
        <div class="dropdown-menu dropdown-menu-arrow dropdown-menu-right">
          <div class="dropdown-divider"></div>
          <a href="logout.php" class="dropdown-item">
            <i class="ni ni-user-run"></i>
            <span>Logout</span>
          </a>
        </div>
      </li>
    </ul>
  </div>
</nav>
