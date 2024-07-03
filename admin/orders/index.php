<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');

// Pagination
$orders_per_page = 5; // Number of orders per page
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $orders_per_page;

// Handle customer name search
$customer_name_filter = '';
if (isset($_GET['customer_name']) && !empty($_GET['customer_name'])) {
    $customer_name_filter = $_GET['customer_name'];
}

// Handle status filter
$status_filter = '';
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status_filter = $_GET['status'];
}

// Handle sorting by total amount
$sort_by = '';
if (isset($_GET['sort']) && !empty($_GET['sort'])) {
    $sort_by = $_GET['sort'];
}

// Base query to fetch orders
$select_orders = "
    SELECT 
        o.order_id,
        o.order_date,
        o.total_amount,
        o.status,
        o.updated_at,
        c.name AS customer_name
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
";

// Adjust query based on filters
$where_conditions = [];
if (!empty($customer_name_filter)) {
    $where_conditions[] = "c.name LIKE '%$customer_name_filter%'";
}
if (!empty($status_filter)) {
    $where_conditions[] = "o.status = '$status_filter'";
}
if (!empty($where_conditions)) {
    $select_orders .= " WHERE " . implode(" AND ", $where_conditions);
}

// Add "Returned" to the status filter
$additional_filters = "
    <li><a class='dropdown-item " . ($status_filter == 'returned' ? 'active' : '') . "' href='?status=returned&customer_name=$customer_name_filter&sort=$sort_by'>" . ($status_filter == 'returned' ? '<i class="bi bi-check2"></i> ' : '') . "Returned</a></li>
";

// Sorting
$sort_order = '';
if ($sort_by == 'total_amount_desc') {
    $select_orders .= " ORDER BY o.total_amount DESC";
    $sort_order = 'total_amount_desc';
} else {
    $select_orders .= " ORDER BY o.total_amount ASC";
    $sort_order = 'total_amount_asc';
}

// Pagination query
$pagination_query = $select_orders . " LIMIT $orders_per_page OFFSET $offset";
$result_orders = mysqli_query($con, $pagination_query);

// Total number of orders for pagination
$total_query = "
    SELECT COUNT(o.order_id) AS total 
    FROM orders o
    JOIN customers c ON o.customer_id = c.id
";
if (!empty($where_conditions)) {
    $total_query .= " WHERE " . implode(" AND ", $where_conditions);
}
$total_result = mysqli_query($con, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total_orders = $total_data['total'];
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
                                Manage Orders
                            </strong>
                            <a href="create.php" class="btn btn-custom">Create Order</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex justify-content-between">
                                <div class="dropdown">
                                    <button class="btn btn-custom2 dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item" href="<?php echo 'index.php'; ?>">All Orders</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item <?php echo empty($status_filter) && empty($customer_name_filter) ? 'active' : ''; ?>" href="?">All Orders</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><span class="dropdown-item-text">Filter by Status:</span></li>
                                        <li><a class="dropdown-item <?php echo $status_filter == 'Pending' ? 'active' : ''; ?>" href="?status=Pending&customer_name=<?php echo $customer_name_filter; ?>&sort=<?php echo $sort_by; ?>"><?php echo $status_filter == 'Pending' ? '<i class="bi bi-check2"></i> ' : ''; ?>Pending</a></li>
                                        <li><a class="dropdown-item <?php echo $status_filter == 'Fulfilled' ? 'active' : ''; ?>" href="?status=Fulfilled&customer_name=<?php echo $customer_name_filter; ?>&sort=<?php echo $sort_by; ?>"><?php echo $status_filter == 'Fulfilled' ? '<i class="bi bi-check2"></i> ' : ''; ?>Fulfilled</a></li>
                                        <li><a class="dropdown-item <?php echo $status_filter == 'Cancelled' ? 'active' : ''; ?>" href="?status=Cancelled&customer_name=<?php echo $customer_name_filter; ?>&sort=<?php echo $sort_by; ?>"><?php echo $status_filter == 'Cancelled' ? '<i class="bi bi-check2"></i> ' : ''; ?>Cancelled</a></li>
                                        <?php echo $additional_filters; ?>
                                    </ul>
                                </div>
                                <form class="d-flex" method="GET" action="index.php">
                                    <input class="form-control me-3" type="search" placeholder="Search by Customer" aria-label="Search" name="customer_name" value="<?php echo isset($_GET['customer_name']) ? htmlspecialchars($_GET['customer_name']) : ''; ?>">
                                    <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                                    <input type="hidden" name="sort" value="<?php echo $sort_by; ?>">
                                    <button class="btn btn-outline-custom" type="submit">Search</button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Order Date</th>
                                            <th scope="col">Customer</th>
                                            <th scope="col">
                                                <a href="?sort=<?php echo ($sort_by == 'total_amount_asc') ? 'total_amount_desc' : 'total_amount_asc'; ?>&customer_name=<?php echo $customer_name_filter; ?>&status=<?php echo $status_filter; ?>" class="text-decoration-none text-dark">
                                                    Total Amount
                                                    <?php if ($sort_by == 'total_amount_asc') { ?>
                                                        <i class="bi bi-sort-down text-dark"></i>
                                                    <?php } elseif ($sort_by == 'total_amount_desc') { ?>
                                                        <i class="bi bi-sort-up text-dark"></i>
                                                    <?php } ?>
                                                </a>
                                            </th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Last Updated</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = $offset + 1;
                                        while ($order = mysqli_fetch_assoc($result_orders)) {
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo $i++; ?></th>
                                                <td><?php echo $order['order_date']; ?></td>
                                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                                <td><?php echo $order['total_amount']; ?></td>
                                                <td><?php echo $order['status']; ?></td>
                                                <td><?php echo $order['updated_at']; ?></td>
                                                <td>
                                                    <a class="btn btn-success btn-sm" href="view.php?id=<?php echo $order['order_id']; ?>" role="button"><i class="bi bi-eye-fill"></i></a>
                                                    <a class="btn btn-primary btn-sm" href="edit.php?id=<?php echo $order['order_id']; ?>" role="button"><i class="bi bi-pencil-square"></i></a>
                                                    <a class="btn btn-danger btn-sm" onclick="return confirm('Do you want to delete this order?');" href="delete.php?id=<?php echo $order['order_id']; ?>" role="button"><i class="bi bi-trash3-fill"></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <?php
                                    $total_pages = ceil($total_orders / $orders_per_page);
                                    for ($page = 1; $page <= $total_pages; $page++) {
                                        echo '<li class="page-item ' . ($page == $current_page ? 'active' : '') . '"><a class="page-link" href="?page=' . $page . '&customer_name=' . $customer_name_filter . '&status=' . $status_filter . '&sort=' . $sort_by . '">' . $page . '</a></li>';
                                    }
                                    ?>
                                </ul>
                            </nav>
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
