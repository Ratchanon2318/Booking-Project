<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $room_id = $_POST['room_id'];
    $user_id = $_SESSION['user_id']; // Assuming user is logged in
    $desired_date = $_POST['desired_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $equipment_ids = isset($_POST['equipment_id']) ? $_POST['equipment_id'] : array();

    // Insert booking data into the database
    $stmt = $objCon->prepare("INSERT INTO booking (room_id, user_id, desired_date, start_time, end_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $room_id, $user_id, $desired_date, $start_time, $end_time);
    $stmt->execute();
    $booking_id = $stmt->insert_id; // Get the ID of the newly inserted booking
    $stmt->close();

    // Insert selected equipment into the booking_equipment table
    if (!empty($equipment_ids)) {
        $stmt = $objCon->prepare("INSERT INTO booking_equipment (booking_id, equipment_id) VALUES (?, ?)");
        foreach ($equipment_ids as $equipment_id) {
            $stmt->bind_param("ii", $booking_id, $equipment_id);
            $stmt->execute();
        }
        $stmt->close();
    }

    // Redirect back to Booking_meeting_room.php
    header("Location: Booking_meeting_room.php");
    exit();
}
?>
