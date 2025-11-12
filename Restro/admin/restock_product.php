<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

// Get product ID
if (!isset($_GET['prod_id'])) {
    header("Location: products.php");
    exit();
}

$prod_id = $_GET['prod_id'];

// Fetch product details
try {
    $stmt = $pdo->prepare("SELECT prod_name, prod_stock FROM rpos_products WHERE prod_id = :pid");
    $stmt->execute(['pid' => $prod_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $err = "Product not found.";
    }
} catch (PDOException $e) {
    $err = "Error fetching product: " . htmlspecialchars($e->getMessage());
}

// Handle restock submission
if (isset($_POST['addStock'])) {
    $quantity = intval($_POST['quantity']);
    $remarks = trim($_POST['remarks']);

    if ($quantity <= 0) {
        $err = "Quantity must be greater than zero.";
    } else {
        try {
            $pdo->beginTransaction();

            // Update product stock
            $updateStock = $pdo->prepare("UPDATE rpos_products SET prod_stock = prod_stock + :qty WHERE prod_id = :pid");
            $updateStock->execute(['qty' => $quantity, 'pid' => $prod_id]);

            // Log the stock addition
            $logStmt = $pdo->prepare("
                INSERT INTO rpos_stock_log (prod_id, change_type, quantity, remarks)
                VALUES (:pid, 'add', :qty, :remarks)
            ");
            $logStmt->execute([
                'pid' => $prod_id,
                'qty' => $quantity,
                'remarks' => $remarks
            ]);

            $pdo->commit();
            $success = "Stock updated successfully!";
            header("refresh:1; url=products.php");
            exit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            $err = "Error updating stock: " . htmlspecialchars($e->getMessage());
        }
    }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header bg-gradient-dark pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body">
          <h2 class="text-white">Add Stock for <?php echo htmlspecialchars($product['prod_name'] ?? ''); ?></h2>
        </div>
      </div>
    </div>

    <div class="container-fluid mt--7">
      <div class="row justify-content-center">
        <div class="col-lg-6">
          <div class="card shadow border-0">
            <div class="card-body">
              <?php if (isset($err)) echo "<div class='alert alert-danger'>{$err}</div>"; ?>
              <?php if (isset($success)) echo "<div class='alert alert-success'>{$success}</div>"; ?>

              <form method="POST">
                <div class="form-group">
                  <label>Current Stock</label>
                  <input type="number" class="form-control" value="<?php echo htmlspecialchars($product['prod_stock']); ?>" readonly>
                </div>

                <div class="form-group">
                  <label>Quantity to Add</label>
                  <input type="number" name="quantity" min="1" class="form-control" required>
                </div>

                <div class="form-group">
                  <label>Remarks (optional)</label>
                  <textarea name="remarks" rows="3" class="form-control" placeholder="e.g., Received new shipment from supplier"></textarea>
                </div>

                <div class="form-group">
                  <button type="submit" name="addStock" class="btn btn-success">
                    <i class="fas fa-check-circle"></i> Update Stock
                  </button>
                  <a href="products.php" class="btn btn-secondary ml-2">
                    <i class="fas fa-arrow-left"></i> Cancel
                  </a>
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
