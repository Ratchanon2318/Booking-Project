<?php
session_start(); // เปิดใช้งาน session
include_once('./function.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>
    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="./css/registe.css">
</head>

<body>
    <div class="container">
        <div class="register-box bg-light p-5 rounded mt-3">
            <h1>ลงทะเบียน</h1>
            <form method="post" action="register_action.php">
                <div class="mb-3">
                    <label for="user_username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="user_username" name="user_username" placeholder="Username" required>
                </div>
                <div class="mb-3">
                    <label for="user_password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <label for="user_department" class="form-label">Department</label>
                    <input type="text" class="form-control" id="user_department" name="user_department" placeholder="Department">
                </div>
                <div class="mb-3">
                    <label for="user_status" class="form-label">Level</label>
                    <select id="user_status" name="user_status" class="form-select">
                        <option value="User">ผู้ใช้ทั่วไป</option>
                        <option value="Admin">ผู้ดูแลระบบ</option>
                    </select>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit">ลงทะเบียน</button>
                <a href="index.php" class="w-100 btn btn-lg btn-danger mt-3">ยกเลิก</a>
            </form>
        </div>
    </div>
</body>

</html>
