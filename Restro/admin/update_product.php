<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

// Check if product ID is provided
if (!isset($_GET['update'])) {
  header("Location: products.php");
  exit();
}

$update = $_GET['update'];

// Fetch current product info
try {
  $stmt = $pdo->prepare("SELECT * FROM rpos_products WHERE prod_id = :id");
  $stmt->execute(['id' => $update]);
  $product = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$product) {
    $err = "Product not found.";
  }
} catch (PDOException $e) {
  $err = "Error fetching product: " . htmlspecialchars($e->getMessage());
}

// Handle Update
if (isset($_POST['UpdateProduct'])) {
  if (empty($_POST["prod_name"]) || empty($_POST["prod_desc"]) || empty($_POST["prod_price"]) || empty($_POST["prod_stock"])) {
    $err = "Blank values not accepted!";
  } else {
    try {
      $prod_name  = trim($_POST['prod_name']);
      $prod_desc  = trim($_POST['prod_desc']);
      $prod_price = trim($_POST['prod_price']);
      $prod_stock = intval($_POST['prod_stock']);
      $prod_img   = $product['prod_img']; // Keep existing image by default

      // Handle new image upload
      if (!empty($_FILES["prod_img"]["name"])) {
        $targetDir = "../assets/img/products/";
        $fileName = basename($_FILES["prod_img"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["prod_img"]["tmp_name"], $targetFilePath)) {
          $prod_img = $fileName;
        } else {
          $err = "Error uploading new image.";
        }
      }

      // Update the product record
      $sql = "UPDATE rpos_products 
              SET prod_name = :prod_name, 
                  prod_img = :prod_img, 
                  prod_desc = :prod_desc, 
                  prod_price = :prod_price,
                  prod_stock = :prod_stock
              WHERE prod_id = :prod_id";

      $stmt = $pdo->prepare($sql);
      $stmt->execute([
        'prod_name' => $prod_name,
        'prod_img'  => $prod_img,
        'prod_desc' => $prod_desc,
        'prod_price'=> $prod_price,
        'prod_stock'=> $prod_stock,
        'prod_id'   => $update
      ]);

      if ($stmt->rowCount() > 0) {
        $success = "Product updated successfully!";
        header("refresh:1; url=products.php");
        exit();
      } else {
        $err = "No changes made or update failed.";
      }
    } catch (PDOException $e) {
      $err = "Database error: " . htmlspecialchars($e->getMessage());
    }
  }
}

require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>

  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid"><div class="header-body"></div></div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Update Product</h3>
            </div>
            <div class="card-body">

              <?php if (isset($err)) echo "<div class='alert alert-danger'>{$err}</div>"; ?>
              <?php if (isset($success)) echo "<div class='alert alert-success'>{$success}</div>"; ?>

              <?php if (!empty($product)): ?>
              <form method="POST" enctype="multipart/form-data">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Product Name</label>
                    <input type="text" name="prod_name" value="<?php echo htmlspecialchars($product['prod_name']); ?>" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label>Product Code</label>
                    <input type="text" value="<?php echo htmlspecialchars($product['prod_code']); ?>" class="form-control" readonly>
                  </div>
                </div>

                <hr>
                <div class="form-row">
                  <div class="col-md-4">
                    <label>Current Image</label><br>
                    <?php
                      $imgPath = "../assets/img/products/" . htmlspecialchars($product['prod_img']);
                      if (!empty($product['prod_img']) && file_exists($imgPath)) {
                        echo "<img src='{$imgPath}' height='80' width='80' class='rounded mb-2'>";
                      } else {
                        echo "<img src='../assets/img/products/default.jpg' height='80' width='80' class='rounded mb-2'>";
                      }
                    ?>
                    <input type="file" name="prod_img" class="form-control-file mt-2">
                  </div>

                  <div class="col-md-4">
                    <label>Product Price ($)</label>
                    <input type="number" step="0.01" name="prod_price" value="<?php echo htmlspecialchars($product['prod_price']); ?>" class="form-control" required>
                  </div>

                  <div class="col-md-4">
                    <label>Available Stock</label>
                    <input type="number" name="prod_stock" value="<?php echo htmlspecialchars($product['prod_stock']); ?>" class="form-control" min="0" required>
                  </div>
                </div>

                <hr>
                <div class="form-row">
                  <div class="col-md-12">
                    <label>Product Description</label>
                    <textarea rows="5" name="prod_desc" class="form-control" required><?php echo htmlspecialchars($product['prod_desc']); ?></textarea>
                  </div>
                </div>

                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="UpdateProduct" value="Update Product" class="btn btn-success">
                    <a href="products.php" class="btn btn-secondary ml-2">Cancel</a>
                  </div>
                </div>
              </form>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <!-- Scripts -->
  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
