<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar-staff.php');

// Handle search filter
$search_filter = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_filter = $_GET['search'];
}

// Handle image type filter
$image_type_filter = '';
if (isset($_GET['image_type']) && !empty($_GET['image_type'])) {
    $image_type_filter = $_GET['image_type'];
}

// SQL query to fetch total number of images (for pagination)
$total_query = "SELECT COUNT(id) AS total FROM media";
if (!empty($image_type_filter)) {
    $total_query .= " WHERE type = '$image_type_filter'";
}
if (!empty($search_filter)) {
    if (!empty($image_type_filter)) {
        $total_query .= " AND ";
    } else {
        $total_query .= " WHERE ";
    }
    $total_query .= " title LIKE '%$search_filter%'";
}

$total_result = mysqli_query($con, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total_images = $total_data['total'];

// Pagination
$images_per_page = 5; // Adjust this to change the number of images per page
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $images_per_page;

// SQL query to fetch images with optional filters and pagination
$select = "SELECT * FROM media";
if (!empty($image_type_filter)) {
    $select .= " WHERE type = '$image_type_filter'";
}
if (!empty($search_filter)) {
    if (!empty($image_type_filter)) {
        $select .= " AND ";
    } else {
        $select .= " WHERE ";
    }
    $select .= " title LIKE '%$search_filter%'";
}

$select .= " LIMIT $images_per_page OFFSET $offset";

$result = mysqli_query($con, $select);
?>

<main id="main" class="main">
    <div class="page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center text-center">
                            <strong class="mx-auto">
                                <i class="bi bi-image pe-1"></i>
                                Manage Images
                            </strong>
                            <a href="create.php" class="btn btn-custom">Add Image</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex justify-content-between">
                                <div class="dropdown">
                                    <button class="btn btn-custom2 dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item" href="<?php echo 'index.php'; ?>">All Images</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item <?php echo empty($image_type_filter) ? 'active' : ''; ?>" href="?image_type=">All Types</a></li>
                                        <li><a class="dropdown-item <?php echo ($image_type_filter == 'png') ? 'active' : ''; ?>" href="?image_type=png">PNG</a></li>
                                        <li><a class="dropdown-item <?php echo ($image_type_filter == 'jpg') ? 'active' : ''; ?>" href="?image_type=jpg">JPG</a></li>
                                        <li><a class="dropdown-item <?php echo ($image_type_filter == 'jpeg') ? 'active' : ''; ?>" href="?image_type=jpeg">JPEG</a></li>
                                    </ul>
                                </div>
                                <form class="d-flex" method="GET" action="index.php">
                                    <input class="form-control me-2" type="search" placeholder="Search by Image Name" aria-label="Search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button class="btn btn-outline-custom" type="submit">Search</button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Image Name</th>
                                            <th scope="col">Image Type</th>
                                            <th scope="col">Date Added</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = $offset + 1;
                                        while ($data = mysqli_fetch_array($result)) {
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i++; ?></th>
                                                <td>
                                                    <a href="<?php echo BASE_URL . '/uploads/' . $data['img_link'] ?>" data-fancybox="images" data-caption="<?php echo htmlspecialchars($data['title']); ?>">
                                                        <img class="img-thumbnail" src="<?php echo BASE_URL . '/uploads/' . $data['img_link']; ?>" alt="<?php echo htmlspecialchars($data['title']); ?>" width="125" height="125">
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($data['title']); ?></td>
                                                <td><?php echo htmlspecialchars($data['type']); ?></td>
                                                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                                <td>
                                                    <a class="btn btn-primary btn-sm" href="edit.php?id=<?php echo $data['id']; ?>" role="button"><i class="bi bi-pencil-square"> Edit</i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($total_images > $images_per_page): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center custom-pagination">
                                        <?php
                                        $total_pages = ceil($total_images / $images_per_page);
                                        for ($page = 1; $page <= $total_pages; $page++) {
                                            $active = ($page == $current_page) ? 'active' : '';
                                            echo "<li class='page-item $active'><a class='page-link' href='?page=$page&search=$search_filter&image_type=$image_type_filter'>$page</a></li>";
                                        }
                                        ?>
                                    </ul>
                                    <div class="text-center">
                                        <span class="text-custom fw-bold">Total: <?php echo $total_images; ?> Images</span>
                                    </div>
                                </nav>
                            <?php endif; ?>
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
