<?php
session_start();

// Include database connection file
include_once('./function.php');

$objCon = connectDB(); // เชื่อมต่อฐานข้อมูล

// Check if user is logged in as admin
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_username = $_POST['user_username'];
    $user_password = $_POST['user_password'];
    $user_department = $_POST['user_department'];
    $user_status = $_POST['user_status'];

    // Encrypt password using md5 (Note: md5 is not secure and should not be used in production)
    $encrypted_password = md5($user_password);

    // Insert new user into the database
    $sql = "INSERT INTO users (user_username, user_password, user_department, user_status) VALUES ('$user_username', '$encrypted_password', '$user_department', '$user_status')";
    if ($objCon->query($sql) === TRUE) {
        header("Location: admin_manage_users.php");
        exit();
    } else {
        echo "Error adding user: " . $objCon->error;
    }
}

$objCon->close();
?>
