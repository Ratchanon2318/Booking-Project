<?php
session_start();

// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // เชื่อมต่อฐานข้อมูล

// Check if user is logged in as admin
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Check if u_id is provided in the URL
if (!isset($_GET['id'])) {
    header("Location: admin_manage_users.php");
    exit();
}

// Get the u_id from the URL
$user_id = $_GET['id'];

// Fetch user data based on u_id
$strSQL = "SELECT * FROM users WHERE user_id = '$user_id'";
$result = $objCon->query($strSQL);
$row = $result->fetch_assoc();

// Check if user data exists
if (!$row) {
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
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>
    <form method="post" action="update_admin_user.php">
        <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
        <input type="text" name="user_username" value="<?php echo $row['user_username']; ?>" placeholder="Username"><br><br>
        <input type="password" name="user_password" placeholder="Password"><br><br>
        <input type="text" name="user_firstname" value="<?php echo $row['user_firstname']; ?>" placeholder="First Name"><br><br>
        <input type="text" name="user_lastname" value="<?php echo $row['user_lastname']; ?>" placeholder="Last Name"><br><br>
        <input type="text" name="user_tel" value="<?php echo $row['user_tel']; ?>" placeholder="Tel"><br><br>
        <select name="user_status">
            <option value="User" <?php if ($row['user_status'] == 'User') echo 'selected'; ?>>User</option>
            <option value="Admin" <?php if ($row['user_status'] == 'Admin') echo 'selected'; ?>>Admin</option>
        </select><br><br>
        <input type="submit" value="Update">
    </form>
</body>
</html>
