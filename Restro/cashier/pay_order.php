<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
require_once('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
  if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $pay_code = $_POST['pay_code'];
    $order_code = $_GET['order_code'];
    $customer_id = $_GET['customer_id'];
    $pay_amt  = $_POST['pay_amt'];
    $pay_method = $_POST['pay_method'];
    $pay_id = $_POST['pay_id'];
    $order_status = $_GET['order_status'];

    try {
      $pdo->beginTransaction();

      $stmt1 = $pdo->prepare("INSERT INTO rpos_payments (pay_id, pay_code, order_code, customer_id, pay_amt, pay_method)
                              VALUES (:pid, :pcode, :ocode, :cid, :amt, :pmethod)");
      $stmt1->execute([
        'pid' => $pay_id,
        'pcode' => $pay_code,
        'ocode' => $order_code,
        'cid' => $customer_id,
        'amt' => $pay_amt,
        'pmethod' => $pay_method
      ]);

      $stmt2 = $pdo->prepare("UPDATE rpos_orders SET order_status = :status WHERE order_code = :ocode");
      $stmt2->execute([
        'status' => $order_status,
        'ocode' => $order_code
      ]);

      $pdo->commit();
      $success = "Payment Successful";
      header("refresh:1; url=receipts.php");
    } catch (PDOException $e) {
      $pdo->rollBack();
      $err = "Error processing payment: " . htmlspecialchars($e->getMessage());
    }
  }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <?php
    $order_code = $_GET['order_code'] ?? null;
    if ($order_code) {
      try {
        $stmt = $pdo->prepare("SELECT * FROM rpos_orders WHERE order_code = :ocode");
        $stmt->execute(['ocode' => $order_code]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($order) {
          $total = $order['prod_price'] * $order['prod_qty'];
        }
      } catch (PDOException $e) {
        $err = "Error loading order: " . htmlspecialchars($e->getMessage());
      }
    }
    ?>

    <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
    </div>

    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0"><h3>Please Fill All Fields</h3></div>
            <div class="card-body">
              <form method="POST">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Payment ID</label>
                    <input type="text" name="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Code</label>
                    <input type="text" name="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Amount ($)</label>
                    <input type="text" name="pay_amt" readonly value="<?php echo $total ?? 0; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Method</label>
                    <select class="form-control" name="pay_method">
                      <option selected>Cash</option>
                      <option>Paypal</option>
                    </select>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="pay" value="Pay Order" class="btn btn-success">
                  </div>
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
