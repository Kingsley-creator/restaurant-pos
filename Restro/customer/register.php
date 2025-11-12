<?php
session_start();
require_once('config/config.php');

$success = $err = '';

if (isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $err = "All fields are required.";
    } elseif ($password !== $confirm) {
        $err = "Passwords do not match.";
    } else {
        try {
            // Check if email already exists
            $check = $pdo->prepare("SELECT * FROM rpos_customers WHERE customer_email = :email");
            $check->execute(['email' => $email]);

            if ($check->rowCount() > 0) {
                $err = "Email is already registered.";
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Generate a unique customer ID
                $customer_id = uniqid("CUST-", true);

                // Insert new customer
                $stmt = $pdo->prepare("
                    INSERT INTO rpos_customers (customer_id, customer_name, customer_email, customer_password)
                    VALUES (:cid, :name, :email, :password)
                ");
                $stmt->execute([
                    'cid' => $customer_id,
                    'name' => $name,
                    'email' => $email,
                    'password' => $hashedPassword
                ]);

                $success = "Registration successful! Redirecting to login...";
                header("refresh:2; url=login.php");
            }
        } catch (PDOException $e) {
            $err = "Database error: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Registration | Restaurant POS</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <link href="../admin/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../admin/assets/css/argon.css?v=1.0.0" rel="stylesheet">
</head>
<body class="bg-gradient-dark">

  <div class="main-content">
    <div class="header bg-gradient-dark py-7 py-lg-8">
      <div class="container text-center text-white">
        <h1 class="display-4">Create Your Account</h1>
        <p class="lead">Register to place orders and view your receipts</p>
      </div>
    </div>

    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
          <div class="card bg-secondary shadow border-0">
            <div class="card-header bg-transparent text-center">
              <h2>Customer Registration</h2>
            </div>
            <div class="card-body px-lg-5 py-lg-5">
              <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
              <?php if (!empty($err)) echo "<div class='alert alert-danger'>$err</div>"; ?>

              <form method="POST">
                <div class="form-group mb-3">
                  <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="form-group mb-3">
                  <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                </div>
                <div class="form-group mb-3">
                  <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="form-group mb-3">
                  <input type="password" name="confirm" class="form-control" placeholder="Confirm Password" required>
                </div>
                <div class="text-center">
                  <button type="submit" name="register" class="btn btn-success my-4">Register</button>
                </div>
              </form>
            </div>
          </div>

          <div class="text-center mt-3">
            <small>Already have an account?</small>
            <a href="login.php" class="text-light"><strong>Login</strong></a>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
