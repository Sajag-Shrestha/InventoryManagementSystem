<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');
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

      <div class="col-12 col-md-6 col-lg-8 mx-auto">
        <div class="card">
          <div class="card-header text-center">
            <strong>
              <i class="bi bi-patch-plus pe-1"></i>
              Add New Category
            </strong>
          </div>
          <div class="card-body">
            <?php

            if (isset($_POST['submit'])) {

              $name = $_POST['name'];


              if ($name != "") {
                $insert = "INSERT INTO categories(name)
VALUES('$name')";
                $result = mysqli_query($con, $insert);

                if ($result) {
            ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Category is created</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?success\">";
                } else {
                ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Category is not created</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?error\">";
                }
              } else {
                ?>
                <div class=" container alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>All Field must be Filled!</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
                echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php\">";
              }
            }

            ?>
            <!-- Multi Columns Form -->
            <form class="row g-3" method="POST" enctype="multipart/form-data">
              <div class="col-md-12">
                <label for="inputName5" class="form-label fw-bold">Category Name</label>
                <input type="text" class="form-control" name="name" id="inputName5" placeholder="E.g. Luxury Goods">
              </div>

              <div class="col-md-12 pt-2">
                <button type="submit" name="submit" class="btn btn-custom fw-bold">Add Category</button>
              </div>
            </form><!-- End Multi Columns Form -->
          </div>
        </div>
      </div>

    </div>
  </div>
</main>

<?php
require(BASE_PATH . '/layouts/footer.php');
?>