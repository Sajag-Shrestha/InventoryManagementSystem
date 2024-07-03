<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');

// Get product ID from URL parameter
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details
    $select_product = "
    SELECT 
        p.id,
        p.name,
        p.description,
        m.img_link AS media_img_link,
        p.stock,
        p.cost,
        p.price,
        c.name AS category_name
    FROM products p
    JOIN categories c ON p.category = c.id
    JOIN media m ON p.img_link = m.id
    WHERE p.id = '$product_id'
    ";
    $result_product = mysqli_query($con, $select_product);
    $product = mysqli_fetch_assoc($result_product);

    // Fetch order history for the product
    $select_orders = "
        SELECT 
            o.order_id,
            o.order_date,
            oi.quantity,
            oi.price
        FROM orders o
        JOIN order_items oi ON o.order_id = oi.order_id
        WHERE oi.product_id = '$product_id'
    ";
    $result_orders = mysqli_query($con, $select_orders);
}
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
                                Product Details
                            </strong>
                            <a href="index.php" class="btn btn-custom">Back to Products</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <h4 class="text-center">Product Information: </h4>
                                <hr>
                                <div class="col-md-6 pb-3">
                                <a href="<?php echo BASE_URL . '/uploads/'?><?php echo $product['media_img_link'] ?>" data-fancybox="images" data-caption="<?php echo $product['name']; ?>"><img class="img-thumbnail" src="<?php echo BASE_URL . '/uploads/'?><?php echo $product['media_img_link']; ?>" alt="<?php echo $product['name']; ?>"></a>
                                </div>
                                <div class="col-md-6 py-3">
                                    <p><strong>Product ID:</strong> <?php echo $product['id']; ?></p>
                                    <p><strong>Name:</strong> <?php echo $product['name']; ?></p>
                                    <p><strong>Description:</strong> <?php echo $product['description']; ?></p>
                                    <p><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
                                    <p><strong>Stock:</strong> <?php echo $product['stock']; ?></p>
                                    <p><strong>Cost:</strong> $<?php echo $product['cost']; ?></p>
                                    <p><strong>Price:</strong> $<?php echo $product['price']; ?></p>
                                </div>
                                <div class="col-md-12 pt-3 text-center">
                                    <button type="button" class="btn btn-custom w-50" data-bs-toggle="modal" data-bs-target="#orderHistoryModal">
                                        View Order History
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order History Modal -->
    <div class="modal fade" id="orderHistoryModal" tabindex="-1" aria-labelledby="orderHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderHistoryModalLabel">Order History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Order ID</th>
                                    <th scope="col">Order Date</th>
                                    <th scope="col">Quantity</th>
                                    <th scope="col">Price</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                while ($order = mysqli_fetch_assoc($result_orders)) {
                                ?>
                                    <tr>
                                        <th scope="row"><?php echo ++$i; ?></th>
                                        <td><?php echo $order['order_id']; ?></td>
                                        <td><?php echo $order['order_date']; ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td>$<?php echo $order['price']; ?></td>
                                        <td>$<?php echo $order['quantity' * 'price']; ?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-custom" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require(BASE_PATH . '/layouts/footer.php');
?>