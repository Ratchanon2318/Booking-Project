<?php
session_start();
include_once('./function.php');
include_once('./sendLineNotify.php'); // เพิ่มไฟล์ที่มีฟังก์ชัน sendLineNotify()

if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['booking_id']) && isset($_POST['approve'])) {
    $booking_id = $_POST['booking_id'];

    $objCon = connectDB();

    // Update booking status to 'Approved'
    $sql = "UPDATE booking SET booking_verify = 'Approved' WHERE booking_id = ?";
    $stmt = $objCon->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    if ($stmt->execute()) {
        // Get booking details
        $sql = "SELECT b.booking_id, b.booker_name, b.user_department, r.room_name, b.desired_date, b.start_time, b.end_time
                FROM booking b
                JOIN room r ON b.room_id = r.room_id
                WHERE b.booking_id = ?";
        $stmt = $objCon->prepare($sql);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();

        // Send LINE Notify
        $message = "การจองห้องประชุมที่ได้รับการอนุมัติ✅\nแผนก: " . $booking['user_department'] .
                   "\nชื่อผู้จอง: " . $booking['booker_name'] .
                   "\nห้อง: " . $booking['room_name'] .
                   "\nวันที่: " . $booking['desired_date'] .
                   "\nเวลา: " . $booking['start_time'] . " - " . $booking['end_time'];
        $token = 'j8ZvVWO1KVOhHWu7XtQDuEj2b78g0W5acCihRUeFFmy';
        sendLineNotify($message, $token);

        header("Location: booking_approval.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $objCon->close();
}
?>
