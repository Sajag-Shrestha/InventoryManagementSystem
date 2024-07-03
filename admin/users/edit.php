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
                            <i class="bi bi-person-fill-exclamation pe-1"></i>
                            Edit User
                        </strong>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $select = "SELECT * FROM users WHERE id='$id'";
                            $result = mysqli_query($con, $select);

                            if (mysqli_num_rows($result) > 0) {
                                $data = mysqli_fetch_assoc($result);
                            } else {
                                echo "User not found.";
                                exit; // or handle accordingly
                            }
                        }

                        if (isset($_POST['register'])) {
                            $name = $_POST['name'];
                            $username = $_POST['username'];
                            $user_role = $_POST['user_role'];
                            $email = $_POST['email'];

                            if ($name != "" && $username != "" && $email != "" && $user_role != "") {
                                $update = "UPDATE users SET name='$name', email='$email', username='$username', user_role='$user_role', password=MD5('$password'), updated_at=NOW() WHERE id='$id'";
                                $result = mysqli_query($con, $update);

                                if ($result) {
                        ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>User is Updated</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?success\">";
                                } else {
                                ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>User is not Updated</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                        <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?error\">";
                                }
                            } else {
                                echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>No updates were made!</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
                                echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?empty\">";
                            }
                        }

                        ?>
                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" enctype="multipart/form-data">
                            <div class="col-md-6">
                                <label for="inputName5" class="form-label fw-bold">Your Name</label>
                                <input type="text" class="form-control" name="name" id="inputName5" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="inputEmail5" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" name="email" id="inputEmail5" value="<?php echo isset($data['email']) ? $data['email'] : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="inputUsername5" class="form-label fw-bold">Username</label>
                                <input type="text" class="form-control" name="username" id="inputName5" value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="inputSelect5" class="form-label fw-bold">User Role</label>
                                <select class="form-select" name="user_role" id="inputSelect5">
                                    <option value="Staff" <?php echo (isset($data['user_role']) && $data['user_role'] == 'Staff') ? 'selected' : ''; ?>>Staff</option>
                                    <option value="Admin" <?php echo (isset($data['user_role']) && $data['user_role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>

                            <div class="col-md-12 pt-2">
                                <button type="submit" name="register" class="btn btn-custom fw-bold"><?php echo isset($_GET['id']) ? 'Update User' : 'Add User'; ?></button>
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