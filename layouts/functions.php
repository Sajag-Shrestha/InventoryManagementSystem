<?php
require(BASE_PATH . '/config/config.php');


function countIds($con, $table)
{
    $sql = "SELECT COUNT(id) AS total_ids FROM $table";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the result row
        $row = $result->fetch_assoc();
        return $row['total_ids'];
    } else {
        return 0;
    }
}

function countOrderIds($con, $table)
{
    $sql = "SELECT COUNT(order_id) AS total_ids FROM $table";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the result row
        $row = $result->fetch_assoc();
        return $row['total_ids'];
    } else {
        return 0;
    }
}

// Function to get a specific setting value from the database
function getSetting($name)
{
    global $con;

    // Query to fetch the setting value based on its name
    $query = "SELECT value FROM settings WHERE name = '$name'";
    $result = mysqli_query($con, $query);

    // Check if query was successful and return the value
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['value'];
    } else {
        return null; // Handle error or return default value if setting not found
    }
}

/*--------------------------------------------------------------*/
/* Function for Remove html characters
/*--------------------------------------------------------------*/
function remove_junk($str)
{
    $str = nl2br($str);
    $str = htmlspecialchars(strip_tags($str, ENT_QUOTES));
    return $str;
}

/*--------------------------------------------------------------*/
/* Function for Uppercase first character
  /*--------------------------------------------------------------*/
function first_character($str)
{
    $val = str_replace('-', " ", $str);
    $val = ucfirst($val);
    return $val;
}

?>