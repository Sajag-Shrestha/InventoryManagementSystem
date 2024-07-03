<?php
require_once(__DIR__ . '/../../configuration.php');
include(BASE_PATH . "/config/config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query1 = "SELECT * from media where id = $id";
    $result = mysqli_query($con, $query1);
    $row = $result->fetch_assoc();
    $filelink = $row['img_link'];
    $finallink = BASE_PATH . '/uploads/' . $filelink;
    unlink($finallink);
    $query = "DELETE from media where id = $id";
    $result = mysqli_query($con, $query);
    if ($result) {
        header('Refresh: 0; url=index.php');
    } else {
        echo "your data is not deleted";
    }
}