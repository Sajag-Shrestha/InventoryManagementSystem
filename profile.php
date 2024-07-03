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
        <div class="col-md-12">
          <div class="card">
            <div class="card-header text-center">
              <strong>
                <i class="bi bi-person-fill"></i>
                My Profile
              </strong>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-4">
                  <div class="text-center">
                    <div class="mb-3">
                      <img src="<?php echo BASE_URL . '/uploads/users/' . $data['image'] ?>" alt="Profile Image" class="rounded-circle img-thumbnail" width="150">
                    </div>
                    <div>
                      <a href="profile-setting.php" class="btn btn-custom fw-bold">Change Image</a>
                    </div>
                  </div>
                </div>
                <div class="col-md-8">
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Name:</strong> <?php echo $data['name']; ?></li>
                    <li class="list-group-item"><strong>Email:</strong> <?php echo $data['email']; ?></li>
                    <li class="list-group-item"><strong>Username:</strong> <?php echo $data['username']; ?></li>
                  </ul>
                </div>
              </div>
            </div>
            <div class="card-footer text-center">
              <a href="profile-setting.php" class="btn btn-custom fw-bold">Edit Profile</a>
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
