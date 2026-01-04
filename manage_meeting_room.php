<?php
session_start();
// Include database connection file
include_once('./function.php');
include_once('./menu_admin.php');
$objCon = connectDB(); // Connect to the database
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
// Fetch room data with equipment names from the database
$sql = "SELECT r.room_id, r.room_name, r.room_detail, GROUP_CONCAT(e.equipment_name SEPARATOR ', ') AS equipment_names
        FROM room r
        LEFT JOIN equipment e ON r.room_id = e.room_id
        GROUP BY r.room_id";
$result = $objCon->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/manage_meeting_room.css">
    <title>Manage Meeting Rooms</title>
</head>
<body>
    <h1>จัดการข้อมูลห้องประชุม</h1>
    
    <table>
        <thead>
            <tr>
                <th>รูปภาพ</th>
                <th>รหัสห้อง</th>
                <th>ชื่อห้อง</th>
                <th>รายละเอียด</th>
                <th>อุปกรณ์</th>
                <th>ดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                <td><?php echo '<img src="room_images/'.$row['room_id'].'.jpg?'.time().'" alt="'.$row['room_name'].'">'; ?></td>
                    <td><?php echo $row['room_id']; ?></td>
                    <td><?php echo $row['room_name']; ?></td>
                    <td><?php echo $row['room_detail']; ?></td>
                    <td><?php echo $row['equipment_names']; ?></td>
                    <td>
                        <!-- <a href="edit_room.php?id=<?php echo $row['room_id']; ?>">Edit</a>
                        <a href="delete_room.php?id=<?php echo $row['room_id']; ?>">Delete</a> -->
                        <a class="edit-link" href="edit_room.php?id=<?php echo $row['room_id']; ?>">แก้ไข</a>
                        <a class="delete-link" href="delete_room.php?id=<?php echo $row['room_id']; ?>">ลบ</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="add_room.php" class="add-room-link">เพิ่มห้องประชุม</a>
</body>
</html>
