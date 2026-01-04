<?php
// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // Connect to the database

// Check if room_id is provided in the URL parameter
if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    // Fetch room data from the database
    $selectRoomSQL = "SELECT * FROM room WHERE room_id = '$room_id'";
    $result = $objCon->query($selectRoomSQL);

    if ($result->num_rows == 1) {
        // Delete room from the database
        $deleteRoomSQL = "DELETE FROM room WHERE room_id = '$room_id'";
        $objCon->query($deleteRoomSQL);

        // Delete equipment related to the room
        $deleteEquipmentSQL = "DELETE FROM equipment WHERE room_id = '$room_id'";
        $objCon->query($deleteEquipmentSQL);

        // Delete room image file if exists
        $room_image_path = "room_images/$room_id.jpg";
        if (file_exists($room_image_path)) {
            unlink($room_image_path);
        }

        // Redirect back to the manage_meeting_room.php page
        header("Location: manage_meeting_room.php");
        exit();
    } else {
        echo "Room not found.";
    }
} else {
    echo "Invalid request.";
}
?>
