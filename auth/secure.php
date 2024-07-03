<?php
require_once(__DIR__ . '/../configuration.php');

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: " . BASE_URL . "/index.php");
}
?>