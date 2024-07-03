<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');
?>

<main id="main" class="main">
  <div class="page">
    <div class="container-fluid">

      <div class="col-12 col-md-6 col-lg-8 mx-auto">
        <div class="card">
          <div class="card-header text-center">
            <strong>
              <i class="bi bi-file-earmark-plus pe-1"></i>
              Add Image
            </strong>
          </div>
          <div class="card-body">
            <?php

            if (isset($_POST['submit'])) {

              $title = $_POST['title'];
              $filename = $_FILES['dataFile']['name'];
              $filesize = $_FILES['dataFile']['size'];
              
              if ($title != "" && $filename != "") {
                $explode = explode('.', $filename);
                $file = strtolower($explode[0]);
                $ext = strtolower($explode[1]);
                $finalname = $file . time() . '.' . $ext;
                if ($filesize >= 2048) {
                  if ($ext == "png" || $ext == "jpg" || $ext == "jpeg") {
                    if (move_uploaded_file($_FILES['dataFile']['tmp_name'], BASE_PATH . '/uploads/' . $finalname)) {
                      $insert = "INSERT INTO media(title,img_link,type)  
        VALUES ('$title', '$finalname', '$ext')";
                      $result = mysqli_query($con, $insert);
                      if ($result) {
            ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                          <strong>Image is submitted</strong>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      <?php
                        echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?success\">";
                      } else {
                      ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <strong>Image was not submitted</strong>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      <?php
                        echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?fail\">";
                      }
                    } else {
                      ?>
                      <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Image was not uploaded</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    <?php
                      echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?notuploaded\">";
                    }
                  } else {
                    ?>
                    <div class=" container alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>File extension not supported! (must be .png, .jpg, or .jpeg)</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php
                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?extensionfail\">";
                  }
                } else {
                  ?>
                  <div class=" container alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>File size must be at least 2kb</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                <?php
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?filesize\">";
                }
              } else {
                ?>
                <div class=" container alert alert-warning alert-dismissible fade show" role="alert">
                  <strong>All Field must be Filled!</strong>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
                echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?empty\">";
              }
            }

            ?>
            <!-- Multi Columns Form -->
            <form class="row g-3" method="POST" enctype="multipart/form-data">
              <div class="col-md-12">
                <label for="inputName5" class="form-label fw-bold">Img Title</label>
                <input type="text" class="form-control" name="title" id="inputName5">
              </div>
              <div class="col-md-12">
                <label for="inputEmail5" class="form-label fw-bold">Image</label>
                <input type="file" class="form-control" name="dataFile" id="inputEmail5">
              </div>

              <div class="col-md-12 pt-2">
                <button type="submit" name="submit" class="btn btn-custom fw-bold">Add Image</button>
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