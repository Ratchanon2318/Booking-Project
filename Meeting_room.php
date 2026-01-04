<?php
session_start();
// Include database connection file
include_once('./function.php');
include_once('./menu_admin.php');
$objCon = connectDB();

// Check if user is logged in
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}
?>
<!-- ส่วน HTML ในหน้าหลักที่แสดงรายการห้องประชุม -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/Meeting_room.css">
    <title>Meeting Room</title>
    
</head>
<body>
<h1>รายละเอียดห้องประชุม</h1>
    <?php
    // Query to retrieve all rooms
    $sql = "SELECT * FROM room";
    $result = $objCon->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="room-container">';
        while ($row = $result->fetch_assoc()) {
            $room_id = $row['room_id'];
            $room_name = $row['room_name'];
            $room_detail = $row['room_detail'];
            $room_image = $row['room_id'];
    ?>
            <!-- <div class="room-card">
                <a href="Meeting_room_details.php?room_id=<?php echo $room_id; ?>">
                    <?php echo '<img src="room_images/'.$row['room_id'].'.jpg?'.time().'" alt="'.$row['room_name'].'">'; ?>
                    <h3><?php echo $room_name; ?></h3>
                </a>
            </div> -->

            <div class="room-card">
                <a href="Meeting_room_details.php?room_id=<?php echo $room_id; ?>" style="display: flex; align-items: center;">
                    <?php echo '<img src="room_images/'.$row['room_id'].'.jpg?'.time().'" alt="'.$row['room_name'].'">'; ?>
                </a>
                <div>
                    <a href="Meeting_room_details.php?room_id=<?php echo $room_id; ?>" style="text-decoration: none; color: inherit;">
                        <h3><?php echo $room_name; ?></h3>
                        <p><?php echo $room_detail; ?></p>
                    </a>
                </div>
            </div>

    <?php
        }
        echo '</div>';
    } else {
        echo "No rooms available.";
    }
    ?>
</body>
</html>
