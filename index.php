<?php
session_start(); // เริ่ม session
?>
<?php
if(isset($_SESSION['user_username']) && isset($_SESSION['user_status'])) { // ตรวจสอบว่ามี session username และ role หรือไม่
    $user_username = $_SESSION['user_username']; // ดึงชื่อผู้ใช้จาก session
    $user_status = $_SESSION['user_status']; // ดึงสิทธิ์ของผู้ใช้จาก session

    // ตรวจสอบสิทธิ์ของผู้ใช้งานและแสดงหน้า index ตามสิทธิ์
    if ($user_status == 'Admin') {
        include ('admin_index.php'); // เรียกใช้งานไฟล์ admin_index.php สำหรับหน้า index ของ admin
    } else {
        include ('user_index.php'); // เรียกใช้งานไฟล์ user_index.php สำหรับหน้า index ของ user
    }
} else {
    header("Location: login.php"); // ถ้าไม่มี session username หรือ role ให้ redirect ไปที่หน้า login.php
    exit();
}

?>

