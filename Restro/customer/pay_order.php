<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
require_once('config/code-generator.php');
check_login();

if (!isset($_SESSION['customer_id'])) {
  header("Location: login.php");
  exit();
}

$customer_id = $_SESSION['customer_id'];
$success = $err = "";

// Payment submission
if (isset($_POST['pay'])) {
  if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST["pay_method"])) {
    $err = "All fields are required!";
  } else {
    $pay_code = trim($_POST['pay_code']);
    $pay_id = $_POST['pay_id'];
    $order_code = $_GET['order_code'] ?? '';
    $order_status = 'Paid';
    $pay_method = $_POST['pay_method'];
    $pay_amt = $_POST['pay_amt'];

    if (strlen($pay_code) !== 10) {
      $err = "Payment code must be exactly 10 characters long.";
    } else {
      try {
        $pdo->beginTransaction();

        $insert = $pdo->prepare("
          INSERT INTO rpos_payments 
          (pay_id, pay_code, order_code, customer_id, pay_amt, pay_method)
          VALUES (:pid, :pcode, :ocode, :cid, :pamt, :pmethod)
        ");
        $insert->execute([
          'pid' => $pay_id,
          'pcode' => $pay_code,
          'ocode' => $order_code,
          'cid' => $customer_id,
          'pamt' => $pay_amt,
          'pmethod' => $pay_method
        ]);

        $update = $pdo->prepare("
          UPDATE rpos_orders 
          SET order_status = :status 
          WHERE order_code = :ocode
        ");
        $update->execute([
          'status' => $order_status,
          'ocode' => $order_code
        ]);

        $pdo->commit();
        $success = "Payment recorded successfully! Redirecting...";
        header("refresh:2; url=index.php");
      } catch (PDOException $e) {
        $pdo->rollBack();
        $err = "Payment failed: " . htmlspecialchars($e->getMessage());
      }
    }
  }
}

$order_code = $_GET['order_code'] ?? null;
if (!$order_code) die("<h3 style='text-align:center;color:red;'>Invalid order reference.</h3>");

try {
  $stmt = $pdo->prepare("SELECT * FROM rpos_orders WHERE order_code = :ocode LIMIT 1");
  $stmt->execute(['ocode' => $order_code]);
  $order = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$order) die("<h3 style='text-align:center;color:red;'>Order not found.</h3>");

  $total = $order['prod_price'] * $order['prod_qty'];
} catch (PDOException $e) {
  die("Database error: " . htmlspecialchars($e->getMessage()));
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header bg-gradient-dark pb-8 pt-5 pt-md-8 text-white">
      <div class="container-fluid">
        <h2>Payment for Order: <?= htmlspecialchars($order_code); ?></h2>
        <p>Complete your payment below.</p>
      </div>
    </div>

    <div class="container-fluid mt--7">
      <div class="card shadow border-0">
        <div class="card-header border-0"><h3>Make Payment</h3></div>
        <div class="card-body">

          <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
          <?php elseif ($err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
          <?php endif; ?>

          <form method="POST">
            <div class="form-row">
              <div class="col-md-6">
                <label>Payment ID</label>
                <input type="text" name="pay_id" readonly value="<?= htmlspecialchars($payid); ?>" class="form-control">
              </div>
              <div class="col-md-6">
                <label>Payment Code</label>
                <small class="text-danger">10-character code if paying by cash</small>
                <input type="text" name="pay_code" maxlength="10" class="form-control" required>
              </div>
            </div>

            <hr>
            <div class="form-row">
              <div class="col-md-6">
                <label>Amount ($)</label>
                <input type="text" name="pay_amt" readonly value="<?= number_format($total, 2); ?>" class="form-control">
              </div>
              <div class="col-md-6">
                <label>Payment Method</label>
                <select class="form-control" name="pay_method" required>
                  <option selected>Cash</option>
                  <option>Paypal</option>
                </select>
              </div>
            </div>

            <br>
            <div class="form-row">
              <div class="col-md-6">
                <button type="submit" name="pay" class="btn btn-success">Pay Order</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
