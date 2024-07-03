<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');

// Handle username search
$username_filter = '';
if (isset($_GET['username']) && !empty($_GET['username'])) {
    $username_filter = $_GET['username'];
}

// Handle user role filter
$user_role_filter = '';
if (isset($_GET['user_role']) && !empty($_GET['user_role'])) {
    $user_role_filter = $_GET['user_role'];
}

// SQL query to fetch total number of users (for pagination)
$total_query = "SELECT COUNT(id) AS total FROM users";
if (!empty($username_filter) || !empty($user_role_filter)) {
    $total_query .= " WHERE ";
    $conditions = [];
    if (!empty($username_filter)) {
        $conditions[] = "username LIKE '%$username_filter%'";
    }
    if (!empty($user_role_filter)) {
        $conditions[] = "user_role = '$user_role_filter'";
    }
    $total_query .= implode(" AND ", $conditions);
}

$total_result = mysqli_query($con, $total_query);
$total_data = mysqli_fetch_assoc($total_result);
$total_users = $total_data['total'];

// Pagination
$users_per_page = 5; // Change this to adjust the number of users per page
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $users_per_page;

// SQL query to fetch users with optional username search, user role filter, and pagination
$select = "SELECT * FROM users";
if (!empty($username_filter) || !empty($user_role_filter)) {
    $select .= " WHERE ";
    $conditions = [];
    if (!empty($username_filter)) {
        $conditions[] = "username LIKE '%$username_filter%'";
    }
    if (!empty($user_role_filter)) {
        $conditions[] = "user_role = '$user_role_filter'";
    }
    $select .= implode(" AND ", $conditions);
}
$select .= " ORDER BY id ASC LIMIT $users_per_page OFFSET $offset";

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
                                <i class="bi bi-person-fill-gear pe-1"></i>
                                Manage Users
                            </strong>
                            <a href="create.php" class="btn btn-custom">Add User</a>
                        </div>
                        <div class="card-body">
                            <div class="mb-3 d-flex justify-content-between">
                                <div class="dropdown">
                                    <button class="btn btn-custom2 dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                        <li><a class="dropdown-item" href="<?php echo 'index.php'; ?>">All Users</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item <?php echo empty($user_role_filter) ? 'active' : ''; ?>" href="?user_role=">All Roles</a></li>
                                        <li><a class="dropdown-item <?php echo ($user_role_filter == 'admin') ? 'active' : ''; ?>" href="?user_role=admin">Admin</a></li>
                                        <li><a class="dropdown-item <?php echo ($user_role_filter == 'staff') ? 'active' : ''; ?>" href="?user_role=staff">Staff</a></li>
                                    </ul>
                                </div>
                                <form class="d-flex" method="GET" action="index.php">
                                    <input class="form-control me-2" type="search" placeholder="Search by Username" aria-label="Search" name="username" value="<?php echo isset($_GET['username']) ? htmlspecialchars($_GET['username']) : ''; ?>">
                                    <button class="btn btn-outline-custom" type="submit">Search</button>
                                </form>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">User Role</th>
                                            <th scope="col">Last Login</th>
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
                                                <td><?php echo htmlspecialchars($data['username']); ?></td>
                                                <td><?php echo htmlspecialchars($data['email']); ?></td>
                                                <td><?php echo htmlspecialchars($data['user_role']); ?></td>
                                                <td><?php echo htmlspecialchars($data['last_login']); ?></td>
                                                <td>
                                                    <a class="btn btn-primary btn-sm" href="edit.php?id=<?php echo $data['id']; ?>" role="button"><i class="bi bi-pencil-square"></i></a>
                                                    <a class="btn btn-danger btn-sm" onclick="return confirm('Do you want to delete this user?');" href="delete.php?id=<?php echo $data['id']; ?>" role="button"><i class="bi bi-trash3-fill"></i></a>
                                                </td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($total_users > $users_per_page): ?>
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center custom-pagination">
                                        <?php
                                        $total_pages = ceil($total_users / $users_per_page);
                                        for ($page = 1; $page <= $total_pages; $page++) {
                                            $active = ($page == $current_page) ? 'active' : '';
                                            echo "<li class='page-item $active'><a class='page-link' href='?page=$page&username=$username_filter&user_role=$user_role_filter'>$page</a></li>";
                                        }
                                        ?>
                                    </ul>
                                    <div class="text-center">
                                        <span class="text-custom fw-bold">Total: <?php echo $total_users; ?> Users</span>
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
