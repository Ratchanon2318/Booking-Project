<?php
// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $equipment_id = $_GET['id'];
    
    // Delete equipment from the database
    $deleteSQL = "DELETE FROM equipment WHERE equipment_id = '$equipment_id'";
    $objCon->query($deleteSQL);

    // Redirect back to the edit_room.php page with room_id parameter
    if (isset($_GET['room_id'])) {
        $room_id = $_GET['room_id'];
        header("Location: edit_room.php?id=$room_id");
    } else {
        header("Location: manage_meeting_room.php");
    }
    exit();
} else {
    echo "Invalid request.";
    exit();
}
?>
