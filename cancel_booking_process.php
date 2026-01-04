<?php
session_start();
include_once('./function.php');
include_once('./sendLineNotify.php'); // เพิ่มไฟล์ที่มีฟังก์ชัน sendLineNotify()
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    if ($booking_id > 0) {
        $objCon = connectDB();
        
        // Get the booking data
        $sql_get_booking = "SELECT * FROM booking WHERE booking_id = ?";
        $stmt_get_booking = $objCon->prepare($sql_get_booking);
        $stmt_get_booking->bind_param("i", $booking_id);
        $stmt_get_booking->execute();
        $result_get_booking = $stmt_get_booking->get_result();
        $booking_data = $result_get_booking->fetch_assoc();
        
        if ($booking_data) {
            // Insert into cancelled_booking
            $sql_insert_cancelled = "INSERT INTO cancelled_booking 
                (booking_date, user_id, user_department, room_id, desired_date, start_time, end_time, cancelled_by, cancelled_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt_insert_cancelled = $objCon->prepare($sql_insert_cancelled);
            $stmt_insert_cancelled->bind_param(
                "sisissss", 
                $booking_data['booking_date'], 
                $booking_data['user_id'], 
                $booking_data['user_department'], 
                $booking_data['room_id'], 
                $booking_data['desired_date'], 
                $booking_data['start_time'], 
                $booking_data['end_time'], 
                $_SESSION['user_id']
            );
            
            $stmt_insert_cancelled->execute();
            
            // Delete from booking
            $sql_delete_booking = "DELETE FROM booking WHERE booking_id = ?";
            $stmt_delete_booking = $objCon->prepare($sql_delete_booking);
            $stmt_delete_booking->bind_param("i", $booking_id);
            $stmt_delete_booking->execute();
            
            if ($booking_data) {
                // Get room name
                $sql_get_room = "SELECT room_name FROM room WHERE room_id = ?";
                $stmt_get_room = $objCon->prepare($sql_get_room);
                $stmt_get_room->bind_param("i", $booking_data['room_id']);
                $stmt_get_room->execute();
                $result_get_room = $stmt_get_room->get_result();
                $room_data = $result_get_room->fetch_assoc();
                $room_name = $room_data['room_name'];
        
                // Send LINE Notify
                $message = "การจองห้องประชุมที่ถูกยกเลิก❌\nแผนก: " . $booking_data['user_department'] .
                           "\nชื่อผู้จอง: " . $booking_data['booker_name'] .
                           "\nห้อง: " . $room_name .
                           "\nวันที่: " . $booking_data['desired_date'] .
                           "\nเวลา: " . $booking_data['start_time'] . " - " . $booking_data['end_time'];
                $token = 'j8ZvVWO1KVOhHWu7XtQDuEj2b78g0W5acCihRUeFFmy';
                sendLineNotify($message, $token);
            }

            if ($stmt_insert_cancelled->affected_rows > 0 && $stmt_delete_booking->affected_rows > 0) {
                // Success
                header("Location: Booking_approval.php?message=cancel_success");
                exit();
            } else {
                // Error
                header("Location: Booking_approval.php?message=cancel_error");
                exit();
            }
        }
        
        $stmt_get_booking->close();
        $stmt_insert_cancelled->close();
        $stmt_delete_booking->close();
        $objCon->close();
    } else {
        header("Location: Booking_approval.php?message=cancel_error");
        exit();
    }
} else {
    header("Location: Booking_approval.php");
    exit();
}
?>