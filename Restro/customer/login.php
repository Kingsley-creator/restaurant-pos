<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $err = login_customer($email, $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Login | Restaurant POS</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
  <link href="../admin/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../admin/assets/css/argon.css?v=1.0.0" rel="stylesheet">
</head>
<body class="bg-gradient-dark">

  <div class="main-content">
    <div class="header bg-gradient-dark py-7 py-lg-8">
      <div class="container text-center text-white">
        <h1 class="display-4">Welcome Back, Customer</h1>
        <p class="lead">Log in to manage your orders and view receipts</p>
      </div>
    </div>

    <div class="container mt--8 pb-5">
      <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
          <div class="card bg-secondary shadow border-0">
            <div class="card-header bg-transparent text-center">
              <h2>Login</h2>
            </div>
            <div class="card-body px-lg-5 py-lg-5">
              <?php if (!empty($err)) echo "<div class='alert alert-danger'>$err</div>"; ?>

              <form method="POST">
                <div class="form-group mb-3">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                    </div>
                    <input class="form-control" placeholder="Email" name="email" type="email" required>
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                    </div>
                    <input class="form-control" placeholder="Password" name="password" type="password" required>
                  </div>
                </div>
                <div class="text-center">
                  <button type="submit" name="login" class="btn btn-success my-4">Log in</button>
                </div>
              </form>
            </div>
          </div>
          <div class="text-center mt-3">
            <small>Don’t have an account?</small>
            <a href="register.php" class="text-light"><strong>Register</strong></a>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
