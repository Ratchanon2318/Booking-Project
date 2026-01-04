<?php
// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // Connect to the database

// Fetch room data from the database
$roomSQL = "SELECT * FROM room";
$roomResult = $objCon->query($roomSQL);

// Fetch equipment data from the database
$equipmentSQL = "SELECT * FROM equipment";
$equipmentResult = $objCon->query($equipmentSQL);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room</title>
</head>
<body>
    <h1>Manage Room</h1>
    <table border="1">
        <tr>
            <th>Room ID</th>
            <th>Room Name</th>
            <th>Room Detail</th>
            <th>Room Image</th>
            <th>Equipment</th>
            <th>Action</th>
        </tr>
        <?php
        if ($roomResult->num_rows > 0) {
            while ($row = $roomResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['room_id'] . "</td>";
                echo "<td>" . $row['room_name'] . "</td>";
                echo "<td>" . $row['room_detail'] . "</td>";
                echo "<td><img src='" . $row['room_image'] . "' width='100'></td>";

                // Fetch equipment for this room
                $equipmentSQL = "SELECT equipment_name FROM equipment JOIN room_equipment ON equipment.id = room_equipment.equipment_id WHERE room_equipment.room_id = " . $row['id'];
                $equipmentResult = $objCon->query($equipmentSQL);
                $equipmentList = [];
                while ($equipment = $equipmentResult->fetch_assoc()) {
                    $equipmentList[] = $equipment['equipment_name'];
                }

                echo "<td>" . implode(", ", $equipmentList) . "</td>";
                echo "<td><a href='edit_room.php?id=" . $row['id'] . "'>Edit</a> | <a href='delete_room.php?id=" . $row['id'] . "'>Delete</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No rooms found</td></tr>";
        }
        ?>
    </table>
    <br>
    <a href="add_room.php">Add Room</a>
</body>
</html>
