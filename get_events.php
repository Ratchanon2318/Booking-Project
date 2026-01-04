<?php
// Include database connection file
include_once('function.php');
$objCon = connectDB();

// Fetch booking data
$sql = "SELECT room.room_name, booking.start_time, booking.end_time, booking.booking_verify
        FROM booking
        JOIN room ON booking.room_id = room.room_id";
$result = $objCon->query($sql);

// Convert booking data to FullCalendar events format
$events = array();

while ($row = $result->fetch_assoc()) {
    $event = array(
        'title' => $row['room_name'],
        'start' => $row['start_time'],
        'end' => $row['end_time'],
        'booking_verify' => $row['booking_verify']
    );

    // แปลง start_time เป็น timestamp เพื่อใช้ในการเรียงลำดับ
    $start_timestamp = strtotime($row['start_time']);
    $event['start_timestamp'] = $start_timestamp;

    array_push($events, $event);
}

// เรียง events ตาม start_time จากน้อยไปมาก
usort($events, function($a, $b) {
    return $a['start_timestamp'] - $b['start_timestamp'];
});

// ลบ start_timestamp ที่ใช้ในการเรียงลำดับออก
foreach ($events as &$event) {
    unset($event['start_timestamp']);
}
