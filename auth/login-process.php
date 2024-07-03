<?php
session_start();
require("../config/config.php");

if (isset($_POST['submit'])) {
    $user_id = $_POST['id'];
    $username = $_POST['username'];
    $name = $_POST['name'];
    $user_role = isset($_POST['user_role']) ? $_POST['user_role'] : '';
    $password = $_POST['password'];

    if ($username != "" && $password != "" && $user_role != "") {
        $select = "SELECT * FROM users WHERE username = '$username' AND password = md5('$password') AND user_role = '$user_role'";
        $result = mysqli_query($con, $select);

        if ($result->num_rows > 0) {
            $data = mysqli_fetch_assoc($result);
            
            // Update last_login timestamp
            $update_last_login = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = '{$data['id']}'";
            mysqli_query($con, $update_last_login);

            // Store user data in session
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['name'] = $data['name'];
            $_SESSION['user_role'] = $data['user_role'];
            $_SESSION['email'] = $data['email'];
            $_SESSION['image'] = $data['image'];
            
            // Redirect based on user role
            if ($user_role == "Admin") {
                header("Location: ../admin/dashboard.php?success");
            } else if ($user_role == "Staff") {
                header("Location: ../staff/dashboard.php?success");
            } 
            exit();
        } else {
            header("Location: ../index.php?error");
            exit();
        }
    } else {
        header("Location: ../index.php?empty");
        exit();
    }
}
?>
