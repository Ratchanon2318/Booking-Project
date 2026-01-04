<?php
session_start();
// เชื่อมต่อกับฐานข้อมูล
include_once('./function.php');
include_once('./menu_user.php');
$objCon = connectDB(); // เชื่อมต่อกับฐานข้อมูล

// ตรวจสอบว่า room_id ถูกตั้งค่าและเป็นตัวเลขหรือไม่
$selectedRoomId = isset($_GET['room_id']) && is_numeric($_GET['room_id']) ? $_GET['room_id'] : '';
$selectedYear = isset($_GET['year']) && is_numeric($_GET['year']) ? $_GET['year'] : date('Y');
$viewType = isset($_GET['view_type']) ? $_GET['view_type'] : 'table';

// ดึงชื่อห้องทั้งหมดสำหรับ dropdown
$roomResult = $objCon->query("SELECT room_id, room_name FROM room");
$rooms = array();
while ($row = $roomResult->fetch_assoc()) {
    $rooms[] = $row;
}

// ดึงปีที่มีการจอง
$yearResult = $objCon->query("SELECT DISTINCT YEAR(booking_date) as year FROM booking ORDER BY year DESC");
$years = array();
while ($row = $yearResult->fetch_assoc()) {
    $years[] = $row['year'];
}

$statistics = array();
if ($selectedRoomId) {
    // Query เพื่อดึงข้อมูลสถิติของห้องที่เลือก
    $sql = "SELECT MONTH(booking_date) as month, COUNT(*) as bookings 
            FROM booking 
            WHERE room_id = ? AND YEAR(booking_date) = ?
            GROUP BY MONTH(booking_date)";
    $stmt = $objCon->prepare($sql);
    $stmt->bind_param("ii", $selectedRoomId, $selectedYear);
    $stmt->execute();
    $result = $stmt->get_result();

    // เก็บผลลัพธ์ใน array แบบ associative
    while ($row = $result->fetch_assoc()) {
        $month = $row['month'];
        $bookings = $row['bookings'];
        $statistics[$month] = $bookings;
    }
}

// ฟังก์ชั่นเพื่อแปลงตัวเลขเดือนเป็นชื่อเดือนภาษาไทย
function getThaiMonth($month) {
    $thaiMonths = array(
        1 => 'มกราคม',
        2 => 'กุมภาพันธ์',
        3 => 'มีนาคม',
        4 => 'เมษายน',
        5 => 'พฤษภาคม',
        6 => 'มิถุนายน',
        7 => 'กรกฎาคม',
        8 => 'สิงหาคม',
        9 => 'กันยายน',
        10 => 'ตุลาคม',
        11 => 'พฤศจิกายน',
        12 => 'ธันวาคม'
    );
    return $thaiMonths[$month];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">
    <link rel="stylesheet" href="./css/room_statistics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>สถิติการใช้งานห้องประชุม</title>
</head>
<body>
    <div class="container">
        <h1>สถิติการใช้งานห้องประชุม</h1>
        <form method="GET" action="">
            <label for="room_id">เลือกห้อง:</label>
            <select name="room_id" id="room_id" required>
                <option value="">-- เลือกห้อง --</option>
                <?php
                foreach ($rooms as $room) {
                    $selected = ($room['room_id'] == $selectedRoomId) ? 'selected' : '';
                    echo "<option value='{$room['room_id']}' $selected>{$room['room_name']}</option>";
                }
                ?>
            </select>
            <label for="year">เลือกปี:</label>
            <select name="year" id="year" required>
                <?php
                foreach ($years as $year) {
                    $selected = ($year == $selectedYear) ? 'selected' : '';
                    echo "<option value='$year' $selected>$year</option>";
                }
                ?>
            </select>
            <label for="view_type">รูปแบบการแสดงผล:</label>
            <select name="view_type" id="view_type" required>
                <option value="table" <?= $viewType == 'table' ? 'selected' : '' ?>>ตาราง</option>
                <option value="chart" <?= $viewType == 'chart' ? 'selected' : '' ?>>กราฟ</option>
            </select>
            <button type="submit">แสดงสถิติ</button>
        </form>

        <?php if ($selectedRoomId): ?>
            <h2>สถิติสำหรับห้อง: 
                <?php
                foreach ($rooms as $room) {
                    if ($room['room_id'] == $selectedRoomId) {
                        echo $room['room_name'];
                        break;
                    }
                }
                ?>
            </h2>
            <?php if ($viewType == 'table'): ?>
                <table>
                    <thead>
                        <tr>
                            <th>เดือน</th>
                            <th>จำนวนการจอง</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $bookings = isset($statistics[$i]) ? $statistics[$i] : 0;
                            echo "<tr><td>".getThaiMonth($i)."</td><td>$bookings</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <canvas id="bookingChart"></canvas>
                <script>
                    const ctx = document.getElementById('bookingChart').getContext('2d');
                    const bookingChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
                            datasets: [{
                                label: 'จำนวนการจอง',
                                data: [
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        echo isset($statistics[$i]) ? $statistics[$i] : 0;
                                        echo $i < 12 ? ',' : '';
                                    }
                                    ?>
                                ],
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
