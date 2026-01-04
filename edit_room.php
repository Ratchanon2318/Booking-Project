<?php
session_start();
include_once('./function.php');
include_once('./menu_admin.php');
$objCon = connectDB();

if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];
    $sql = "SELECT * FROM room WHERE room_id = $room_id";
    $result = $objCon->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "No room found.";
        exit();
    }
}

// Fetch equipment data for the room
$sql = "SELECT * FROM equipment WHERE room_id = $room_id";
$equipment_result = $objCon->query($sql);
$equipment = [];
while ($equip = $equipment_result->fetch_assoc()) {
    $equipment[] = $equip;
}

if (isset($_POST['submit'])) {
    $room_id = $_POST['room_id'];
    $room_name = $_POST['room_name'];
    $room_detail = $_POST['room_detail'];

    // Handle room image upload
    if ($_FILES['room_image']['name'] != '') {
        $target_dir = "room_images/";
        $target_file = $target_dir . $room_id . ".jpg"; // Save file with room_id as name
        $imageFileType = strtolower(pathinfo($_FILES['room_image']['name'], PATHINFO_EXTENSION));
        $extensions_arr = array("jpg", "jpeg", "png", "gif");

        if (in_array($imageFileType, $extensions_arr)) {
            // Delete current room image if exists
            if (file_exists($target_file)) {
                unlink($target_file);
            }

            // Upload new room image
            move_uploaded_file($_FILES['room_image']['tmp_name'], $target_file);
        }
    }

    // Update room details in the database
    $sql = "UPDATE room SET room_name = '$room_name', room_detail = '$room_detail' WHERE room_id = $room_id";
    $objCon->query($sql);

    // Handle equipment updates
    if (isset($_POST['equipment'])) {
        foreach ($_POST['equipment'] as $equip_id => $equip_name) {
            if ($equip_name != '') {
                $sql = "UPDATE equipment SET equipment_name = '$equip_name' WHERE equipment_id = $equip_id";
                $objCon->query($sql);
            } else {
                // Delete equipment if name is empty
                $sql = "DELETE FROM equipment WHERE equipment_id = $equip_id";
                $objCon->query($sql);
            }
        }
    }

    // Handle new equipment
    if (isset($_POST['new_equipment']) && !empty($_POST['new_equipment'])) {
        foreach ($_POST['new_equipment'] as $new_equip_name) {
            if ($new_equip_name != '') {
                $sql = "INSERT INTO equipment (room_id, equipment_name) VALUES ($room_id, '$new_equip_name')";
                $objCon->query($sql);
            }
        }
    }

    // Handle deleted equipment
    if (isset($_POST['deleted_equipment']) && !empty($_POST['deleted_equipment'])) {
        $deleted_equipment_ids = explode(',', $_POST['deleted_equipment']);
        foreach ($deleted_equipment_ids as $deleted_equip_id) {
            if (!empty($deleted_equip_id)) {
                $sql = "DELETE FROM equipment WHERE equipment_id = $deleted_equip_id";
                $objCon->query($sql);
            }
        }
    }

    // Redirect to manage meeting rooms page
    header("Location: manage_meeting_room.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="./css/edit_room.css">
    <title>Edit Room</title>
</head>
<body>
    <div class="container">
    <h1>แก้ไขห้องประชุม</h1>
    <?php if (isset($row)) : ?>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="hidden" name="room_id" value="<?php echo $row['room_id']; ?>">
        <label for="room_name">ชื่อห้องประชุม:</label>
        <input type="text" id="room_name" name="room_name" value="<?php echo $row['room_name']; ?>">
        <label for="room_detail">รายละเอียดห้องประชุม:</label>
        <textarea id="room_detail" name="room_detail"><?php echo $row['room_detail']; ?></textarea>
        <label for="room_image">รูปภาพห้องประชุม:</label>
        <?php
        $image_path = "room_images/" . $row['room_id'] . ".jpg";
        if (file_exists($image_path)) : ?>
            <img src="<?php echo $image_path; ?>" alt="<?php echo $row['room_name']; ?>" style="max-width: 300px; max-height: 300px;">
        <?php endif; ?>
        <input type="file" id="room_image" name="room_image">
        <h2>อุปกรณ์</h2>
        <div id="equipment-list">
            <?php foreach ($equipment as $equip) : ?>
            <div class="row">
                <input type="text" name="equipment[<?php echo $equip['equipment_id']; ?>]" value="<?php echo $equip['equipment_name']; ?>">
                <button type="button" onclick="removeEquipment(this, <?php echo $equip['equipment_id']; ?>)">ลบ</button>
            </div>  
            <?php endforeach; ?>
        </div>
        <h3>เพิ่มอุปกรณ์</h3>
        <div id="new-equipment-list">
            <input type="text" name="new_equipment[]" placeholder="อุปกรณ์ใหม่">
        </div>
        <button type="button" onclick="addNewEquipment()">เพิ่มอุปกรณ์</button>
        <button type="submit" name="submit">บันทึกการเปลี่ยนแปลง</button>
        <input type="hidden" name="deleted_equipment" id="deleted-equipment">
    </form>
    <?php else: ?>
        <p>ไม่พบห้อง.</p>
    <?php endif; ?>
    <script>
        // ฟังก์ชันสำหรับลบรายการอุปกรณ์
        function removeEquipment(element, equip_id) {
            var deletedEquipment = $('#deleted-equipment');
            var deletedValue = deletedEquipment.val();
            deletedEquipment.val(deletedValue + (deletedValue ? ',' : '') + equip_id);
            $(element).closest('.row').remove();
        }

        // ฟังก์ชันสำหรับเพิ่ม input field สำหรับอุปกรณ์ใหม่
        function addNewEquipment() {
            var newEquipList = $('#new-equipment-list');
            var newEquipInput = $('<input type="text" name="new_equipment[]" placeholder="New Equipment">');
            newEquipList.append(newEquipInput);
        }
    </script>
    </div>
</body>
</html>
