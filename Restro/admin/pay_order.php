<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

if (!isset($_GET['order_code'], $_GET['customer_id'], $_GET['order_status'])) {
    header("Location: orders.php");
    exit();
}

$order_code = $_GET['order_code'];
$customer_id = $_GET['customer_id'];
$order_status = $_GET['order_status'];

try {
    // Start transaction to ensure stock and payment consistency
    $pdo->beginTransaction();

    // 1️⃣ Fetch order details
    $stmt = $pdo->prepare("SELECT * FROM rpos_orders WHERE order_code = :order_code LIMIT 1");
    $stmt->execute(['order_code' => $order_code]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        throw new Exception("Order not found.");
    }

    $prod_id   = $order['prod_id'];
    $prod_qty  = intval($order['prod_qty']);
    $pay_amt   = floatval($order['prod_price']) * $prod_qty;

    // 2️⃣ Check available stock
    $stockStmt = $pdo->prepare("SELECT prod_stock FROM rpos_products WHERE prod_id = :pid LIMIT 1");
    $stockStmt->execute(['pid' => $prod_id]);
    $stock = $stockStmt->fetchColumn();

    if ($stock === false) {
        throw new Exception("Product not found for this order.");
    }

    if ($stock < $prod_qty) {
        throw new Exception("Insufficient stock for this product.");
    }

    // 3️⃣ Deduct stock
    $updateStock = $pdo->prepare("UPDATE rpos_products SET prod_stock = prod_stock - :qty WHERE prod_id = :pid");
    $updateStock->execute(['qty' => $prod_qty, 'pid' => $prod_id]);

    // 4️⃣ Log stock deduction
    $logStmt = $pdo->prepare("
        INSERT INTO rpos_stock_log (prod_id, change_type, quantity, remarks)
        VALUES (:pid, 'deduct', :qty, :remarks)
    ");
    $logStmt->execute([
        'pid' => $prod_id,
        'qty' => $prod_qty,
        'remarks' => "Order payment processed: {$order_code}"
    ]);

    // 5️⃣ Record payment
    $pay_id = uniqid();
    $pay_code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10));
    $pay_method = 'Cash'; // Default, can be modified later

    $insertPayment = $pdo->prepare("
        INSERT INTO rpos_payments (pay_id, pay_code, order_code, customer_id, pay_amt, pay_method)
        VALUES (:pid, :pcode, :ocode, :cid, :amt, :method)
    ");
    $insertPayment->execute([
        'pid' => $pay_id,
        'pcode' => $pay_code,
        'ocode' => $order_code,
        'cid' => $customer_id,
        'amt' => $pay_amt,
        'method' => $pay_method
    ]);

    // 6️⃣ Update order status
    $updateOrder = $pdo->prepare("UPDATE rpos_orders SET order_status = :status WHERE order_code = :ocode");
    $updateOrder->execute(['status' => $order_status, 'ocode' => $order_code]);

    // ✅ Commit all changes
    $pdo->commit();

    $success = "Payment recorded and stock updated successfully!";
    header("refresh:2; url=orders.php");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    $err = "Error processing order: " . htmlspecialchars($e->getMessage());
}
?>

<?php require_once('partials/_head.php'); ?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="container-fluid mt-8">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card shadow mt-5">
            <div class="card-header border-0 bg-gradient-success text-white">
              <h3 class="mb-0">Process Payment</h3>
            </div>
            <div class="card-body">
              <?php if (isset($err)) echo "<div class='alert alert-danger'>{$err}</div>"; ?>
              <?php if (isset($success)) echo "<div class='alert alert-success'>{$success}</div>"; ?>

              <?php if (!isset($success) && !isset($err)): ?>
              <div class="alert alert-info">
                <strong>Confirm Payment:</strong> Are you sure you want to mark Order <b><?php echo htmlspecialchars($order_code); ?></b> as paid?
              </div>
              <a href="orders.php" class="btn btn-secondary">Cancel</a>
              <a href="?order_code=<?php echo urlencode($order_code); ?>&customer_id=<?php echo urlencode($customer_id); ?>&order_status=Paid"
                 class="btn btn-success">Confirm Payment</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php require_once('partials/_footer.php'); ?>
  </div>
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
