<?php
session_start();
include_once('./function.php');
include_once('./menu_admin.php');
$objCon = connectDB(); // Connect to the database

if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $room_name = $_POST['room_name'];
    $room_detail = $_POST['room_detail'];

    // Insert new room into the database
    $insertSQL = "INSERT INTO room (room_name, room_detail) VALUES ('$room_name', '$room_detail')";
    $objCon->query($insertSQL);

    // Get the room_id of the newly added room
    $room_id = $objCon->insert_id;

    // Upload room image
    if ($_FILES['room_image']['name']) {
        $target_dir = "room_images/";
        $target_file = $target_dir . $room_id . ".jpg";
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($_FILES["room_image"]["name"], PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["room_image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["room_image"]["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["room_image"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars(basename($_FILES["room_image"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Redirect back to the manage_meeting_room.php page
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
    <link rel="stylesheet" href="./css/add_room.css">
    <title>Add Room</title>
</head>
<body>
    <div class="container">
        <h1>เพิ่มห้องประชุม</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
            <label for="room_name">ชื่อห้องประชุม:</label>
            <input type="text" id="room_name" name="room_name">
            <label for="room_detail">รายละเอียด:</label>
            <textarea id="room_detail" name="room_detail"></textarea>
            <label for="room_image">รูปภาพห้องประชุม:</label>
            <input type="file" id="room_image" name="room_image">
            <input type="submit" name="add_room" value="เพิ่มห้องประชุม">
        </form>
    </div>
</body>
</html>
