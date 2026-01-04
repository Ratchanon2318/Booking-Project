<?php
session_start();
// Include database connection file
include_once('./function.php');
include_once('./menu_admin.php');
$objCon = connectDB(); // Connect to the database

// Handle updating room
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_room_changes'])) {
    $room_id = $_POST['room_id']; // Assuming room_id is provided by the user
    $room_name = $_POST['room_name'];
    $room_detail = $_POST['room_detail'];

    // Update room details in the database
    $updateSQL = "UPDATE room SET room_name = '$room_name', room_detail = '$room_detail' WHERE room_id = '$room_id'";
    $objCon->query($updateSQL);

    // Redirect back to the edit_room.php page
    header("Location: manage_meeting_room.php");
    exit();
}
//-----------------------------------------------------------------------------------------------------------------------------
// Handle uploading room image
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_image'])) {
    // Check if room_id is set
    if (!isset($_POST['room_id'])) {
        echo "Invalid request: Missing room_id.";
        exit();
    }

    $room_id = $_POST['room_id'];

    // Check if a file is selected for upload
    if (isset($_FILES['room_image']) && $_FILES['room_image']['name']) {
        // New image file name
        $new_image_name = $room_id . '.jpg';

        // Check if file already exists, if yes, delete it
        if (file_exists('room_images/' . $new_image_name)) {
            unlink('room_images/' . $new_image_name);
        }

        // Move uploaded file to room_images folder with new name
        $target_file = 'room_images/' . $new_image_name;
        if (move_uploaded_file($_FILES['room_image']['tmp_name'], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["room_image"]["name"])) . " has been uploaded.";
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Redirect back to the edit_room.php page
    header("Location: edit_room.php?id=$room_id");
    exit();
}


//-----------------------------------------------------------------------------------------------------------------------------

// Handle adding equipment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_equipment'])) {
    $room_id = $_POST['room_id']; // Assuming room_id is provided by the user
    $new_equipment_name = $_POST['new_equipment_name'];

    // Insert new equipment into the database
    $insertSQL = "INSERT INTO equipment (room_id, equipment_name) VALUES ('$room_id', '$new_equipment_name')";
    $objCon->query($insertSQL);

    // Redirect back to the edit_room.php page
    header("Location: edit_room.php?id=$room_id");
    exit();
}

// Handle deleting equipment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_equipment'])) {
    $equipment_id = $_POST['equipment_id'];
    $room_id = $_POST['room_id']; // Get room_id for redirecting

    // Delete equipment from the database
    $deleteSQL = "DELETE FROM equipment WHERE equipment_id = '$equipment_id'";
    $objCon->query($deleteSQL);

    // Redirect back to the edit_room.php page
    header("Location: edit_room.php?id=$room_id");
    exit();
}

// Handle updating equipment
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_equipment'])) {
    $equipment_id = $_POST['equipment_id'];
    $room_id = $_POST['room_id'];
    $updated_equipment_name = $_POST['updated_equipment_name'];

    // Update equipment name in the database
    $updateSQL = "UPDATE equipment SET equipment_name = '$updated_equipment_name' WHERE equipment_id = '$equipment_id'";
    $objCon->query($updateSQL);

    // Redirect back to the edit_room.php page
    header("Location: edit_room.php?id=$room_id");
    exit();
}

// Fetch room data based on the ID from the URL parameter
if (isset($_GET['id'])) {
    $room_id = $_GET['id'];
    $selectSQL = "SELECT * FROM room WHERE room_id = '$room_id'";
    $result = $objCon->query($selectSQL);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "Room not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Fetch room's equipment
$equipmentSQL = "SELECT * FROM equipment WHERE room_id = '$room_id'";
$equipmentResult = $objCon->query($equipmentSQL);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/edit_room.css">
    <title>Edit Room</title>
</head>
<body>
    <div class="container">
        <h1>แก้ไขข้อมูลห้องประชุม</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
            <label for="room_name">ชื่อห้อง:</label>
            <input type="text" id="room_name" name="room_name" value="<?php echo $row['room_name']; ?>"><br><br>
            <label for="room_detail">รายละเอียดห้อง:</label>
            <textarea id="room_detail" name="room_detail"><?php echo $row['room_detail']; ?></textarea><br><br>
            

        <!-- Display room image -->
        <?php
        $room_image_path = 'room_images/'.$row['room_id'].'.jpg';
        if (file_exists($room_image_path)) {
            $room_image_src = $room_image_path;
        } else {
            $room_image_src = 'default_room_image.jpg';
        }
        $room_image_src .= '?t=' . time();
        echo '<img src="'.$room_image_src.'" alt="'.$row['room_name'].'">';
        ?>

    <h3>อัปโหลดรูปภาพ</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
        เลือกรูปภาพเพื่ออัปโหลด:
        <input type="file" name="room_image" id="room_image">
        <input type="submit" value="อัปโหลดรูปภาพ" name="upload_image">
    </form>  
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["upload_image"])) {
            $target_dir = "room_images/";
            $target_file = $target_dir . basename($_FILES["room_image"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["room_image"]["tmp_name"]);
            if($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }

            // Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }

            // Check file size
            if ($_FILES["room_image"]["size"] > 500000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }

            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }

            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
                    echo "The file ". htmlspecialchars( basename( $_FILES["room_image"]["name"])). " has been uploaded.";
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        }
        ?>

<fieldset>
    <legend>อุปกรณ์:</legend>
    <div class="equipment-table">
        <table>
            <thead>
                <tr>
                    <th>รหัสอุปกรณ์</th>
                    <th>ชื่ออุปกรณ์</th>
                    <th>การดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($equipmentResult->num_rows > 0) {
                    while ($equipmentRow = $equipmentResult->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $equipmentRow['equipment_id'] . '</td>';
                        echo '<td>' . $equipmentRow['equipment_name'] . '</td>';
                        echo '<td>';
                        
                        echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';                    
                        echo '<input type="hidden" name="equipment_id" value="' . $equipmentRow['equipment_id'] . '">';
                        echo '<input type="hidden" name="room_id" value="' . $room_id . '">';
                        echo '<input type="text" name="updated_equipment_name" value="' . $equipmentRow['equipment_name'] . '">';
                        echo '<input type="submit" name="update_equipment" value="Update">';
                        echo '<input type="hidden" name="room_id" value="' . $room_id . '">';
                        echo '<input type="hidden" name="equipment_id" value="' . $equipmentRow['equipment_id'] . '">';
                        echo '<input type="submit" name="delete_equipment" value="Delete">';
                        echo '</form>';
                        
                        // echo '<form method="post" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
                        
                        // echo '</form>';
                        
                        echo '</td>';
                         echo '</tr>';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <h3>เพิ่มอุปกรณ์</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
        <label for="new_equipment_name">ชื่ออุปกรณ์:</label>
        <input type="text" id="new_equipment_name" name="new_equipment_name">
        <input type="submit" name="add_equipment" value="เพิ่ม">
    </form>
</fieldset>

        <input type="submit" name="save_room_changes" value="บันทึกการแก้ไข">
        </form>
    </div>
</body>
</html>
