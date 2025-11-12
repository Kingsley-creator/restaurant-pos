<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();

// Handle product deletion
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];

  try {
    $stmt = $pdo->prepare("DELETE FROM rpos_products WHERE prod_id = :id");
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() > 0) {
      $success = "Product deleted successfully.";
      header("refresh:1; url=products.php");
      exit();
    } else {
      $err = "Product not found or already deleted.";
    }
  } catch (PDOException $e) {
    $err = "Error deleting product: " . htmlspecialchars($e->getMessage());
  }
}

require_once('partials/_head.php');
?>

<body>
  <!-- Sidebar -->
  <?php require_once('partials/_sidebar.php'); ?>

  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php require_once('partials/_topnav.php'); ?>

    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;"
         class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body"></div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
              <a href="add_product.php" class="btn btn-outline-success">
                <i class="fas fa-utensils"></i> Add New Product
              </a>
              <?php if (isset($success)) echo "<div class='text-success font-weight-bold'>{$success}</div>"; ?>
              <?php if (isset($err)) echo "<div class='text-danger font-weight-bold'>{$err}</div>"; ?>
            </div>

            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Image</th>
                    <th scope="col">Product Code</th>
                    <th scope="col">Name</th>
                    <th scope="col">Price ($)</th>
                    <th scope="col">Stock</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                    $query = "SELECT * FROM rpos_products ORDER BY created_at DESC";
                    $stmt = $pdo->query($query);

                    foreach ($stmt as $prod):
                      $img = !empty($prod['prod_img']) ? htmlspecialchars($prod['prod_img']) : 'default.jpg';
                      $imgPath = "assets/img/products/" . $img;
                      $stock = isset($prod['prod_stock']) ? intval($prod['prod_stock']) : 0;
                  ?>
                      <tr>
                        <td><img src="<?php echo $imgPath; ?>" height="60" width="60" class="img-thumbnail"></td>
                        <td><?php echo htmlspecialchars($prod['prod_code']); ?></td>
                        <td><?php echo htmlspecialchars($prod['prod_name']); ?></td>
                        <td>$<?php echo number_format($prod['prod_price'], 2); ?></td>
                        <td>
                          <?php
                            if ($stock == 0) {
                              echo "<span class='badge badge-danger'>Out of Stock</span>";
                            } elseif ($stock < 5) {
                              echo "<span class='badge badge-warning'>{$stock} (Low)</span>";
                            } else {
                              echo "<span class='badge badge-success'>{$stock}</span>";
                            }
                          ?>
                        </td>
                        <td>
                          <a href="restock_product.php?prod_id=<?php echo urlencode($prod['prod_id']); ?>">
                            <button class="btn btn-sm btn-warning">
                              <i class="fas fa-plus-circle"></i> Add Stock
                            </button>
                          </a>

                          <a href="update_product.php?update=<?php echo urlencode($prod['prod_id']); ?>">
                            <button class="btn btn-sm btn-primary">
                              <i class="fas fa-edit"></i> Update
                            </button>
                          </a>

                          <a href="products.php?delete=<?php echo urlencode($prod['prod_id']); ?>"
                             onclick="return confirm('Are you sure you want to delete this product?');">
                            <button class="btn btn-sm btn-danger">
                              <i class="fas fa-trash"></i> Delete
                            </button>
                          </a>
                        </td>
                      </tr>
                  <?php
                    endforeach;
                  } catch (PDOException $e) {
                    echo "<tr><td colspan='6'>Error loading products: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
