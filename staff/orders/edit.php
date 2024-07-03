<?php
require_once(__DIR__ . '/../../configuration.php');
require(BASE_PATH . '/layouts/header.php');
require(BASE_PATH . '/layouts/navbar.php');
require(BASE_PATH . '/layouts/sidebar.php');


if (isset($_GET['id'])) {
    $order_id = $_GET['id'];

    // Fetch order details
    $select_order = "
        SELECT 
            o.order_id, o.customer_id, o.order_date, o.status, o.total_amount,
            o.shipping_address, o.billing_address,
            c.name AS customer_name
        FROM orders o
        LEFT JOIN customers c ON o.customer_id = c.id
        WHERE o.order_id='$order_id'
    ";
    $result_order = mysqli_query($con, $select_order);

    if (mysqli_num_rows($result_order) > 0) {
        $order = mysqli_fetch_assoc($result_order);
        $old_status = $order['status']; // Store current status in $old_status

        // Fetch order items
        $select_order_items = "
            SELECT 
                oi.order_item_id, oi.product_id, oi.quantity, oi.price,
                p.name AS product_name, m.img_link AS media_img_link
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN media m ON p.img_link = m.id
            WHERE oi.order_id='$order_id'
        ";
        $result_order_items = mysqli_query($con, $select_order_items);
        $order_items = mysqli_fetch_all($result_order_items, MYSQLI_ASSOC);
    } else {
        echo "Order not found.";
        exit; // Handle accordingly
    }
} else {
    echo "Order ID not provided.";
    exit; // Handle accordingly
}

// Fetch products for dropdown
$select_products = "
SELECT p.id, p.name, p.price, p.stock, m.img_link AS media_img_link 
FROM products p
LEFT JOIN media m ON p.img_link = m.id
";
$result_products = mysqli_query($con, $select_products);
$products = mysqli_fetch_all($result_products, MYSQLI_ASSOC);

?>

<?php
// Fetch the customer's name for the current order
$select_customer = "
    SELECT name 
    FROM customers 
    WHERE id = '{$order['customer_id']}'
";
$result_customer = mysqli_query($con, $select_customer);
$customer = mysqli_fetch_assoc($result_customer);
?>

<main id="main" class="main">
    <div class="page">
        <div class="container-fluid">
            <div class="col-12 col-md-6 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header text-center">
                        <strong>
                            <i class="material-symbols-outlined pe-1">box_edit</i>
                            Edit Order #<?php echo $order['order_id']; ?>
                        </strong>
                    </div>
                    <div class="card-body">

                        <?php if (isset($_GET['success'])) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Order updated successfully!</strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php echo "<meta http-equiv=\"refresh\" content=\"2;URL=index.php\">"; ?>
                        <?php } ?>

                        <?php
                        // Handle form submission
                        if (isset($_POST['submit'])) {
                            $shipping_address = $_POST['shipping_address'];
                            $billing_address = $_POST['billing_address'];
                            $status = $_POST['status'];
                            $order_items_post = $_POST['order_items'];

                            $total_amount = 0.00;

                            // Check stock before updating order status
                            $insufficient_stock = false;
                            $insufficient_product_names = []; // To store names of products with insufficient stock

                            foreach ($order_items_post as $order_item_post) {
                                $product_id = $order_item_post['product_id'];
                                $quantity = $order_item_post['quantity'];

                                // Get current stock for the product
                                $select_stock = "SELECT name, stock FROM products WHERE id = '$product_id'";
                                $result_stock = mysqli_query($con, $select_stock);
                                $product = mysqli_fetch_assoc($result_stock);
                                $stock = $product['stock'];

                                if ($status == 'Fulfilled' && $old_status == 'Pending' || $status == 'Fulfilled' && $old_status == 'Fulfilled' && $stock < $quantity) {
                                    $insufficient_stock = true;
                                    $insufficient_product_names[] = $product['name']; // Add the product name to the array
                                }
                            }

                            if ($insufficient_stock) {
                                $insufficient_product_list = implode(', ', $insufficient_product_names);
                                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <strong>Insufficient stock for products: {$insufficient_product_list}.</strong>
                                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                                </div>";
                                echo "<meta http-equiv=\"refresh\" content=\"2;URL=edit.php?id={$order_id}&lowstock\">";
                            } else {
                                // Update order in orders table
                                $update_order = "
            UPDATE orders 
            SET 
                shipping_address = '$shipping_address', 
                billing_address = '$billing_address',
                status = '$status'
            WHERE order_id = '$order_id'
        ";
                                $result_update_order = mysqli_query($con, $update_order);

                                if ($result_update_order) {
                                    // Update order items
                                    foreach ($order_items_post as $order_item_post) {
                                        $order_item_id = $order_item_post['order_item_id'];
                                        $product_id = $order_item_post['product_id'];
                                        $quantity = $order_item_post['quantity'];

                                        // Fetch the price of the selected product
                                        $select_product = "
                    SELECT p.id, p.price, p.stock, m.img_link AS media_img_link
                    FROM products p
                    LEFT JOIN media m ON p.img_link = m.id
                    WHERE p.id = '$product_id'
                ";
                                        $result_product = mysqli_query($con, $select_product);
                                        $product_data = mysqli_fetch_assoc($result_product);

                                        if ($product_data) {
                                            $price = $product_data['price'];
                                            $stock = $product_data['stock'];

                                            // Update order item details including img_link
                                            $update_order_item = "
                        UPDATE order_items 
                        SET 
                            quantity = '$quantity', 
                            price = '$price'
                        WHERE order_item_id = '$order_item_id'
                    ";
                                            mysqli_query($con, $update_order_item);

                                            // Calculate subtotal
                                            $subtotal = floatval($quantity) * floatval($price);
                                            $total_amount += $subtotal;

                                            // Update product stock based on order status change
                                            if ($status == 'Fulfilled' && $old_status == 'Pending') {
                                                // Decrease product stock
                                                $update_product_stock = "
                            UPDATE products 
                            SET stock = stock - $quantity
                            WHERE id = '$product_id'
                        ";
                                                mysqli_query($con, $update_product_stock);
                                            } elseif ($status == 'Returned' && $old_status == 'Fulfilled') {
                                                // Increase product stock
                                                $update_product_stock = "
                            UPDATE products 
                            SET stock = stock + $quantity
                            WHERE id = '$product_id'
                        ";
                                                mysqli_query($con, $update_product_stock);
                                            }
                                        }
                                    }

                                    // Apply tax rate to total_amount
                                    $tax_rate = floatval(getSetting('tax_rate'));
                                    $total_amount_with_tax = $total_amount * (1 + $tax_rate);

                                    // Update total_amount in orders table
                                    $update_order_amount = "
                UPDATE orders 
                SET total_amount = '$total_amount_with_tax' 
                WHERE order_id = '$order_id'
            ";
                                    mysqli_query($con, $update_order_amount);

                                    echo "<meta http-equiv=\"refresh\" content=\"0;URL=edit.php?id={$order_id}&success\">";
                                } else {
                                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <strong>Failed to update order.</strong>
                    <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>";
                                }
                            }
                        }
                        
                        ?>

                        <!-- Multi Columns Form -->
                        <form class="row g-3" method="POST">
                            <div class="col-md-12">
                                <label for="customer" class="form-label fw-bold">Customer:</label>
                                <input type="text" class="form-control" name="customer" id="customer" value="<?php echo $customer['name']; ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_address" class="form-label fw-bold">Shipping Address:</label>
                                <textarea class="form-control" name="shipping_address" id="shipping_address" rows="2"><?php echo $order['shipping_address']; ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="billing_address" class="form-label fw-bold">Billing Address:</label>
                                <textarea class="form-control" name="billing_address" id="billing_address" rows="2"><?php echo $order['billing_address']; ?></textarea>
                            </div>
                            <div class="col-md-12">
                                <label for="status" class="form-label fw-bold">Status:</label>
                                <select class="form-select fw-bold" name="status" id="status" required>
                                    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    <option value="Fulfilled" <?php echo $order['status'] == 'Fulfilled' ? 'selected' : ''; ?>>Fulfilled</option>
                                    <option value="Returned" <?php echo $order['status'] == 'Returned' ? 'selected' : ''; ?>>Returned</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <h5 class="card-title text-center mb-3">Order Items</h5>
                                <table class="table table-bordered table-hover table-condensed">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Product</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Price</th>
                                            <th class="text-center">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="order-items">
                                        <?php foreach ($order_items as $index => $item) : ?>
                                            <tr>
                                                <td class="text-center"><?php echo $index + 1; ?></td>
                                                <td class="text-center">
                                                    <?php echo $item['product_name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <input type="number" class="form-control" name="order_items[<?php echo $index; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
                                                    <input type="hidden" name="order_items[<?php echo $index; ?>][order_item_id]" value="<?php echo $item['order_item_id']; ?>">
                                                    <input type="hidden" name="order_items[<?php echo $index; ?>][product_id]" value="<?php echo $item['product_id']; ?>">
                                                </td>
                                                <td class="text-center"><?php echo $item['price']; ?></td>
                                                <td class="text-center"><?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-grid d-flex justify-content-between">
                                <a href="index.php" class="btn btn-custom">Cancel</a>
                                <button type="submit" name="submit" class="btn btn-custom">Update Order</button>
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
