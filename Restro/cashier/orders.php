<?php
session_start();
require_once('../includes/config.php');
require_once('config/checklogin.php');
check_login();
require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>
    <div class="header pb-8 pt-5 pt-md-8" style="background-image: url(../admin/assets/img/theme/restro00.jpg); background-size: cover;">
      <span class="mask bg-gradient-dark opacity-8"></span>
    </div>

    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">Select a Product to Make an Order</div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th>Image</th>
                    <th>Product Code</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  try {
                    $stmt = $pdo->query("SELECT * FROM rpos_products ORDER BY created_at DESC");
                    foreach ($stmt as $prod) {
                      $imgPath = !empty($prod['prod_img']) ? "../admin/assets/img/products/{$prod['prod_img']}" : "../admin/assets/img/products/default.jpg";
                      echo "<tr>
                            <td><img src='{$imgPath}' height='60' width='60' class='img-thumbnail'></td>
                            <td>{$prod['prod_code']}</td>
                            <td>{$prod['prod_name']}</td>
                            <td>\${$prod['prod_price']}</td>
                            <td>
                              <a href='make_oder.php?prod_id={$prod['prod_id']}&prod_name={$prod['prod_name']}&prod_price={$prod['prod_price']}'>
                                <button class='btn btn-sm btn-warning'><i class='fas fa-cart-plus'></i> Place Order</button>
                              </a>
                            </td>
                          </tr>";
                    }
                  } catch (PDOException $e) {
                    echo "<tr><td colspan='5'>Error loading products: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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
