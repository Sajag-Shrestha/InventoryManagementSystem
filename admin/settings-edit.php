<?php
require_once(__DIR__ . '/../configuration.php');
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
                            Edit Setting
                        </strong>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $select = "SELECT * FROM settings WHERE id='$id'";
                            $result = mysqli_query($con, $select);

                            if (mysqli_num_rows($result) > 0) {
                                $data = mysqli_fetch_assoc($result);
                            } else {
                                echo "Setting not found.";
                                exit; // or handle accordingly
                            }
                        }

                        if (isset($_POST['submit'])) {
                            $value = $_POST['value'];

                            if ($value != "") {
                                $update = "UPDATE settings SET value='$value', updated_at=NOW() WHERE id='$id'";
                                $result = mysqli_query($con, $update);

                                if ($result) {
                        ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Setting is Updated</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=settings.php?success\">";
                                } else {
                                ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Setting is not Updated</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                        <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=settings-edit.php?error\">";
                                }
                            } else {
                                echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong>No updates were made!</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
                                echo "<meta http-equiv=\"refresh\" content=\"2;URL=settings-edit.php?empty\">";
                            }
                        }

                        ?>
                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" enctype="multipart/form-data">
                            <div class="col-md-6">
                                <label for="inputName5" class="form-label fw-bold">Setting</label>
                                <input type="text" class="form-control" name="name" id="inputName5" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="inputEmail5" class="form-label fw-bold">Value</label>
                                <input type="text" class="form-control" name="value" id="inputValue5" value="<?php echo isset($data['value']) ? $data['value'] : ''; ?>">
                            </div>
                            
                            <div class="col-md-12 pt-2">
                                <button type="submit" name="submit" class="btn btn-custom fw-bold"><?php echo isset($_GET['id']) ? 'Update User' : 'Add User'; ?></button>
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