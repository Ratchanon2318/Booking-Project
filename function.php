<?php
function connectDB()
{
    $serverName = "localhost";
    $userName = "root";
    $userPassword = "";
    $dbName = "booking_room_sql";

    $objCon = mysqli_connect($serverName, $userName, $userPassword, $dbName);
    mysqli_set_charset($objCon, "utf8");
    return $objCon;
}

function getUserDepartment($user_id)
{
    $conn = connectDB();
    $stmt = $conn->prepare("SELECT user_department FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_department);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $user_department;
}
?>
