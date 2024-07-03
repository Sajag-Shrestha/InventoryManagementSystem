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

// Handle search filter
$search_filter = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_filter = $_GET['search'];
}

// SQL query to fetch total number of products (for pagination)
$total_query = "
    SELECT COUNT(p.id) AS total
    FROM products p
    JOIN media m ON p.img_link = m.id
    JOIN categories c ON p.category = c.id
";
if (!empty($category_filter)) {
    $total_query .= " WHERE p.category = '$category_filter'";
}
if (!empty($search_filter)) {
    if (!empty($category_filter)) {
        $total_query .= " AND ";
    } else {
        $total_query .= " WHERE ";
    }
    $total_query .= " p.name LIKE '%$search_filter%'";
}

$total_result = mysqli_query($con, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total_products = $total_data['total'];

// Pagination
$products_per_page = 5; // Change this to adjust the number of products per page
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $products_per_page;

// SQL query to fetch products with joins, optional category filter, search filter, and pagination
$select = "
    SELECT 
        p.id,
        p.name,
        p.stock,
        p.cost,
        p.price,
        p.created_at,
        m.img_link AS media_img_link,
        m.title AS media_title,
        c.name AS category_name
    FROM products p
    JOIN media m ON p.img_link = m.id
    JOIN categories c ON p.category = c.id
";
if (!empty($category_filter)) {
    $select .= " WHERE p.category = '$category_filter'";
}
if (!empty($search_filter)) {
    if (!empty($category_filter)) {
        $select .= " AND ";
    } else {
        $select .= " WHERE ";
    }
    $select .= " p.name LIKE '%$search_filter%'";
}

// Sorting by cost, price, stock
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'stock':
            $select .= " ORDER BY p.stock DESC";
            break;
        case 'cost':
            $select .= " ORDER BY p.cost DESC";
            break;
        case 'price':
            $select .= " ORDER BY p.price DESC";
            break;
        default:
            // Default order (latest products first)
            $select .= " ORDER BY p.id DESC";
            break;
    }
} else {
    // Default order (latest products first)
    $select .= " ORDER BY p.id DESC";
}

$select .= " LIMIT $products_per_page OFFSET $offset";

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
                                <i class="bi bi-box-seam-fill pe-1"></i>
                                Manage Products
                            </strong>
                            <a href="create.php" class="btn btn-custom">Add Product</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex justify-content-between">
                                <div class="dropdown">
                                    <button class="btn btn-custom2 dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item" href="<?php echo 'index.php'; ?>">All Products</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <?php
                                        $categories_query = "SELECT id, name FROM categories";
                                        $categories_result = mysqli_query($con, $categories_query);
                                        while ($category = mysqli_fetch_assoc($categories_result)) {
                                            $selected = ($category['id'] == $category_filter) ? 'active' : '';
                                            echo "<li><a class='dropdown-item $selected' href='?category={$category['id']}'>{$category['name']}</a></li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <form class="d-flex" method="GET" action="index.php">
                                    <input class="form-control me-2" type="search" placeholder="Search by Product Name" aria-label="Search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                    <button class="btn btn-outline-custom" type="submit">Search</button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Image</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">
                                                <a href="?sort=stock" class="text-decoration-none text-dark">Stock</a>
                                            </th>
                                            <th scope="col">
                                                <a href="?sort=cost" class="text-decoration-none text-dark">Cost</a>
                                            </th>
                                            <th scope="col">
                                                <a href="?sort=price" class="text-decoration-none text-dark">Price</a>
                                            </th>
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
                                                    <a href="<?php echo BASE_URL . '/uploads/' . $data['media_img_link'] ?>" data-fancybox="images" data-caption="<?php echo htmlspecialchars($data['name']); ?>">
                                                        <img class="img-thumbnail" src="<?php echo BASE_URL . '/uploads/' . $data['media_img_link']; ?>" alt="<?php echo htmlspecialchars($data['name']); ?>" width="125" height="125">
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($data['name']); ?></td>
                                                <td><?php echo htmlspecialchars($data['category_name']); ?></td>
                                                <td><?php echo htmlspecialchars($data['stock']); ?></td>
                                                <td><?php echo htmlspecialchars($data['cost']); ?></td>
                                                <td><?php echo htmlspecialchars($data['price']); ?></td>
                                                <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="view.php?id=<?php echo $data['id']; ?>" role="button"><i class="bi bi-eye-fill"></i></a>
                                                    <a class="btn btn-primary btn-sm" href="edit.php?id=<?php echo $data['id']; ?>" role="button"><i class="bi bi-pencil-square"></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($total_products > $products_per_page): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center custom-pagination">
                                        <?php
                                        $total_pages = ceil($total_products / $products_per_page);
                                        for ($page = 1; $page <= $total_pages; $page++) {
                                            $active = ($page == $current_page) ? 'active' : '';
                                            echo "<li class='page-item $active'><a class='page-link' href='?page=$page&category=$category_filter&search=$search_filter'>$page</a></li>";
                                        }
                                        ?>
                                    </ul>
                                    <div class="text-center">
                                        <span class="text-custom fw-bold">Total: <?php echo $total_products; ?> Products</span>
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