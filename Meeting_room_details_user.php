<?php
session_start();
// Include database connection file
include_once('./function.php');
include_once('./menu_user.php');
$objCon = connectDB(); // Connect to the database

// Check if room_id is set and is a valid number
if(isset($_GET['room_id']) && is_numeric($_GET['room_id'])){
    $room_id = $_GET['room_id'];

    // Query to get room details by room_id
    $sql = "SELECT * FROM room WHERE room_id = '$room_id'";
    $result = $objCon->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css">
    <link rel="stylesheet" href="./css/Meeting_room_details.css">
    <title>Meeting Room Details</title>
</head>
<body>
    <div class="container">
        <h1>รายละเอียดห้องประชุม</h1>
        <img src="room_images/<?php echo $row['room_id']; ?>.jpg" alt="<?php echo $row['room_name']; ?>" style="width:200px; height:200px;"><br>
        <strong>ชื่อห้องประชุม: </strong><span class="room-name"><?php echo $row['room_name']; ?></span><br>
        <strong>รายละเอียดห้องประชุม: </strong><span class="room-detail"><?php echo $row['room_detail']; ?></span><br>
        
        <strong>อุปกรณ์ที่เลือกได้: </strong>
        <?php
        $sql = "SELECT * FROM equipment WHERE room_id = '$room_id'";
        $result = $objCon->query($sql);
        echo "<ul>";
        while ($equipment = $result->fetch_assoc()) {
            echo "<li>". $equipment['equipment_name'] . "</li>";
        }
        echo "</ul>";
        ?>
        
        <div class="statistics-container">
        
        <a href="room_statistics_user.php?room_id=<?php echo $row['room_id']; ?>" class="statistics-link">สถิติการใช้งาน</a>
        </div>
    </div>
</body>
</html>
<?php
    } else {
        echo "Room not found.";
    }
} else {
    echo "Invalid room ID.";
}

?>

