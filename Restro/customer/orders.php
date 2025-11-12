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

// Handle new order submission
if (isset($_GET['prod_id']) && isset($_GET['prod_price']) && isset($_GET['prod_name'])) {
    if (isset($_POST['place_order'])) {
        $prod_id = $_GET['prod_id'];
        $prod_name = $_GET['prod_name'];
        $prod_price = $_GET['prod_price'];
        $prod_qty = $_POST['prod_qty'];
        $order_code = "ORD-" . strtoupper(substr(md5(time()), 0, 6));
        $order_status = '';

        try {
            $stmt = $pdo->prepare("
                INSERT INTO rpos_orders (order_code, customer_id, prod_id, prod_name, prod_price, prod_qty, order_status)
                VALUES (:order_code, :cid, :pid, :pname, :pprice, :pqty, :status)
            ");
            $stmt->execute([
                'order_code' => $order_code,
                'cid' => $customer_id,
                'pid' => $prod_id,
                'pname' => $prod_name,
                'pprice' => $prod_price,
                'pqty' => $prod_qty,
                'status' => $order_status
            ]);

            $success = "Order placed successfully!";
        } catch (PDOException $e) {
            $err = "Failed to place order: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Fetch available products
try {
    $prodStmt = $pdo->prepare("SELECT * FROM rpos_products ORDER BY created_at DESC");
    $prodStmt->execute();
    $products = $prodStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . htmlspecialchars($e->getMessage()));
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
          <h2>My Orders</h2>
          <p>Select a product below to make an order.</p>
        </div>
      </div>
    </div>

    <!-- Page Content -->
    <div class="container-fluid mt--7">
      <div class="row">
        <div class="col">
          <div class="card shadow border-0">
            <div class="card-header border-0">
              <h3 class="mb-0">Select Any Product To Make An Order</h3>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Product Code</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price ($)</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (empty($products)) {
                      echo "<tr><td colspan='5' class='text-center text-muted'>No products available.</td></tr>";
                  } else {
                      foreach ($products as $prod) {
                          $image = !empty($prod['prod_img'])
                              ? "../admin/assets/img/products/" . htmlspecialchars($prod['prod_img'])
                              : "../admin/assets/img/products/default.jpg";
                          
                          echo "
                            <tr>
                              <td><img src='{$image}' height='60' width='60' class='img-thumbnail'></td>
                              <td>{$prod['prod_code']}</td>
                              <td>{$prod['prod_name']}</td>
                              <td>$" . number_format($prod['prod_price'], 2) . "</td>
                              <td>
                                <a href='make_order.php?prod_id={$prod['prod_id']}&prod_name={$prod['prod_name']}&prod_price={$prod['prod_price']}'>
                                  <button class='btn btn-sm btn-warning'>
                                    <i class='fas fa-cart-plus'></i> Place Order
                                  </button>
                                </a>
                              </td>
                            </tr>
                          ";
                      }
                  }
                  ?>
                </tbody>
              </table>
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
