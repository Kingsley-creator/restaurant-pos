<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
check_login();

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Validate product selection from URL
if (!isset($_GET['prod_id']) || !isset($_GET['prod_name']) || !isset($_GET['prod_price'])) {
    die("<h3 style='color:red; text-align:center;'>Invalid product selection!</h3>");
}

$prod_id = $_GET['prod_id'];
$prod_name = $_GET['prod_name'];
$prod_price = $_GET['prod_price'];
$order_code = "ORD-" . strtoupper(substr(md5(time()), 0, 6));
$order_id = strtoupper(substr(uniqid(), 0, 10));
$success = $err = "";

// Fetch customer name
try {
    $stmt = $pdo->prepare("SELECT customer_name FROM rpos_customers WHERE customer_id = :cid LIMIT 1");
    $stmt->execute(['cid' => $customer_id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        die("<h3 style='color:red;text-align:center;'>Customer not found.</h3>");
    }
    $customer_name = $customer['customer_name'];
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
}

// Handle order submission
if (isset($_POST['make'])) {
    $prod_qty = $_POST['prod_qty'];

    if (empty($prod_qty)) {
        $err = "Please enter quantity before placing the order.";
    } else {
        try {
            $insert = $pdo->prepare("
                INSERT INTO rpos_orders 
                (order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price, prod_qty, order_status)
                VALUES 
                (:oid, :ocode, :cid, :cname, :pid, :pname, :pprice, :pqty, :status)
            ");

            $insert->execute([
                'oid' => $order_id,
                'ocode' => $order_code,
                'cid' => $customer_id,
                'cname' => $customer_name,
                'pid' => $prod_id,
                'pname' => $prod_name,
                'pprice' => $prod_price,
                'pqty' => $prod_qty,
                'status' => 'Pending'
            ]);

            $success = "Order placed successfully! Redirecting to Receipts...";
            header("refresh:2; url=receipts.php");
        } catch (PDOException $e) {
            $err = "Failed to place order: " . htmlspecialchars($e->getMessage());
        }
    }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>

  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div class="header bg-gradient-dark pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body text-white">
          <h2>Confirm Your Order</h2>
          <p>Review product details and complete your order below.</p>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow border-0">
            <div class="card-header border-0">
              <h3 class="mb-0">Order Form</h3>
            </div>
            <div class="card-body">

              <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
              <?php elseif ($err): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
              <?php endif; ?>

              <form method="POST">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Customer Name</label>
                    <input type="text" readonly value="<?= htmlspecialchars($customer_name); ?>" class="form-control">
                  </div>

                  <div class="col-md-6">
                    <label>Order Code</label>
                    <input type="text" readonly value="<?= htmlspecialchars($order_code); ?>" class="form-control">
                  </div>
                </div>

                <hr>

                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Name</label>
                    <input type="text" readonly value="<?= htmlspecialchars($prod_name); ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Price ($)</label>
                    <input type="text" readonly value="<?= htmlspecialchars($prod_price); ?>" class="form-control">
                  </div>
                </div>

                <div class="form-row mt-3">
                  <div class="col-md-6">
                    <label>Quantity</label>
                    <input type="number" name="prod_qty" min="1" required class="form-control">
                  </div>
                </div>

                <br>

                <div class="form-row">
                  <div class="col-md-6">
                    <button type="submit" name="make" class="btn btn-success">Confirm Order</button>
                    <a href="orders.php" class="btn btn-secondary">Cancel</a>
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
