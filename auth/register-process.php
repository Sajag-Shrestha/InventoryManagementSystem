<?php
require('../config/config.php');

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $user_role = isset($_POST['user_role']) ? $_POST['user_role'] : '';

    if ($name != "" && $email != "" && $username != "" && $user_role != "") {
        $select = "SELECT * FROM users WHERE email = '$email' OR username = '$username'";
        $result = mysqli_query($con, $select);

        if ($result->num_rows > 0) {
            echo "Username or Email already exists";
            header("Refresh:2; URL=../register.php");
        } else {

            // Setting the default image based on user role
            $default_image = '';
            if ($user_role == 'Admin') {
                $default_image = 'admin.png';
            } else if ($user_role == 'Staff') {
                $default_image = 'staff.png';
            }


            $insert = "INSERT INTO users (name, email, username, password, user_role, image) 
                VALUES ('$name', '$email', '$username', '$password', '$user_role', '$default_image')";
            $result = mysqli_query($con, $insert);

            if ($result) {
                header("Refresh:0; URL=../index.php?success");
            } else {
                header("Refresh:0; URL=../register.php?error");
            }
        }
    } else {
        header("Refresh:0; URL=../register.php?empty");
    }
}
