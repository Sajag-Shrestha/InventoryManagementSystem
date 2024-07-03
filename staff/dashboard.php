<?php
require_once(__DIR__ . '/../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar-staff.php');
?>

<?php
$c_user = countIds($con, 'users');
$c_category = countIds($con, 'categories');
$c_product = countIds($con, 'products');
$c_order = countOrderIds($con, 'orders');
?>


<main id="main" class="main">
  <div class="page">
    <div class="container-fluid">
      <?php
      if (isset($_GET['success'])) {
      ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Logged In Successfully!</strong>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php
        echo "<meta http-equiv=\"refresh\" content=\"2;URL=dashboard.php\">";
      }
      ?>
      <div class="row">
        <div class="col-md-6">
        </div>
      </div>
      <div class="row">
        
        <div class="col-md-4 col-sm-6 mb-4">
          <a href="categories/index.php" style="color:black;" class="text-decoration-none">
            <div class="card card-dash d-flex">
              <div class="card-icon d-flex align-items-center justify-content-center">
                <i class="material-symbols-outlined fs-2">category</i>
              </div>
              <div class="card-value d-flex flex-column justify-content-center align-items-center">
                <h2 class="card-body card-title"><?php echo $c_category; ?></h2>
                <p class="text-muted">Categories</p>
              </div>
            </div>
          </a>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
          <a href="products/index.php" style="color:black;" class="text-decoration-none">
            <div class="card card-dash d-flex">
              <div class="card-icon d-flex align-items-center justify-content-center">
                <i class="material-symbols-outlined fs-2">shopping_cart</i>
              </div>
              <div class="card-value d-flex flex-column justify-content-center align-items-center">
                <h2 class="card-body card-title"><?php echo $c_product; ?></h2>
                <p class="text-muted">Products</p>
              </div>
            </div>
          </a>
        </div>

        <div class="col-md-4 col-sm-6 mb-4">
          <a href="orders/index.php" style="color:black;" class="text-decoration-none">
            <div class="card card-dash d-flex">
              <div class="card-icon d-flex align-items-center justify-content-center">
                <i class="bi bi-box2-heart-fill fs-2"></i>
              </div>
              <div class="card-value d-flex flex-column justify-content-center align-items-center">
                <h2 class="card-body card-title"><?php echo $c_order; ?></h2>
                <p class="text-muted">Orders</p>
              </div>
            </div>
          </a>
        </div>

        <!-- Highest Selling Products Section -->
        <?php
        // Fetch highest selling products
        $query_highest_selling = "
  SELECT 
    p.name,
    SUM(oi.quantity) AS total_quantity,
    SUM(oi.quantity * oi.price) AS total_sales
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  JOIN orders o ON oi.order_id = o.order_id
  WHERE o.status = 'fulfilled'
  GROUP BY oi.product_id
  ORDER BY total_quantity DESC
  LIMIT 5
";
        $result_highest_selling = mysqli_query($con, $query_highest_selling);
        $highest_selling_products = mysqli_fetch_all($result_highest_selling, MYSQLI_ASSOC);
        ?>

        <div class="row mt-4">
          <div class="col-md-4 pb-3">
            <div class="card">
              <div class="card-header text-center">
                <strong>
                  <i class="bi bi-graph-up-arrow pe-1"></i>
                  Highest Selling Products
                </strong>
              </div>
              <div class="card-body">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th>Title</th>
                      <th>Total Sold</th>
                      <th>Total Quantity</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($highest_selling_products as $product) : ?>
                      <tr>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo number_format($product['total_sales'], 2); ?></td>
                        <td><?php echo $product['total_quantity']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <?php
          // Fetch latest sales
          $query_latest_sales = "
          SELECT 
            oi.order_item_id,  
            oi.product_id,
            p.name,
            o.order_date AS date, 
            (oi.quantity * oi.price) AS total_sale
          FROM order_items oi
          JOIN orders o ON oi.order_id = o.order_id
          JOIN products p ON oi.product_id = p.id
          WHERE o.status = 'fulfilled'
          ORDER BY o.order_date DESC
          LIMIT 5
        ";
          $result_latest_sales = mysqli_query($con, $query_latest_sales);
          $latest_sales = mysqli_fetch_all($result_latest_sales, MYSQLI_ASSOC);
          ?>

          <div class="col-md-4 pb-3">
            <div class="card">
              <div class="card-header text-center">
                <strong>
                  <i class="bi bi-clock-history pe-1"></i>
                  Latest Sales
                </strong>
              </div>
              <div class="card-body">
                <table class="table table-striped table-bordered">
                  <thead>
                    <tr>
                      <th class="text-center" style="width: 50px;">#</th>
                      <th>Product Name</th>
                      <th>Date</th>
                      <th>Total Sale</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($latest_sales as $index => $sale) : ?>
                      <tr>
                        <td class="text-center"><?php echo $index + 1; ?></td>
                        <td>
                          <a href="products/view.php?id=<?php echo (int)$sale['product_id']; ?>"> <!-- corrected column name -->
                            <?php echo htmlspecialchars($sale['name']); ?>
                          </a>
                        </td>
                        <td><?php echo htmlspecialchars($sale['date']); ?></td>
                        <td>$<?php echo number_format($sale['total_sale'], 2); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Recently Added Products Section -->
          <?php
          // Fetch recently added products
          $query_recent_products = "
  SELECT 
    p.id,
    p.name,
    p.price,
    p.created_at,
    m.img_link AS image,
    c.name AS category,
    IFNULL(m.id, '0') AS media_id
  FROM products p
  LEFT JOIN media m ON p.img_link = m.id
  LEFT JOIN categories c ON p.category = c.id
  ORDER BY p.created_at DESC
  LIMIT 5
";
          $result_recent_products = mysqli_query($con, $query_recent_products);
          $recent_products = mysqli_fetch_all($result_recent_products, MYSQLI_ASSOC);
          ?>

          <div class="col-md-4 pb-3">
            <div class="card">
              <div class="card-header text-center">
                <strong>
                  <i class="bi bi-box-seam pe-1"></i>
                  Recently Added Products
                </strong>
              </div>
              <div class="card-body">
                <div class="list-group">
                  <?php foreach ($recent_products as $recent_product) : ?>
                    <a class="list-group-item list-group-item-action d-flex align-items-center" href="products/view.php?id=<?php echo (int)$recent_product['id']; ?>">
                      <div class="flex-shrink-0">
                        <?php if ($recent_product['media_id'] === '0') : ?>
                          <img class="img-avatar img-thumbnail img-fluid rounded-circle" src="" alt="no image">
                        <?php else : ?>
                          <img class="img-avatar img-thumbnail img-fluid rounded-circle" src="<?php echo BASE_URL . '/uploads/' ?><?php echo $recent_product['image']; ?>" alt="">
                        <?php endif; ?>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1"><strong><?php echo remove_junk(first_character($recent_product['name'])); ?></strong></h6>
                        <span class="badge bg-warning">$<?php echo number_format($recent_product['price'], 2); ?></span>
                        <p class="mb-0"><?php echo remove_junk(first_character($recent_product['category'])); ?></p>
                      </div>
                    </a>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>

        </div>
</main>

<?php
require(BASE_PATH . '/layouts/footer.php');
?>