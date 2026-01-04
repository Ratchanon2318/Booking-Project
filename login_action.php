<?php
session_start();
include_once('./function.php');

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $user_username = $_POST['user_username'];
    $user_password = $_POST['user_password'];

    $strSQL = "SELECT * FROM users WHERE user_username = '$user_username' AND user_password = '$user_password'";
    $result = $objCon->query($strSQL);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_username'] = $user_username;
        $_SESSION['user_status'] = $row['user_status'];

        if ($row['user_status'] == "Admin") {
            header("Location: admin_index.php");
        } else {
            header("Location: user_index.php");
        }
    } else {
        $error = "Invalid username or password";
    }
}

?>