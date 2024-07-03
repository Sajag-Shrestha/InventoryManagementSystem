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
                            $customer_id = $_POST['customer_id'];
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

                                if ($status == 'Fulfilled' && $old_status == 'Pending' && $stock < $quantity || $status == 'Fulfilled' && $old_status == 'Fulfilled') {
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
                customer_id = '$customer_id', 
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

                                        // Fetch the price, name, and img_link of the selected product
                                        $select_product = "
                    SELECT p.id, p.name, p.price, p.stock, m.img_link AS media_img_link
                    FROM products p
                    LEFT JOIN media m ON p.img_link = m.id
                    WHERE p.id = '$product_id'
                ";
                                        $result_product = mysqli_query($con, $select_product);
                                        $product_data = mysqli_fetch_assoc($result_product);

                                        if ($product_data) {
                                            $price = $product_data['price'];
                                            $stock = $product_data['stock'];
                                            $product_name = $product_data['name'];
                                            $img_link = $product_data['media_img_link']; // Assuming this is the media link you want to update

                                            // Update order item details including product name and img_link
                                            $update_order_item = "
                        UPDATE order_items 
                        SET 
                            product_id = '$product_id',
                            quantity = '$quantity', 
                            price = '$price',
                            product_name = '$product_name', 
                            img_link = '$img_link'
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
                            <div class="col-md-12 d-flex align-items-center">
                                <span class="bg-icon">
                                    <i class="bi bi-person-fill"></i>
                                </span>
                                <select class="form-select fw-bold" name="customer_id" id="inputSelect5" required>
                                    <option value="" disabled>Select Customer</option>
                                    <?php
                                    $select_customers = "SELECT id, name FROM customers";
                                    $result_customers = mysqli_query($con, $select_customers);
                                    while ($customer = mysqli_fetch_assoc($result_customers)) {
                                        $selected = $customer['id'] == $order['customer_id'] ? 'selected' : '';
                                        echo "<option value='{$customer['id']}' {$selected}>{$customer['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_address" class="form-label fw-bold">Shipping Address:</label>
                                <textarea class="form-control" name="shipping_address" id="shipping_address" rows="2" required><?php echo $order['shipping_address']; ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="billing_address" class="form-label fw-bold">Billing Address:</label>
                                <textarea class="form-control" name="billing_address" id="billing_address" rows="2" required><?php echo $order['billing_address']; ?></textarea>
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

                            <!-- Order Items -->
                            <div class="col-12 mt-3">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover table-bordered text-center align-middle">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Product</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col">Price</th>
                                                <th scope="col">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($order_items as $key => $order_item) { ?>
                                                <tr>
                                                    <td><?php echo $key + 1; ?></td>
                                                    <td>
                                                        <select class="form-select" name="order_items[<?php echo $key; ?>][product_id]" required>
                                                            <?php foreach ($products as $product) { ?>
                                                                <?php $selected = $product['id'] == $order_item['product_id'] ? 'selected' : ''; ?>
                                                                <option value="<?php echo $product['id']; ?>" <?php echo $selected; ?>>
                                                                    <?php echo $product['name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" class="form-control" name="order_items[<?php echo $key; ?>][quantity]" value="<?php echo $order_item['quantity']; ?>" min="1" required>
                                                        <input type="hidden" name="order_items[<?php echo $key; ?>][order_item_id]" value="<?php echo $order_item['order_item_id']; ?>">
                                                    </td>
                                                    <td><?php echo number_format($order_item['price'], 2); ?></td>
                                                    <td><?php echo number_format($order_item['quantity'] * $order_item['price'], 2); ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- /Order Items -->

                            <div class="d-grid d-flex justify-content-between">
                                <a href="index.php" class="btn btn-custom">Cancel</a>
                                <button type="submit" name="submit" class="btn btn-custom">Update Order</button>
                            </div>
                        </form>
                        <!-- /Multi Columns Form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require(BASE_PATH . '/layouts/footer.php');
?>