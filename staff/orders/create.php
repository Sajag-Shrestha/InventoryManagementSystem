<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar-staff.php');

// Fetch products and media links from the database
$select_products = "SELECT p.id, p.name, p.price, m.img_link 
                    FROM products p
                    JOIN media m ON p.img_link = m.id";
$result_products = mysqli_query($con, $select_products);
$products = [];
while ($row = mysqli_fetch_assoc($result_products)) {
    $products[$row['id']] = $row;
}

?>

<main id="main" class="main">
  <div class="page">
    <div class="container-fluid">
      <div class="col-12 col-md-6 col-lg-8 mx-auto pb-3">
        <div class="card">
          <div class="card-header text-center">
            <strong>
              <i class="material-symbols-outlined pe-1">box_add</i>
              Create Order
            </strong>
          </div>
          <div class="card-body">

            <?php if (isset($_GET['success'])) { ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Order created successfully!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php\">"; ?>
            <?php } ?>

            <?php if (isset($_GET['error'])) { ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Failed to create order!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php\">"; ?>
            <?php } ?>

            <?php if (isset($_GET['duplicate'])) { ?>
              <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Email or Phone no. already exists</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php\">"; ?>
            <?php } ?>

            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
              $customer_id = $_POST['customer_id'];
              $shipping_address = $_POST['shipping_address'];
              $billing_address = $_POST['billing_address'];
              $status = 'Pending';

              // Fetch tax rate from settings
              $tax_rate = floatval(getSetting('tax_rate')); 

              // Insert into orders table
              $insert_order = "INSERT INTO orders (customer_id, status, shipping_address, billing_address) VALUES ('$customer_id', '$status', '$shipping_address', '$billing_address')";
              $result = mysqli_query($con, $insert_order);

              if ($result) {
                $order_id = mysqli_insert_id($con);
                $total_amount = 0.00;

                // Process each selected product and its quantity
                foreach ($_POST['products'] as $product) {
                  $product_id = $product['product_id'];
                  $quantity = $product['quantity'];
                  $price = $product['price'];
                  $img_link = $products[$product_id]['img_link']; // Correctly index the products array
                  $product_name = $products[$product_id]['name']; // Correctly index the products array
                  $subtotal = floatval($quantity) * floatval($price);
                  $total_amount += $subtotal;

                  // Insert into order_items table
                  $insert_item = "INSERT INTO order_items (order_id, product_id, product_name, img_link, quantity, price) VALUES ('$order_id', '$product_id', '$product_name', '$img_link', '$quantity', '$price')";
                  mysqli_query($con, $insert_item);
                }

                // Apply tax rate to total_amount
                $total_amount_with_tax = $total_amount * (1 + $tax_rate);

                // Update total_amount in orders table
                $update_order_amount = "UPDATE orders SET total_amount = '$total_amount_with_tax' WHERE order_id = '$order_id'";
                mysqli_query($con, $update_order_amount);

                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>Order created successfully!</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
                echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\">";
              } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Failed to create order.</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
                echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php\">";
              }
            }
            ?>
            <form method="POST">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="customer_id" class="form-label">Select Customer:</label>
                    <select class="form-select" name="customer_id" id="customer_id" required>
                      <option value="" disabled selected>Select Customer</option>
                      <?php
                      $select_customers = "SELECT id, name FROM customers";
                      $result_customers = mysqli_query($con, $select_customers);
                      while ($customer = mysqli_fetch_assoc($result_customers)) {
                        echo "<option value='{$customer['id']}'>{$customer['name']}</option>";
                      }
                      ?>
                    </select>
                    <div class="mt-2">
                      <button type="button" class="btn btn-custom" data-bs-toggle="modal" data-bs-target="#newCustomerModal">Add New Customer</button>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="shipping_address" class="form-label">Shipping Address:</label>
                    <textarea class="form-control" name="shipping_address" id="shipping_address" rows="2"></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="billing_address" class="form-label">Billing Address:</label>
                    <textarea class="form-control" name="billing_address" id="billing_address" rows="2"></textarea>
                  </div>
                  <hr>
                  <button type="submit" name="add_order" class="btn btn-custom fw-bold">Create Order</button>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-body">
                      <h5>Add Products:</h5>
                      <div id="product-list">
                        <!-- Product selection form fields will be added dynamically using JavaScript -->
                        <div class="product-field mb-3">
                          <label for="product_1" class="form-label">Product:</label>
                          <select class="form-select product-select" name="products[1][product_id]" required>
                            <option value="" disabled selected>Select Product</option>
                            <?php
                            foreach ($products as $product) {
                              echo "<option value='{$product['id']}' data-price='{$product['price']}'>{$product['name']}</option>";
                            }
                            ?>
                          </select>
                          <label for="quantity_1" class="form-label">Quantity:</label>
                          <input type="number" class="form-control quantity-input" name="products[1][quantity]" required>
                          <input type="hidden" name="products[1][price]" class="price-input" value="">
                        </div>
                      </div>
                      <button type="button" class="btn btn-custom mt-3" onclick="addProductField()">Add Product</button>
                    </div>
                  </div>
                </div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- New Customer Modal -->
<div class="modal fade" id="newCustomerModal" tabindex="-1" aria-labelledby="newCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <?php
      if (isset($_POST['register'])) {
        $customer_name = $_POST['customer_name'];
        $customer_email = $_POST['customer_email'];
        $customer_phone = $_POST['customer_phone'];
        $customer_address = $_POST['customer_address'];

        $select = "SELECT * FROM customers WHERE email = '$customer_email' OR phone = '$customer_phone'";
        $result = mysqli_query($con, $select);

        if ($result->num_rows > 0) {
          echo "<script>window.location.href = 'create.php?duplicate';</script>";
        } else {
          // Insert the new customer into the database
          $insert_customer_query = "INSERT INTO customers (name, email, phone, address) VALUES ('$customer_name', '$customer_email', '$customer_phone', '$customer_address')";
          $insert_customer_result = mysqli_query($con, $insert_customer_query);

          if ($insert_customer_result) {
            $new_customer_id = mysqli_insert_id($con);
            echo "<script>window.location.href = 'create.php?success';</script>";
          } else {
            echo "<script>window.location.href = 'create.php?error';</script>";
          }
        }
      }
      ?>

      <div class="modal-header">
        <h5 class="modal-title" id="newCustomerModalLabel">Add New Customer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="customer_name" class="form-label">Customer Name:</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
          </div>
          <div class="mb-3">
            <label for="customer_email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="customer_email" name="customer_email" required>
          </div>
          <div class="mb-3">
            <label for="customer_phone" class="form-label">Phone:</label>
            <input type="tel" class="form-control" id="customer_phone" name="customer_phone" required>
          </div>
          <div class="mb-3">
            <label for="customer_address" class="form-label">Address:</label>
            <textarea class="form-control" id="customer_address" name="customer_address" rows="2" required></textarea>
          </div>
          <button type="submit" name="register" class="btn btn-custom fw-bold">Add Customer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
require(BASE_PATH . '/layouts/footer.php');
?>

<script>
function addProductField() {
  const productCount = document.querySelectorAll('.product-field').length + 1;
  const productSelectOptions = '<?php foreach ($products as $product) { echo "<option value=\"{$product['id']}\" data-price=\"{$product['price']}\">{$product['name']}</option>"; } ?>';

  const productFieldHTML = `
    <div class="product-field mb-3">
      <label for="product_${productCount}" class="form-label">Product:</label>
      <select class="form-select product-select" name="products[${productCount}][product_id]" required>
        <option value="" disabled selected>Select Product</option>
        ${productSelectOptions}
      </select>
      <label for="quantity_${productCount}" class="form-label">Quantity:</label>
      <input type="number" class="form-control quantity-input" name="products[${productCount}][quantity]" required>
      <input type="hidden" name="products[${productCount}][price]" class="price-input" value="">
    </div>
  `;

  document.getElementById('product-list').insertAdjacentHTML('beforeend', productFieldHTML);
}

document.addEventListener('change', function(e) {
  if (e.target.classList.contains('product-select')) {
    const price = e.target.selectedOptions[0].getAttribute('data-price');
    e.target.closest('.product-field').querySelector('.price-input').value = price;
  }
});
</script>
