<?php
session_start();

// Include database connection file
include_once('./function.php');
include_once('./menu_user.php');

// ตรวจสอบสถานะการล็อกอินและสถานะผู้ใช้
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'User') {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$objCon = connectDB();

// ตรวจสอบ user_id ของผู้ใช้ที่ล็อกอิน
$user_id = $_SESSION['user_id'];

// กำหนดจำนวนรายการต่อหน้า
$items_per_page = 10;

// รับค่าหน้าปัจจุบันจาก URL ถ้าไม่มีให้ตั้งเป็นหน้าแรก (1)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// คำนวณ offset สำหรับการ query
$offset = ($page - 1) * $items_per_page;

// ดึงข้อมูลการจองเฉพาะของผู้ใช้ที่ล็อกอินพร้อมการแบ่งหน้า
$sql = "SELECT b.booking_id, b.booking_date, b.booker_name, b.phone_number, b.user_department, r.room_name, b.desired_date, b.start_time, b.end_time, b.booking_verify
        FROM booking b
        JOIN room r ON b.room_id = r.room_id
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC
        LIMIT ? OFFSET ?";  // แบ่งหน้าด้วย LIMIT และ OFFSET

$stmt = $objCon->prepare($sql);
$stmt->bind_param("iii", $user_id, $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// นับจำนวนรายการทั้งหมดเพื่อคำนวณจำนวนหน้า
$count_sql = "SELECT COUNT(*) AS total FROM booking WHERE user_id = ?";
$count_stmt = $objCon->prepare($count_sql);
$count_stmt->bind_param("i", $user_id);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_items = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_items / $items_per_page);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/cancel_booking.css">
    <title>รายงานการจอง</title>
</head>
<body>
    <h1>รายงานการจอง</h1>
    <div class="report-container">
        <div class="menu-container">
            <ul class="menu-links">
                <li><a href="report_user.php">รายงานการจอง</a></li>
                <li><a href="cancelled_report_user.php">รายงานยกเลิกการจอง</a></li>
                <!-- เพิ่มเมนูอื่นๆที่นี่ -->
            </ul>
        </div>
        <table class="report-table">
            <thead>
                <tr>
                    <th>วันที่ทำรายการ</th>
                    <th>ชื่อผู้จอง</th>
                    <th>เบอร์โทร</th>
                    <th>แผนก</th>
                    <th>ห้อง</th>
                    <th>วันที่ต้องการจอง</th>
                    <th>เริ่มเวลา</th>
                    <th>สิ้นสุดเวลา</th>
                    <th>สถานะการจอง</th>
                    <th>รายละเอียดการจอง</th>
                    <th>ยกเลิกการจอง</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) :
                    $room_name = htmlspecialchars($row['room_name']);
                    // กำหนดสีตาม room_name
                    $room_color = '';
                    switch ($room_name) {
                        case 'ห้องประชุม OPD':
                            $room_color = '#f54500'; 
                            break;
                        case 'ห้องประชุม ตึกส่งเสริม':
                            $room_color = '#ff00ff'; 
                            break;
                        
                        default:
                            $room_color = 'black'; 
                            break;
                    }
                    ?>
                
                    <tr>
                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['booker_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_department']); ?></td>
                        <td style="color: <?php echo $room_color; ?>;"><?php echo $room_name; ?></td>
                        <td><?php echo htmlspecialchars($row['desired_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                        <td>
                            <?php
                            if ($row['booking_verify'] == 'Approved') {
                                echo '<span style="color: green;">อนุมัติ</span>';
                            } else {
                                echo '<span style="color: red;">รออนุมัติ</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <a class="report_details" href="javascript:void(0);" data-booking-id="<?php echo htmlspecialchars($row['booking_id']); ?>">รายละเอียด</a>
                        </td>
                        <td>
                            <form method="post" action="cancel_report_user_process.php">
                                <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($row['booking_id']); ?>">
                                <input type="submit" name="cancel" value="ยกเลิกการจอง" class="cancel-btn">
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <!-- การแบ่งหน้า -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">&laquo; ก่อนหน้า</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" <?php if ($i == $page) echo 'class="active"'; ?>><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>">ถัดไป &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Popup -->
    <div class="overlay"></div>
    <div class="popup" id="popup">
        <span class="close-btn" id="close-btn">&times;</span>
        <div id="popup-content"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const detailsLinks = document.querySelectorAll('.report_details');
            const popup = document.getElementById('popup');
            const overlay = document.querySelector('.overlay');
            const closeBtn = document.getElementById('close-btn');
            const popupContent = document.getElementById('popup-content');

            detailsLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const bookingId = this.getAttribute('data-booking-id');
                    fetch(`get_booking_details.php?booking_id=${bookingId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(data => {
                            popupContent.innerHTML = data;
                            popup.style.display = 'block';
                            overlay.style.display = 'block';
                        })
                        .catch(error => {
                            console.error('Error fetching booking details:', error);
                            alert('Error fetching booking details. Please try again later.');
                        });
                });
            });

            closeBtn.addEventListener('click', function() {
                popup.style.display = 'none';
                overlay.style.display = 'none';
            });
        });
    </script>
</body>
</html>
