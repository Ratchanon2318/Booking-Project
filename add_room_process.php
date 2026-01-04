<?php
session_start();

// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // Connect to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_id = $_POST['room_id'];
    $room_name = $_POST['room_name'];
    $room_detail = $_POST['room_detail'];
    $equipment_name = $_POST['equipment_name'];

    // Upload room image
    $target_dir = "room_images/";
    $target_file = $target_dir . basename($_FILES["room_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["room_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
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
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
            // Insert new room into database
            $insertSQL = "INSERT INTO room (room_id, room_name, room_detail, room_status, equipment_id)
                          VALUES ('$room_id', '$room_name', '$room_detail', 'Available',
                          (SELECT equipment_id FROM equipment WHERE equipment_name = '$equipment_name'))";
            $objCon->query($insertSQL);

            // Redirect back to manage_meeting_room.php
            header("Location: manage_meeting_room.php");
            exit();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
