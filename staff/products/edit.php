<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar-staff.php');

// Fetching categories
$select_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($con, $select_categories);
$categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);

// Fetching images
$select_images = "SELECT * FROM media";
$result_images = mysqli_query($con, $select_images);
$images = mysqli_fetch_all($result_images, MYSQLI_ASSOC);

// Fetching product details along with linked image
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $select_product = "
    SELECT 
      p.id, p.name, p.stock, p.cost, p.price, p.created_at, p.category, p.img_link, p.description,
      m.img_link AS media_img_link, m.title AS media_title
    FROM products p
    JOIN media m ON p.img_link = m.id
    WHERE p.id='$id'
  ";
  $result_product = mysqli_query($con, $select_product);

  if (mysqli_num_rows($result_product) > 0) {
    $product = mysqli_fetch_assoc($result_product);
  } else {
    echo "Product not found.";
    exit; // or handle accordingly
  }
}
?>

?>

<main id="main" class="main">
  <div class="page">
    <div class="container-fluid">

      <div class="col-12 col-md-6 col-lg-8 mx-auto">
        <div class="card">
          <div class="card-header text-center">
            <strong>
              <i class="material-symbols-outlined pe-1">box_edit</i>
              Edit Product
            </strong>
          </div>
          <div class="card-body">

            <?php if (isset($_GET['success'])) { ?>
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Product added successfully!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=dashboard.php\">"; ?>
            <?php }

            if (isset($_POST['submit'])) {
              $name = $_POST['name'];
              $category = $_POST['category'];
              $img_link = $_POST['img_link'];
              $stock = $_POST['stock'];
              $cost = $_POST['cost'];
              $price = $_POST['price'];
              $description = $_POST['description'];

              if ($name != "" && $img_link != "" && $category != "" && $stock != "" && $cost != "" && $price != "" && $description != "") {
                $update = "UPDATE products SET name = '$name', description = '$description', img_link = '$img_link', category = '$category', stock = '$stock', cost = '$cost', price = '$price', updated_at = NOW() WHERE id = '$id'";
                $result = mysqli_query($con, $update);

                if ($result) {
                  echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <strong>Product updated successfully</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?success\">";
                } else {
                  echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <strong>Failed to update product</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?error\">";
                }
              } else {
                echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>No updates were made!</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
                echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?empty\">";
              }
            }
            ?>

            <!-- Multi Columns Form -->
            <form class="row g-3" method="POST">
              <div class="col-md-12 d-flex align-items-center">
                <span class="bg-icon">
                  <i class="bi bi-box-seam-fill"></i>
                </span>
                <input type="text" class="form-control form-product" name="name" placeholder="Product Name" id="inputName5" value="<?php echo $product['name']; ?>">
              </div>
              <div class="col-md-6">
                <select class="form-select fw-bold" name="category" id="inputSelect5">
                  <option value="" disabled>Select Product Category</option>
                  <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $product['category'] ? 'selected' : ''; ?>>
                      <?php echo $category['name']; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="col-md-6">
                <button type="button" class="btn btn-custom w-100" data-bs-toggle="modal" data-bs-target="#imageModal">Change Image</button>
                <input type="hidden" name="img_link" id="imgLink" value = "<?php echo $product['img_link']; ?>">
                <div id="selectedImagePreview" class="mt-2"">
                <img class="img-thumbnail" id ="" src="<?php echo BASE_URL . '/uploads/' ?><?php echo $product['media_img_link']; ?>" alt="<?php echo $data['media_title']; ?>">
              </div>
          </div>
          <!-- Additional form fields for stock, cost, price -->
          <div class="col-md-4 d-flex align-items-center">
            <span class="bg-icon">
              <i class="material-symbols-outlined fs-6">shopping_cart</i>
            </span>
            <input type="number" class="form-control form-product" name="stock" placeholder="Product Stock" id="inputStock5" value="<?php echo $product['stock']; ?>">
          </div>
          <div class="col-md-4 d-flex align-items-center">
            <span class="bg-icon">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <input type="number" class="form-control form-product" name="cost" placeholder="Cost" id="inputCost5" step="0.01" min="0" value="<?php echo $product['cost']; ?>">
          </div>
          <div class="col-md-4 d-flex align-items-center">
            <span class="bg-icon">
              <i class="bi bi-currency-dollar"></i>
            </span>
            <input type="number" class="form-control form-product" name="price" placeholder="Price" id="inputPrice5" step="0.01" min="0" value="<?php echo $product['price']; ?>">
          </div>
          <div class="col-md-12">
                <textarea name="description" class="form-control form-product" id="inputDesc" placeholder="Description for the Product..." rows="3"><?php echo $product['description']; ?></textarea>
              </div>

          <div class="col-md-12 pt-2">
            <button type="submit" name="submit" class="btn btn-custom fw-bold">Update Product</button>
          </div>
          </form><!-- End Multi Columns Form -->
        </div>
      </div>
    </div>


    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="imageModalLabel">Select Image</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <?php foreach ($images as $image) : ?>
                <div class="col-md-3 mb-3">
                  <div class="card h-100">
                    <label for="image<?php echo $image['id']; ?>">
                      <img src="<?php echo BASE_URL . '/uploads/' . $image['img_link']; ?>" class="card-img-top img-thumbnail" alt="<?php echo $image['title']; ?>">
                    </label>
                    <div class="card-body text-center">
                      <input type="radio" name="selectedImage" value="<?php echo $image['id']; ?>" data-img-link="<?php echo $image['img_link']; ?>" id="image<?php echo $image['id']; ?>">
                      <label for="image<?php echo $image['id']; ?>"><?php echo $image['title']; ?></label>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-custom" id="confirmImageSelection">Confirm Selection</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</main>

<?php
require(BASE_PATH . '/layouts/footer.php');
?>