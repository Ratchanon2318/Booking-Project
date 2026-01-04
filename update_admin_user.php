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

// Get data from POST request
$data = $_POST;
$user_id = mysqli_real_escape_string($objCon, $data['user_id']);
$user_username = mysqli_real_escape_string($objCon, $data['user_username']);
$user_password = md5($data['user_password']); // เข้ารหัสด้วย md5
$user_department = mysqli_real_escape_string($objCon, $_POST['user_department']);
$user_status = mysqli_real_escape_string($objCon, $_POST['user_status']);

// Update user data
$strSQL = "UPDATE users SET 
    user_username = '$user_username', 
    user_password = '$user_password', 
    user_department = '$user_department',
    user_status = '$user_status'
    WHERE user_id = '$user_id'";

$objQuery = mysqli_query($objCon, $strSQL) or die(mysqli_error($objCon));
header("Location: admin_manage_users.php");
exit();
?>
