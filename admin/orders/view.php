<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');

// Get the tax rate from the settings table
$tax_rate = floatval(getSetting('tax_rate'));

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    // Fetch order details from the database
    $select_order = "
        SELECT 
            o.order_id,
            o.order_date,
            o.total_amount,
            o.status,
            o.shipping_address,
            o.billing_address,
            c.name AS customer_name,
            c.email AS customer_email,
            c.phone AS customer_phone
        FROM orders o
        JOIN customers c ON o.customer_id = c.id
        WHERE o.order_id = '$order_id'
    ";
    $result_order = mysqli_query($con, $select_order);
    $order = mysqli_fetch_assoc($result_order);

    // Fetch order items from the database
    $select_order_items = "
        SELECT 
            oi.order_item_id,
            oi.product_name,
            oi.img_link,
            oi.quantity,
            oi.price
        FROM order_items oi
        WHERE oi.order_id = '$order_id'
    ";
    $result_order_items = mysqli_query($con, $select_order_items);
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
                                View Order
                            </strong>
                            <a href="index.php" class="btn btn-custom">Back to Orders</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Order Details</h4>
                                    <hr>
                                    <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
                                    <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
                                    <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
                                    <p><strong>Total Amount (before Tax):</strong> <?php echo number_format($order['total_amount'] / (1 + $tax_rate), 2); ?></p>
                                    <p><strong>Tax Rate:</strong> <?php echo $tax_rate * 100; ?>%</p>
                                    <p><strong>Shipping Address:</strong> <?php echo $order['shipping_address']; ?></p>
                                    <p><strong>Billing Address:</strong> <?php echo $order['billing_address']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <h4>Customer Details</h4>
                                    <hr>
                                    <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                                    <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
                                    <p><strong>Phone:</strong> <?php echo $order['customer_phone']; ?></p>
                                </div>
                            </div>
                            <h4 class="py-2">Order Items</h4>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th class="view-th" scope="col">#</th>
                                            <th class="view-th" scope="col">Image</th>
                                            <th class="view-th" scope="col">Product Name</th>
                                            <th class="view-th" scope="col">Quantity</th>
                                            <th class="view-th" scope="col">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 0;
                                        while ($item = mysqli_fetch_assoc($result_order_items)) {
                                        ?>
                                            <tr>
                                                <th scope="row"><?php echo ++$i; ?></th>
                                                <td><a href="<?php echo BASE_URL . '/uploads/'?><?php echo $item['img_link'] ?>" data-fancybox="images" data-caption="<?php echo $item['product_name'];; ?>"><img class="img-thumbnail" src="<?php echo BASE_URL . '/uploads/'?><?php echo $item['img_link']; ?>" alt="<?php echo $item['product_name']; ?>" width="150" height="150"></a></td>
                                                <td><?php echo $item['product_name']; ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo $item['price']; ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <p><strong>Total Amount (with Tax):</strong> <?php echo $order['total_amount']; ?></p>
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
