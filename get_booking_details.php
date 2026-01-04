<?php
session_start();

// เชื่อมต่อกับฐานข้อมูล
include_once('./function.php');
$objCon = connectDB();

// ตรวจสอบว่ามีการส่งค่า booking_id มาหรือไม่
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // ดึงรายละเอียดการจอง
    $sql = "SELECT b.booking_date, b.booker_name, b.phone_number, b.user_department, r.room_name, b.desired_date, b.start_time, b.end_time
            FROM booking b
            JOIN room r ON b.room_id = r.room_id
            WHERE b.booking_id = ?";
    $stmt = $objCon->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $booking = $result->fetch_assoc();
    $stmt->close();

    // ดึงรายละเอียดอุปกรณ์ที่ถูกจอง
    $sql = "SELECT e.equipment_name
            FROM booking_equipment be
            JOIN equipment e ON be.equipment_id = e.equipment_id
            WHERE be.booking_id = ?";
    $stmt = $objCon->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipment = [];
    while ($row = $result->fetch_assoc()) {
        $equipment[] = $row['equipment_name'];
    }
    $stmt->close();
} else {
    echo "No booking ID provided.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details</title>
    <link rel="stylesheet" href="./css/booking_details.css">
</head>
<body>
    <h2>รายละเอียดการจอง</h2>
    <p><strong>วันที่ทำรายการ:</strong> <?php echo $booking['booking_date']; ?></p>
    <p><strong>ชื่อผู้จอง:</strong> <?php echo $booking['booker_name']; ?></p>
    <p><strong>เบอร์โทร:</strong> <?php echo $booking['phone_number']; ?></p>
    <p><strong>แผนก:</strong> <?php echo $booking['user_department']; ?></p>
    <p><strong>ห้อง:</strong> <?php echo $booking['room_name']; ?></p>
    <p><strong>วันที่ต้องการจอง:</strong> <?php echo $booking['desired_date']; ?></p>
    <p><strong>เริ่มเวลา:</strong> <?php echo $booking['start_time']; ?></p>
    <p><strong>สิ้นสุดเวลา:</strong> <?php echo $booking['end_time']; ?></p>
    <h3>อุปกรณ์ที่จอง:</h3>
    <ul>
        <?php foreach ($equipment as $item) : ?>
            <li><?php echo $item; ?></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
