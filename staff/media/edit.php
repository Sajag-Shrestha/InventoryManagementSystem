<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar-staff.php');
?>

<main id="main" class="main">
    <div class="page">
        <div class="container-fluid">

            <div class="col-12 col-md-6 col-lg-8 mx-auto pb-3">
                <div class="card">
                    <div class="card-header text-center">
                        <strong>
                            <i class="bi bi-file-image pe-1"></i>
                            Edit Images
                        </strong>
                    </div>
                    <div class="card-body">
                        <?php

                        if (isset($_GET['id'])) {
                            $id = $_GET['id'];
                            $query = "SELECT * FROM media WHERE id=$id";
                            $result = mysqli_query($con, $query);
                            $data = mysqli_fetch_assoc($result);
                        }

                        if (isset($_POST['submit'])) {

                            $title = $_POST['title'];
                            $filename = $_FILES['dataFile']['name'];
                            $filesize = $_FILES['dataFile']['size'];

                            if ($title != "" && $filename != "") {
                                if ($filesize >= 2048) {
                                    $explode = explode('.', $filename);
                                    $file = strtolower($explode[0]);
                                    $ext = strtolower($explode[1]);
                                    $finalname = $file . time() . '.' . $ext;
                                    $target_file = BASE_PATH . '/uploads/' . $finalname;
                                    if ($ext == "png" || $ext == "jpg" || $ext == "jpeg") {
                                        $oldfilelink = $data['img_link'];
                                        $finallink = BASE_PATH . '/uploads/' . $oldfilelink;
                                        unlink($finallink);
                                        if (move_uploaded_file($_FILES['dataFile']['tmp_name'], $target_file)) {
                                            $query = "UPDATE media SET title='$title', img_link='$finalname', type='$ext', updated_at=NOW() WHERE id=$id";
                                            $result = mysqli_query($con, $query);
                                            if ($result) {
                                    ?>
                                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                    <strong>Image updated successfully</strong>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            <?php
                                                echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\">";
                                            } else {
                                            ?>
                                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                    <strong>Image was not updated</strong>
                                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                                </div>
                                            <?php
                                                echo "<meta http-equiv=\"refresh\" content=\"2;URL=edit.php?fail\">";
                                            }
                                        } else {
                                            ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <strong>Failed to move uploaded image</strong>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                            </div>
                                        <?php
                                            echo "<meta http-equiv=\"refresh\" content=\"2;URL=edit.php?notuploaded\">";
                                        }
                                    } else {
                                        ?>
                                        <div class=" container alert alert-warning alert-dismissible fade show" role="alert">
                                            <strong>File extension not supported! (must be .png, .jpg, or .jpeg)</strong>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    <?php
                                        echo "<meta http-equiv=\"refresh\" content=\"2;URL=edit.php?extensionfail\">";
                                    }
                                } else {
                                    ?>
                                    <div class=" container alert alert-warning alert-dismissible fade show" role="alert">
                                        <strong>File size must be at least 2kb</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=edit.php?filesize\">";
                                }
                            } 
                            elseif ($title != "" && $filename == ""){
                                // Update
                                $query = "UPDATE media SET title='$title', updated_at=NOW() WHERE id=$id";
                                $result = mysqli_query($con, $query);
                                if ($result) {
                        ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Title Updated</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\">";
                                } else {
                                ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Title Update failed</strong>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    <?php
                                    echo "<meta http-equiv=\"refresh\" content=\"2;URL=edit.php?failed\">";
                                }
                            }
                            else {
                                ?>
                                <div class=" container alert alert-warning alert-dismissible fade show" role="alert">
                                    <strong>No updates were made!</strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                        <?php
                                echo "<meta http-equiv=\"refresh\" content=\"2;URL=edit.php?empty\">";
                            }
                        }

                        ?>
                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST" enctype="multipart/form-data">
                            <div class="col-md-12">
                                <label for="inputName5" class="form-label fw-bold">Img Title</label>
                                <input type="text" class="form-control" name="title" id="inputName5" value="<?php echo $data['title']; ?>">
                            </div>
                            <div class="col-md-12">
                                <label for="inputEmail5" class="form-label fw-bold">Image</label>
                                <div class="mb-2">
                                    <img class="img-thumbnail" src="<?php echo BASE_URL . '/uploads/'?><?php echo $data['img_link']; ?>" alt="<?php echo $data['title']; ?>" width="250">
                                </div>
                                <input type="file" class="form-control" name="dataFile" id="inputEmail5">
                            </div>

                            <div class="col-md-12 pt-2">
                                <button type="submit" name="submit" class="btn btn-custom fw-bold">Update Image</button>
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