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

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_manage_users.php");
    exit();
}

$user_id = $_GET['id'];

// Delete user from database
$deleteSQL = "DELETE FROM users WHERE user_id = '$user_id'";
$objCon->query($deleteSQL);

// Redirect to manage users page
header("Location: admin_manage_users.php");
exit();
?>
