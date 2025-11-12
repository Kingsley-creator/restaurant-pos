<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
check_login();

// Ensure user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$success = $err = "";

try {
    // Fetch customer info
    $stmt = $pdo->prepare("SELECT * FROM rpos_customers WHERE customer_id = :cid LIMIT 1");
    $stmt->execute(['cid' => $customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        die("<h3 style='text-align:center;color:red;'>Customer record not found.</h3>");
    }
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}

// Handle update request
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($email)) {
        $err = "Please fill in all required fields.";
    } else {
        try {
            if (!empty($password)) {
                // Update including new password
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $updateQuery = "
                    UPDATE rpos_customers 
                    SET customer_name = :name, customer_email = :email, customer_password = :password 
                    WHERE customer_id = :cid
                ";
                $params = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashed,
                    'cid' => $customer_id
                ];
            } else {
                // Update without password change
                $updateQuery = "
                    UPDATE rpos_customers 
                    SET customer_name = :name, customer_email = :email 
                    WHERE customer_id = :cid
                ";
                $params = [
                    'name' => $name,
                    'email' => $email,
                    'cid' => $customer_id
                ];
            }

            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute($params);

            // Redirect to profile page with success message
            $_SESSION['success'] = "Profile updated successfully!";
            header("Location: profile.php");
            exit();
        } catch (PDOException $e) {
            $err = "Error updating profile: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<?php require_once('partials/_head.php'); ?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>

  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header bg-gradient-dark pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body text-white">
          <h2>Update My Profile</h2>
          <p>Change your account information below.</p>
        </div>
      </div>
    </div>

    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow border-0">
            <div class="card-header bg-transparent">
              <h3 class="mb-0">Edit Details</h3>
            </div>
            <div class="card-body">

              <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
              <?php elseif ($err): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
              <?php endif; ?>

              <form method="POST">
                <div class="form-group">
                  <label>Full Name</label>
                  <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['customer_name']); ?>" required>
                </div>

                <div class="form-group">
                  <label>Email Address</label>
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['customer_email']); ?>" required>
                </div>

                <div class="form-group">
                  <label>New Password (leave blank to keep current)</label>
                  <input type="password" name="password" class="form-control">
                </div>

                <div class="text-right">
                  <button type="submit" name="update" class="btn btn-success">Save Changes</button>
                </div>
              </form>

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
