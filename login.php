<?php
session_start();

// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_username = $_POST['user_username'];
    $user_password = $_POST['user_password'];

    // Fetch user data from database
    $strSQL = "SELECT * FROM users WHERE user_username = '$user_username' AND user_password = md5('$user_password')";
    $result = $objCon->query($strSQL);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['user_id']; // Set user id to session
        $_SESSION['user_username'] = $row['user_username'];
        $_SESSION['user_password'] = $row['user_password'];
        $_SESSION['user_status'] = $row['user_status'];

        if ($row['user_status'] == 'Admin') {
            header("Location: admin_index.php");
        } else if ($row['user_status'] == 'User'){
            header("Location: user_index.php");
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <title>Login</title>
</head>
<body class="text-center">
    <main class="form-signin">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <h1 class="h3 mb-3 fw-normal">ระบบยืนยันตัวตน</h1>
            <div class="form-floating">
                <input type="text" class="form-control" id="floatingInput" name="user_username" placeholder="Username">
                <label for="floatingInput">Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="floatingPassword" name="user_password" placeholder="Password">
                <label for="floatingPassword">Password</label>
                
            </div>
            <?php if (isset($error)) echo "<div class='error-message'>$error</div>"; ?>
            <button class="w-100 btn btn-lg btn-primary" type="submit">เข้าสู่ระบบ</button>
            <!-- <a href="register.php" class="w-100 btn btn-lg btn-secondary mt-3">ลงทะเบียน</a> -->
        </form>
    </main>
    
</body>
</html>
