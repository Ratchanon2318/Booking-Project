<?php
session_start();
// Include database connection file
include_once('./function.php');
$objCon = connectDB();

$data = $_POST;
$user_id = $data['user_id'];
$user_username = $data['user_username'];
$user_password = md5($data['user_password']); // เข้ารหัสด้วย md5
$user_firstname = $data['user_firstname'];
$user_lastname = $data['user_lastname'];
$user_tel = $data['user_tel'];

$strSQL = "UPDATE users SET 
    user_username = '$user_username', 
    user_password = '$user_password', 
    user_firstname = '$user_firstname', 
    user_lastname = '$user_lastname', 
    user_tel = '$user_tel' 
    WHERE user_id = $user_id";

$objQuery = mysqli_query($objCon, $strSQL) or die(mysqli_error($objCon));
if ($objQuery) {
    echo '<script>alert("อัปเดตข้อมูลเรียบร้อยแล้ว");window.location="admin_manage.php";</script>';
} else {
    echo '<script>alert("พบข้อผิดพลาดในการอัปเดตข้อมูล");window.location="admin_manage.php";</script>';
}
?>