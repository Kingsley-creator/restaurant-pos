<?php
session_start();
require_once('config/config.php');
require_once('config/checklogin.php');
require_once('config/code-generator.php');

check_login();

if (isset($_POST['make'])) {
  if (empty($_POST["order_code"]) || empty($_POST["customer_name"]) || empty($_GET['prod_price'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $order_id = $_POST['order_id'];
    $order_code  = $_POST['order_code'];
    $customer_id = $_POST['customer_id'];
    $customer_name = $_POST['customer_name'];
    $prod_id  = $_GET['prod_id'];
    $prod_name = $_GET['prod_name'];
    $prod_price = $_GET['prod_price'];
    $prod_qty = $_POST['prod_qty'];

    try {
      $stmt = $pdo->prepare("INSERT INTO rpos_orders (prod_qty, order_id, order_code, customer_id, customer_name, prod_id, prod_name, prod_price) 
                             VALUES (:qty, :oid, :ocode, :cid, :cname, :pid, :pname, :pprice)");
      $stmt->execute([
        'qty' => $prod_qty,
        'oid' => $order_id,
        'ocode' => $order_code,
        'cid' => $customer_id,
        'cname' => $customer_name,
        'pid' => $prod_id,
        'pname' => $prod_name,
        'pprice' => $prod_price
      ]);
      $success = "Order Submitted";
      header("refresh:1; url=payments.php");
    } catch (PDOException $e) {
      $err = "Error submitting order: " . htmlspecialchars($e->getMessage());
    }
  }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>
    <div style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;" 
         class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
    </div>

    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Please Fill All Fields</h3>
            </div>
            <div class="card-body">
              <form method="POST">
                <div class="form-row">
                  <div class="col-md-4">
                    <label>Customer Name</label>
                    <select class="form-control" name="customer_name" id="custName" onChange="getCustomer(this.value)" required>
                      <option value="">Select Customer Name</option>
                      <?php
                      try {
                        $stmt = $pdo->query("SELECT * FROM rpos_customers ORDER BY customer_name ASC");
                        foreach ($stmt as $cust) {
                          echo "<option>" . htmlspecialchars($cust['customer_name']) . "</option>";
                        }
                      } catch (PDOException $e) {
                        echo "<option>Error loading customers</option>";
                      }
                      ?>
                    </select>
                    <input type="hidden" name="order_id" value="<?php echo $orderid; ?>" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label>Customer ID</label>
                    <input type="text" name="customer_id" readonly id="customerID" class="form-control">
                  </div>

                  <div class="col-md-4">
                    <label>Order Code</label>
                    <input type="text" name="order_code" value="<?php echo $alpha . '-' . $beta; ?>" class="form-control">
                  </div>
                </div>
                <hr>

                <?php
                $prod_id = $_GET['prod_id'] ?? null;
                if ($prod_id) {
                  try {
                    $stmt = $pdo->prepare("SELECT * FROM rpos_products WHERE prod_id = :pid");
                    $stmt->execute(['pid' => $prod_id]);
                    $prod = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($prod) {
                      echo "
                        <div class='form-row'>
                          <div class='col-md-6'>
                            <label>Product Price (\$)</label>
                            <input type='text' readonly name='prod_price' value='{$prod['prod_price']}' class='form-control'>
                          </div>
                          <div class='col-md-6'>
                            <label>Product Quantity</label>
                            <input type='number' name='prod_qty' class='form-control' required min='1'>
                          </div>
                        </div>
                      ";
                    }
                  } catch (PDOException $e) {
                    echo "<p class='text-danger'>Error fetching product info.</p>";
                  }
                }
                ?>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="make" value="Make Order" class="btn btn-success">
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
