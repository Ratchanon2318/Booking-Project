<?php
session_start();
// Include database connection file
include_once('./function.php');
include_once('./menu_admin.php');
include_once('./sendLineNotify.php'); // เพิ่มไฟล์ที่มีฟังก์ชัน sendLineNotify()
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$objCon = connectDB();

// แสดงเฉพาะการจองที่ยังไม่อนุมัติหรือยกเลิก
$sql = "SELECT b.booking_id, b.booking_date, b.booker_name, b.phone_number, b.user_department, r.room_name, b.desired_date, b.start_time, b.end_time, b.booking_verify
        FROM booking b
        JOIN room r ON b.room_id = r.room_id
        WHERE b.booking_verify = 'Pending'
        ORDER BY b.booking_date DESC";  // เรียงลำดับจากวันที่ล่าสุด
$limit = 10;

// หาหน้าปัจจุบัน
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$result = $objCon->query($sql);

// ดึงจำนวนรายการทั้งหมดที่ยังไม่อนุมัติหรือยกเลิก
$sql_count = "SELECT COUNT(*) FROM booking WHERE booking_verify = 'Pending'";
$count_result = $objCon->query($sql_count);
$total_records = $count_result->fetch_row()[0];
$total_pages = ceil($total_records / $limit);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/booking_approval.css">
    <title>Booking Approval</title>
</head>
<body>
    <h1>อนุมัติการจอง</h1>
    <table class="booking-table">
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
                <th>รายละเอียดการจอง</th>
                <th>อนุมัติการจอง</th>
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
                    <td><?php echo $row['booking_date']; ?></td>
                    <td><?php echo $row['booker_name']; ?></td>
                    <td><?php echo $row['phone_number']; ?></td>
                    <td><?php echo $row['user_department']; ?></td>
                    <!-- <td><?php echo $row['room_name']; ?></td> -->
                    <td style="color: <?php echo $room_color; ?>;"><?php echo $room_name; ?></td>
                    <td><?php echo $row['desired_date']; ?></td>
                    <td><?php echo $row['start_time']; ?></td>
                    <td><?php echo $row['end_time']; ?></td>
                    <td>
                        <a class="report_details" href="javascript:void(0);" data-booking-id="<?php echo $row['booking_id']; ?>">รายละเอียด</a>
                    </td>
                    <td>
                        <form method="post" action="process_approval.php">
                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
                            <input type="submit" name="approve" value="อนุมัติ" class="approve-btn">
                        </form>
                    </td>
                    <td>
                        <form method="post" action="cancel_booking_process.php">
                            <input type="hidden" name="booking_id" value="<?php echo $row['booking_id']; ?>">
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
            <a href="report.php?page=<?php echo $page - 1; ?>">&laquo; ก่อนหน้า</a>
        <?php endif; ?>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="report.php?page=<?php echo $i; ?>" <?php if ($page == $i) echo 'class="active"'; ?>><?php echo $i; ?></a>
        <?php endfor; ?>
        <?php if ($page < $total_pages): ?>
            <a href="report.php?page=<?php echo $page + 1; ?>">ถัดไป &raquo;</a>
        <?php endif; ?>
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
