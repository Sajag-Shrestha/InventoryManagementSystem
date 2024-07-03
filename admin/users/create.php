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
              <i class="bi bi-person-fill-add pe-1"></i>
              Add User
            </strong>
          </div>
          <div class="card-body">
            <?php

            if (isset($_POST['register'])) {
              $name = $_POST['name'];
              $email = $_POST['email'];
              $username = $_POST['username'];
              $password = md5($_POST['password']);
              $user_role = isset($_POST['user_role']) ? $_POST['user_role'] : '';

              if ($name != "" && $email != "" && $username != "" && $user_role != "") {
                $select = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
                $result = mysqli_query($con, $select);

                if ($result->num_rows > 0) {
                  echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
      <strong>Username or Email already exists</strong>
      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>";
                  echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?success\">";
                } else {

                  // Setting the default image based on user role
                  $default_image = '';
                  if ($user_role == 'Admin') {
                    $default_image = 'admin.png';
                  } else if ($user_role == 'Staff') {
                    $default_image = 'staff.png';
                  }

                  $insert = "INSERT INTO users (name, email, username, password, user_role, image) 
                     VALUES ('$name', '$email', '$username', '$password', '$user_role', '$default_image')";
                  $result = mysqli_query($con, $insert);


                  if ($result) {
            ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <strong>User is created</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                  <?php
                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?success\">";
                  } else {
                  ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      <strong>User is not created</strong>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?error\">";
                  }
                }
              } else {
                ?>
                <div class=" container alert alert-warning alert-dismissible fade show" role="alert">
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
              <div class="col-md-6">
                <label for="inputName5" class="form-label fw-bold">Your Name</label>
                <input type="text" class="form-control" name="name" id="inputName5">
              </div>
              <div class="col-md-6">
                <label for="inputEmail5" class="form-label fw-bold">Email</label>
                <input type="email" class="form-control" name="email" id="inputEmail5">
              </div>
              <div class="col-md-6">
                <label for="inputUsername5" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control" name="username" id="inputName5">
              </div>
              <div class="col-md-6">
                <label for="inputPassword5" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control" name="password" id="inputPassword5">
              </div>
              <div class="col-md-6">
                <label for="inputSelect5" class="form-label fw-bold">User Role</label>
                <select class="form-select" name="user_role" id="inputSelect5">
                  <option value="Staff">Staff</option>
                  <option value="Admin">Admin</option>
                </select>
              </div>

              <div class="col-md-12 pt-2">
                <button type="submit" name="register" class="btn btn-custom fw-bold">Add User</button>
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