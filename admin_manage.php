<?php
session_start();
// Include database connection file
include_once('./function.php');
include_once('./menu_admin.php');
$objCon = connectDB();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from session
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id='$user_id'";
$result = $objCon->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_username = $row['user_username'];
    $user_department = $row['user_department'];
    $user_status = $row['user_status'];
} else {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/manage_user.css">
    <title>Edit Profile</title>
</head>
<body>
    <h1>แก้ไขข้อมูลผู้ใช้งาน</h1>
    <form id="editUserForm" method="post" action="update_admin.php">
        <input type="hidden" id="editUserId" name="user_id" value="<?php echo $user_id; ?>">
        <label for="editUsername">Username:</label>
        <input type="text" id="editUsername" name="user_username" value="<?php echo $user_username; ?>"><br><br>
        <label for="editPassword">New Password:</label>
        <input type="password" id="editPassword" name="user_password"><br><br>
        <label for="editDepartment">แผนก:</label>
        <input type="text" id="editDepartment" name="user_department" value="<?php echo $user_department; ?>"><br><br>
        <input type="submit" value="บันทึกการเปลี่ยนแปลง">
    </form>
</body>
</html>
