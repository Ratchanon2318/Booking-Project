<?php
include_once('./function.php');
$objCon = connectDB();

$data = $_POST;
$user_username = $data['user_username'];
$user_password = md5($data['user_password']); // เข้ารหัสด้วย md5
$user_department = $data['user_department'];
$user_status = $data['user_status'];

$strSQL = "INSERT INTO 
users(
    user_username,
    user_password,
    user_department,
    user_status
) VALUES (
    '$user_username', 
    '$user_password', 
    '$user_department',
    '$user_status'
)";

$objQuery = mysqli_query($objCon, $strSQL) or die(mysqli_error($objCon));
if ($objQuery) {
    echo '<script>alert("ลงทะเบียนเรียบร้อยแล้ว");window.location="login.php";</script>';
} else {
    echo '<script>alert("พบข้อผิดพลาด");window.location="register.php";</script>';
}
?>
