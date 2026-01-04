<?php
// Include database connection file
session_start();
// Include database connection file
include_once('./function.php');
include_once('./menu_admin.php');
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
// เชื่อมต่อฐานข้อมูล
$objCon = connectDB();

// คำสั่ง SQL เพื่อดึงข้อมูลการจองห้องและชื่อห้อง
$sql = "SELECT r.room_name, b.desired_date, b.start_time, b.end_time, b.booking_verify
        FROM booking b
        JOIN room r ON b.room_id = r.room_id";
$result = $objCon->query($sql);

// สร้างตัวแปรเก็บข้อมูลการจองสำหรับปฏิทิน
$events = array();

// วนลูปผลลัพธ์และเพิ่มข้อมูลการจองลงในอาเรย์เหตุการณ์
while ($row = $result->fetch_assoc()) {
    // กำหนดรูปแบบเวลาเริ่มต้นและสิ้นสุดเป็นชั่วโมง:นาที
    $start_time = date('H:i', strtotime($row['start_time']));
    $end_time = date('H:i', strtotime($row['end_time']));

    // กำหนดสถานะการจองเป็น "อนุมัติแล้ว" หรือ "รออนุมัติ" ตามค่าจากฐานข้อมูล
    $status = $row['booking_verify'] == 'Approved' ? 'อนุมัติแล้ว' : 'รออนุมัติ';

    // กำหนดข้อมูลเหตุการณ์ในรูปแบบที่ FullCalendar ต้องการ
    $event = array(
        'title' => $row['room_name'],
        'start' => $row['desired_date'],
        'end' => $row['desired_date'],
        'description' => $status,                           // สถานะการจอง
        'color' => $row['booking_verify'] == 'Approved' ? 'green' : 'red', // กำหนดสีตามสถานะ
        'start_time' => $start_time,                        // เวลาเริ่มต้น
        'end_time' => $end_time                             // เวลาสิ้นสุด
    );

    array_push($events, $event); // เพิ่มเหตุการณ์ลงในอาเรย์
}

// แปลงอาเรย์เหตุการณ์เป็น JSON format
$eventsJSON = json_encode($events);
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8' />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.css' rel='stylesheet' />
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <link rel="stylesheet" href="./css/index.css">
    <title>Meeting Room Calendar</title>
</head>
<body>

    <div class="calendar-container">
        <div id="qr-code-section">
            <img src="./room_images/LINEQR.png" alt="QR Code LINE Group">
            <p>สแกนเข้าร่วมกลุ่ม LINE เพื่อรับการแจ้งเตือนการจอง</p>
        </div>

        <div id="calendar">
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'th', // กำหนดให้แสดงเป็นภาษาไทย
                events: <?php echo json_encode($events); ?>,
                eventColor: function(info) {
                  return info.event.extendedProps.color;
                  },
                  locale: 'th',
                  eventContent: function(info) {
                  var content = '<b>' + info.event.title + '</b><br/>' + 
                                'เวลา: ' + info.event.extendedProps.start_time + ' - ' + info.event.extendedProps.end_time + '<br/>' + 
                                'สถานะ: ' + info.event.extendedProps.description;
                  return { html: content };
                  }
            });
            
            calendar.render();
        });
    </script>
    </div>
    </div>
</body>
</html>
