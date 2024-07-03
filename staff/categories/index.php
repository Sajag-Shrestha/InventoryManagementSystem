<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar-staff.php');

// Handle category filtering
$category_filter = '';
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category_filter = $_GET['category'];
}

// SQL query to fetch total number of categories (for pagination)
$total_query = "SELECT COUNT(id) AS total FROM categories";
if (!empty($category_filter)) {
    $total_query .= " WHERE name LIKE '%$category_filter%'";
}

$total_result = mysqli_query($con, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total_categories = $total_data['total'];

// Pagination
$categories_per_page = 5; // Change this to adjust the number of categories per page
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $categories_per_page;

// SQL query to fetch categories with optional search filter and pagination
$select = "SELECT * FROM categories";
if (!empty($category_filter)) {
    $select .= " WHERE name LIKE '%$category_filter%'";
}
$select .= " ORDER BY id ASC LIMIT $categories_per_page OFFSET $offset";

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
                                <i class="material-symbols-outlined pt-2 pe-1">category</i>
                                Manage Categories
                            </strong>
                            <a href="create.php" class="btn btn-custom">Add Category</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <form class="d-flex mt-3" method="GET" action="index.php">
                                    <input class="form-control me-2" type="search" placeholder="Search by Category Name" aria-label="Search" name="category" value="<?php echo isset($_GET['category']) ? htmlspecialchars($_GET['category']) : ''; ?>">
                                    <button class="btn btn-outline-custom" type="submit">Search</button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Category Name</th>
                                            <th scope="col">Date Created</th>
                                            <th scope="col">Last Updated</th>
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
                                                <td><?php echo htmlspecialchars($data['name']); ?></td>
                                                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                                <td><?php echo htmlspecialchars($data['updated_at']); ?></td>
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
                            <?php if ($total_categories > $categories_per_page): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center custom-pagination">
                                        <?php
                                        $total_pages = ceil($total_categories / $categories_per_page);
                                        for ($page = 1; $page <= $total_pages; $page++) {
                                            $active = ($page == $current_page) ? 'active' : '';
                                            echo "<li class='page-item $active'><a class='page-link' href='?page=$page&category=$category_filter'>$page</a></li>";
                                        }
                                        ?>
                                    </ul>
                                    <div class="text-center">
                                        <span class="text-custom fw-bold">Total: <?php echo $total_categories; ?> Categories</span>
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
