<?php
// partials/_topnav.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$display_name = 'System User';
$avatar = ''; // you can set a default avatar path if you have one

// pick admin or staff
if (isset($_SESSION['admin_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT admin_name FROM rpos_admin WHERE admin_id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['admin_id']]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r && isset($r['admin_name'])) $display_name = $r['admin_name'];
    } catch (PDOException $e) {
        error_log("Topnav admin fetch error: " . $e->getMessage());
    }
} elseif (isset($_SESSION['staff_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT staff_name FROM rpos_staff WHERE staff_id = :id LIMIT 1");
        $stmt->execute(['id' => $_SESSION['staff_id']]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($r && isset($r['staff_name'])) $display_name = $r['staff_name'];
    } catch (PDOException $e) {
        error_log("Topnav staff fetch error: " . $e->getMessage());
    }
}
?>
<nav class="navbar navbar-top navbar-expand-md navbar-dark" id="navbar-main">
  <div class="container-fluid">
    <a class="h4 mb-0 text-white text-uppercase d-none d-lg-inline-block" href="dashboard.php">WELCOME, <?php echo htmlspecialchars(strtoupper($display_name)); ?></a>
    <ul class="navbar-nav align-items-center d-none d-md-flex">
      <li class="nav-item dropdown">
        <a class="nav-link pr-0" href="#" role="button">
          <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle">
              <?php if (!empty($avatar)): ?>
                <img alt="Image placeholder" src="<?php echo htmlspecialchars($avatar); ?>">
              <?php else: ?>
                <img alt="avatar" src="../assets/img/theme/default_avatar.png">
              <?php endif; ?>
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm  font-weight-bold"><?php echo htmlspecialchars($display_name); ?></span>
            </div>
          </div>
        </a>
      </li>
    </ul>
  </div>
</nav>
