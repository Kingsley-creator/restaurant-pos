<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

// Generate unique ID and code
function generateProductCode() {
  return strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4)) . '-' . rand(1000, 9999);
}

$prod_id = uniqid();
$prod_code = generateProductCode();

if (isset($_POST['addProduct'])) {
  if (empty($_POST["prod_name"]) || empty($_POST["prod_desc"]) || empty($_POST["prod_price"]) || empty($_POST["prod_stock"])) {
    $err = "Please fill all required fields.";
  } else {
    try {
      $prod_name = trim($_POST['prod_name']);
      $prod_desc = trim($_POST['prod_desc']);
      $prod_price = trim($_POST['prod_price']);
      $prod_stock = intval($_POST['prod_stock']);
      $prod_img = "";

      // Handle image upload
      if (!empty($_FILES["prod_img"]["name"])) {
        $targetDir = "../assets/img/products/";
        $fileName = basename($_FILES["prod_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["prod_img"]["tmp_name"], $targetFilePath)) {
          $prod_img = $fileName;
        } else {
          $err = "Image upload failed.";
        }
      }

      // Insert into DB
      $sql = "INSERT INTO rpos_products (prod_id, prod_code, prod_name, prod_img, prod_desc, prod_price, prod_stock, created_at)
              VALUES (:prod_id, :prod_code, :prod_name, :prod_img, :prod_desc, :prod_price, :prod_stock, CURRENT_TIMESTAMP)";
      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        'prod_id' => $prod_id,
        'prod_code' => $prod_code,
        'prod_name' => $prod_name,
        'prod_img' => $prod_img,
        'prod_desc' => $prod_desc,
        'prod_price' => $prod_price,
        'prod_stock' => $prod_stock
      ]);

      if ($stmt->rowCount() > 0) {
        $success = "Product added successfully!";
        header("refresh:1; url=products.php");
        exit();
      } else {
        $err = "Failed to add product.";
      }

    } catch (PDOException $e) {
      $err = "Database error: " . htmlspecialchars($e->getMessage());
    }
  }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header pb-8 pt-5 pt-md-8" style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;">
      <span class="mask bg-gradient-dark opacity-8"></span>
    </div>

    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Add New Product</h3>
            </div>
            <div class="card-body">
              <?php if (isset($err)) echo "<div class='alert alert-danger'>{$err}</div>"; ?>
              <?php if (isset($success)) echo "<div class='alert alert-success'>{$success}</div>"; ?>

              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Name</label>
                    <input type="text" name="prod_name" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label>Product Code</label>
                    <input type="text" name="prod_code" class="form-control" value="<?php echo $prod_code; ?>" readonly>
                  </div>
                </div>

                <hr>
                <div class="form-row">
                  <div class="col-md-4">
                    <label>Product Image</label>
                    <input type="file" name="prod_img" class="form-control-file">
                  </div>
                  <div class="col-md-4">
                    <label>Product Price ($)</label>
                    <input type="number" step="0.01" name="prod_price" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label>Initial Stock</label>
                    <input type="number" name="prod_stock" class="form-control" min="0" required>
                  </div>
                </div>

                <hr>
                <div class="form-group">
                  <label>Product Description</label>
                  <textarea rows="4" name="prod_desc" class="form-control" required></textarea>
                </div>

                <button type="submit" name="addProduct" class="btn btn-success">Add Product</button>
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
