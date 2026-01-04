<?php
session_start();

include_once('./function.php');
include_once('./menu_user.php');

if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'User') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$objCon = connectDB();

// Count total records for the logged-in user
$sql_count = "SELECT COUNT(*) AS total_records FROM cancelled_booking WHERE user_id = ?";
$stmt_count = $objCon->prepare($sql_count);
if (!$stmt_count) {
    die("Error preparing statement: " . $objCon->error);
}
$stmt_count->bind_param("i", $user_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
if (!$result_count) {
    die("Error executing query: " . $objCon->error);
}
$row_count = $result_count->fetch_assoc();
$total_records = $row_count['total_records'];

$records_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $records_per_page;

$sql = "SELECT cb.cancelled_id, cb.booking_date, u.user_username AS user_name, 
        u.user_department, r.room_name, cb.desired_date, 
        TIME_FORMAT(cb.start_time, '%H:%i') AS start_time, 
        TIME_FORMAT(cb.end_time, '%H:%i') AS end_time, 
        cu.user_username AS cancelled_by, 
        DATE_FORMAT(cb.cancelled_at, '%Y-%m-%d') AS cancelled_date
        FROM cancelled_booking cb
        JOIN room r ON cb.room_id = r.room_id
        JOIN users u ON cb.user_id = u.user_id
        JOIN users cu ON cb.cancelled_by = cu.user_id
        WHERE cb.user_id = ?
        ORDER BY cb.cancelled_at DESC
        LIMIT ?, ?";

$stmt = $objCon->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $objCon->error);
}
$stmt->bind_param("iii", $user_id, $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/report.css">
    <title>Cancelled Reports</title>
</head>
<body>
    <h1>รายงานยกเลิกการจอง</h1>
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
                    <th>ชื่อผู้ใช้</th>
                    <th>แผนก</th>
                    <th>ห้อง</th>
                    <th>วันที่ต้องการจอง</th>
                    <th>เริ่มเวลา</th>
                    <th>สิ้นสุดเวลา</th>
                    <th>วันทำการยกเลิก</th>
                    <th>ยกเลิกโดย</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_department']); ?></td>
                        <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['desired_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['cancelled_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['cancelled_by']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- Pagination -->
        <div class="pagination">
            <?php
                $total_pages = ceil($total_records / $records_per_page);
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = ($i == $current_page) ? 'active' : '';
                    echo "<a href='?page=$i' class='$active'>$i</a>";
                }
            ?>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
$objCon->close();
?>
