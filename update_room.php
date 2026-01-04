<?php
// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $room_name = $_POST['room_name'];
    $room_detail = $_POST['room_detail'];

    // Upload room image
    $target_dir = "room_images/";
    $target_file = $target_dir . basename($_FILES["room_image"]["name"]);
    move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file);

    // Update room data
    $updateRoomSQL = "UPDATE room SET room_name = '$room_name', room_detail = '$room_detail' WHERE id = '$room_id'";
    $objCon->query($updateRoomSQL);

    // Delete existing equipment associations
    $deleteEquipmentSQL = "DELETE FROM room_equipment WHERE room_id = '$room_id'";
    $objCon->query($deleteEquipmentSQL);

    // Insert selected equipment into room_equipment table
    if (isset($_POST['equipment_id']) && is_array($_POST['equipment_id'])) {
        foreach ($_POST['equipment_id'] as $equipment_id) {
            $insertEquipmentSQL = "INSERT INTO room_equipment (room_id, equipment_id) VALUES ('$room_id', '$equipment_id')";
            $objCon->query($insertEquipmentSQL);
        }
    }

    // Redirect back to manage_meeting_room.php
    header("Location: manage_meeting_room.php");
    exit();
}
?>
