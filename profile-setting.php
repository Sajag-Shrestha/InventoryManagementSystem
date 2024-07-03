<?php
require_once(__DIR__ . '/configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');

$id = $_SESSION['user_id'];

$select = "SELECT * FROM users WHERE id='$id'";
$result = mysqli_query($con, $select);

if (mysqli_num_rows($result) > 0) {
  $data = mysqli_fetch_assoc($result);
} else {
  echo "User not found.";
  exit;
}

?>

<main id="main" class="main">
  <div class="page">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-4">
          <div class="card">
            <div class="card-header text-center">
              <strong>
                <i class="bi bi-person-circle pe-1"></i>
                Profile Image
              </strong>
            </div>
            <div class="card-body text-center">
              <?php
              if (isset($_POST['update-img'])) {
                $filename = $_FILES['dataFile']['name'];
                $filesize = $_FILES['dataFile']['size'];

                if ($filename != "") {
                  if ($filesize >= 2048) {
                    $explode = explode('.', $filename);
                    $file = strtolower($explode[0]);
                    $ext = strtolower($explode[1]);
                    $finalname = $file . time() . '.' . $ext;
                    $target_file = BASE_PATH . '/uploads/users/' . $finalname;

                    if ($ext == "png" || $ext == "jpg" || $ext == "jpeg") {
                      // Check if old image should be deleted
                      if ($data['image'] != 'admin.png' && $data['image'] != 'staff.png') {
                        $oldfilelink = $data['image'];
                        $finallink = BASE_PATH . '/uploads/users/' . $oldfilelink;
                        unlink($finallink);
                      }

                      if (move_uploaded_file($_FILES['dataFile']['tmp_name'], $target_file)) {
                        $query = "UPDATE users SET image ='$finalname', updated_at=NOW() WHERE id=$id";
                        $result = mysqli_query($con, $query);

                        if ($result) {
              ?>
                          <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Profile Image updated!</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                        <?php
                          echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php\">";
                        } else {
                        ?>
                          <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Profile Image not updated</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                        <?php
                          echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?fail\">";
                        }
                      } else {
                        ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <strong>Failed to move uploaded image</strong>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                      <?php
                        echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?notuploaded\">";
                      }
                    } else {
                      ?>
                      <div class="container alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>File extension not supported! (must be .png, .jpg, or .jpeg)</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    <?php
                      echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?extensionfail\">";
                    }
                  } else {
                    ?>
                    <div class="container alert alert-warning alert-dismissible fade show" role="alert">
                      <strong>Image size must be at least 2kb</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php
                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?filesize\">";
                  }
                } else {
                  ?>
                  <div class="container alert alert-warning alert-dismissible fade show" role="alert">
                    <strong>No updates were made!</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
              <?php
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?empty\">";
                }
              }
              ?>



              <img src="<?php echo BASE_URL . '/uploads/users/' ?><?php echo $data['image'] ?>" alt="Profile Image" class="rounded-circle img-thumbnail" width="150">
              <form method="POST" enctype="multipart/form-data">
                <div class="mt-3">
                  <input type="file" class="form-control" name="dataFile">
                </div>
                <div class="mt-3">
                  <button type="submit" name="update-img" class="btn btn-custom fw-bold">Update Image</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col-md-8">
          <div class="card">
            <div class="card-header text-center">
              <strong>
                <i class="bi bi-person-fill"></i>
                Edit Profile
              </strong>
            </div>
            <div class="card-body">

              <?php

              if (isset($_POST['update'])) {
                $name = $_POST['name'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $password = $_POST['password'];
                $old_password = $data['password'];

                if ($password == "") {
                  $password_query = "UPDATE users SET password = MD5('$old_password') WHERE id = '$id'";
                  $pass = mysqli_query($con, $password_query);
                } elseif ($password != "") {
                  $password_query = "UPDATE users SET password = MD5('$password') WHERE id = '$id'";
                  $pass = mysqli_query($con, $password_query);
                }
                if ($name != "" && $username != "" && $email != "") {
                  $update = "UPDATE users SET name='$name', email='$email', username='$username', updated_at=NOW() WHERE id='$id'";
                  $result = mysqli_query($con, $update);

                  if ($result) {
              ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>Saved Changes</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php
                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?success\">";
                  } else {
                  ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>Changes not saved</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
              <?php
                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?error\">";
                  }
                } else {
                  echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>No updates were made!</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=profile-setting.php?empty\">";
                }
              }

              ?>

              <form method="POST">
                <div class="mb-3">
                  <label for="name" class="form-label fw-bold">Name</label>
                  <input type="text" class="form-control" name="name" value="<?php echo ($data['name']); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label fw-bold">Email</label>
                  <input type="email" class="form-control" name="email" value="<?php echo ($data['email']); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="username" class="form-label fw-bold">Username</label>
                  <input type="text" class="form-control" name="username" value="<?php echo ($data['username']); ?>" required>
                </div>
                <div class="mb-3">
                  <label for="password" class="form-label fw-bold">New Password (leave blank to keep current password)</label>
                  <input type="password" class="form-control" name="password">
                </div>
                <button type="submit" name="update" class="btn btn-custom fw-bold">Update Profile</button>
              </form>
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