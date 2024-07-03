<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar-staff.php');
?>

<main id="main" class="main">
    <div class="page">
        <div class="container-fluid">

            <div class="col-12 col-md-6 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header text-center">
                        <strong>
                            <i class="bi bi-person-fill-exclamation pe-1"></i>
                            Edit Categories
                        </strong>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $select = "SELECT * FROM categories WHERE id='$id'";
                            $result = mysqli_query($con, $select);

                            if (mysqli_num_rows($result) > 0) {
                                $data = mysqli_fetch_assoc($result);
                            } else {
                                echo "Category not found.";
                                exit; // or handle accordingly
                            }
                        }

                        if (isset($_POST['submit'])) {
                            $name = $_POST['name'];

                            if ($name != "") {
                                $update = "UPDATE categories SET name='$name', updated_at=NOW() WHERE id='$id'";
                                $result = mysqli_query($con, $update);

                                if ($result) {
                        ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Category is Updated</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php?success\">";
                                } else {
                                ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Category is not Updated</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=create.php?error\">";
                                }
                            } else {
                                ?>
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>No updates were made!</strong>
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
                                <label for="inputName5" class="form-label fw-bold">Category Name</label>
                                <input type="text" class="form-control" name="name" id="inputName5" value="<?php echo isset($data['name']) ? $data['name'] : ''; ?>">
                            </div>


                            <div class="col-md-12 pt-2">
                                <button type="submit" name="submit" class="btn btn-custom fw-bold">Update Category</button>
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