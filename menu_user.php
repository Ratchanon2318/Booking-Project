<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();


if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'User') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">
    <link rel="stylesheet" href="./css/menu.css">
    
    <title>Booking_room</title>
</head>
<body>
    <ul class="logo">
            <li class="logo"><a href="user_index.php"><img src="./room_images/menu.png" alt="ระบบจองห้องประชุม"/></a></li>
        </ul>
    <nav>

        <div id="menu">
        <ul class="menu">
            <li class="item"><a href="user_index.php">หน้าหลัก</a></li>
            <li class="item"><a href="#">จัดการข้อมูลผู้ใช้งาน</a>
                <ul class="submenu">
                    <li><a href="user_manage.php">ข้อมูลผู้ใช้งาน</a></li>
                </ul>
            </li>
            <li class="item"><a href="#">ตรวจสอบสถานะห้องประชุม</a>
                <ul class="submenu">
                    <li><a href="Reservation_calendar_user.php">ปฏิทินการจอง</a></li>
                    <li><a href="Meeting_room_user.php">รายระเอียดห้องประชุม</a></li>
                </ul>
            </li>
            <li class="item"><a href="Booking_meeting_room_user.php">จองห้องประชุม</a></li>
            <li class="item"><a href="report_user.php">รายการจองของฉัน</a></li>
            <li class="item button"><a href="logout.php">ออกจากระบบ</a></li>
            <li class="toggle"><a href="#"><i class="fas fa-bars"></i></a></li>
        </ul>
        </div>
    </nav>
</body>
</html>

