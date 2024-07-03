
<?php

require_once(__DIR__ . '/../../configuration.php');
include(BASE_PATH . "/config/config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $data = "DELETE FROM orders where order_id='$id'";
    $data_result = mysqli_query($con, $data);

    echo "<meta http-equiv=\"refresh\" content=\"1;URL=index.php?delete\">";
}

?>