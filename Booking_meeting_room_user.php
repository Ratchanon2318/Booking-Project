<?php
session_start();
include_once('./function.php');
include_once('./menu_user.php');
include_once('./sendLineNotify.php'); // เพิ่มไฟล์ที่มีฟังก์ชัน sendLineNotify()
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    $objCon = connectDB(); // Connect to the database

    // Fetch room data
    $sql = "SELECT * FROM room";
    $result = $objCon->query($sql);
    
    // Fetch equipment data based on selected room
    $selected_room_id = $_GET['room_id'] ?? null; // Get selected room_id from URL parameter
    if ($selected_room_id) {
        $equipment_sql = "SELECT * FROM equipment WHERE room_id = '$selected_room_id'";
        $equipment_result = $objCon->query($equipment_sql);
    }
    
    // Fetch user department
    $user_id = $_SESSION['user_id'];
    $user_department = getUserDepartment($user_id);
    
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if room is selected
        if (!isset($_POST['room_id'])) {
            echo "<script>alert('โปรดเลือกห้องประชุม');</script>";
        } else {
            $room_id = $_POST['room_id'];
            $desired_date = $_POST['desired_date'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];
    
            // Fetch booker name and phone number
            $booker_name = $_POST['booker_name'];
            $phone_number = $_POST['phone_number'];
    
            // Check if desired date is not in the past
            $today = date("Y-m-d");
            if ($desired_date < $today) {
                echo "<script>alert('ไม่สามารถจองห้องประชุมย้อนหลังได้');</script>";
            } else {
                // Check room availability
                $availability_sql = "SELECT * FROM booking WHERE room_id = '$room_id' AND ((desired_date = '$desired_date' AND ((start_time <= '$start_time' AND end_time > '$start_time') OR (start_time < '$end_time' AND end_time >= '$end_time'))) OR (desired_date = '$desired_date' AND start_time >= '$start_time' AND end_time <= '$end_time'))";
                $availability_result = $objCon->query($availability_sql);
    
                if ($availability_result->num_rows > 0) {
                    echo "<script>alert('ห้องประชุมถูกจองไว้แล้วตามวันและเวลาที่เลือก โปรดเลือกวันที่หรือเวลาอื่น');</script>";
                } else {
                    // Proceed with booking
                    // Insert booking data into the database
                    $stmt = $objCon->prepare("INSERT INTO booking (room_id, user_id, user_department, booker_name, phone_number, desired_date, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("iissssss", $room_id, $user_id, $user_department, $booker_name, $phone_number, $desired_date, $start_time, $end_time);
                    $stmt->execute();
                    $stmt->close();
    
                    // Get the booking_id of the inserted record
                    $booking_id = $objCon->insert_id;
    
                    // Insert equipment selections into booking_equipment table
                    if (isset($_POST['equipment_id'])) {
                        $equipment_ids = $_POST['equipment_id'];
                        foreach ($equipment_ids as $equipment_id) {
                            $stmt = $objCon->prepare("INSERT INTO booking_equipment (booking_id, equipment_id) VALUES (?, ?)");
                            $stmt->bind_param("ii", $booking_id, $equipment_id);
                            $stmt->execute();
                            $stmt->close();
                        }
                    }
                      // Fetch room_name for LINE Notify message
                    $room_name = '';
                    $room_name_sql = "SELECT room_name FROM room WHERE room_id = '$room_id'";
                    $room_name_result = $objCon->query($room_name_sql);
                    if ($room_name_result->num_rows > 0) {
                        $room_name_row = $room_name_result->fetch_assoc();
                        $room_name = $room_name_row['room_name'];
                    }
    
                    // Send LINE Notify
                    $message = "ห้องประชุมถูกจอง:📌\nห้อง: $room_name\nวันที่: $desired_date\nเวลา: $start_time - $end_time\nแผนก: $user_department\nผู้จอง: $booker_name\nโทรศัพท์: $phone_number";
                    $token = "j8ZvVWO1KVOhHWu7XtQDuEj2b78g0W5acCihRUeFFmy"; // แก้ไข Token ที่นี่
    
                    // Call sendLineNotify function
                    sendLineNotify($message, $token);
                    // Redirect or show success message
                    echo "<script>alert('จองห้องประชุมเรียบร้อยแล้ว!');</script>";
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/booking_meeting_room.css">
    <title>Booking Meeting Room</title>
</head>
<body>
    <div class="container">
        <h1>จองห้องประชุม</h1>
        <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="form-select-room">
            <label for="room">เลือกห้อง:</label>
            <select id="room" name="room_id">
                <?php if ($result->num_rows > 0) : ?>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                        <option value="<?php echo htmlspecialchars($row['room_id']); ?>" <?php if ($row['room_id'] == $selected_room_id) echo 'selected'; ?>><?php echo htmlspecialchars($row['room_name']); ?></option>
                    <?php endwhile; ?>
                <?php else : ?>
                    <option value="" disabled>ไม่มีห้องว่าง</option>
                <?php endif; ?>
            </select>
            <input type="submit" value="เลือก">
        </form>

        <?php if ($selected_room_id) : ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="form-book-room">
                <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($selected_room_id); ?>">
                <label for="desired_date">วันที่จอง:</label>
                <input type="date" id="desired_date" name="desired_date" required>
                
                <label for="start_time">เวลาเริ่ม:</label>
                <input type="time" id="start_time" name="start_time" required>
                
                <label for="end_time">เวลาสิ้นสุด:</label>
                <input type="time" id="end_time" name="end_time" required>

                <label for="booker_name">ชื่อผู้จอง:</label>
                <input type="text" id="booker_name" name="booker_name" required>

                <label for="phone_number">เบอร์โทรศัพท์:</label>
                <input type="text" id="phone_number" name="phone_number" required>

                <label for="user_department">แผนก:</label>
                <input type="text" id="user_department" name="user_department" value="<?php echo htmlspecialchars($user_department); ?>" readonly>
                
                <fieldset>
                    <legend>เลือกอุปกรณ์:</legend>
                    <?php if ($equipment_result->num_rows > 0) : ?>
                        <?php while ($equipment_row = $equipment_result->fetch_assoc()) : ?>
                            <div class="equipment-item">
                                <input type="checkbox" id="equipment_<?php echo htmlspecialchars($equipment_row['equipment_id']); ?>" name="equipment_id[]" value="<?php echo htmlspecialchars($equipment_row['equipment_id']); ?>">
                                <label for="equipment_<?php echo htmlspecialchars($equipment_row['equipment_id']); ?>"><?php echo htmlspecialchars($equipment_row['equipment_name']); ?></label>
                            </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <p>ไม่มีอุปกรณ์ใช้งานสำหรับห้องนี้</p>
                    <?php endif; ?>
                </fieldset>
                <input type="submit" value="จองห้อง">
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var desiredDateInput = document.getElementById('desired_date');
                    var today = new Date().toISOString().split('T')[0];
                    desiredDateInput.setAttribute('min', today);
                });
            </script>
        <?php endif; ?>
        <!-- <a href="admin_index.php" class="back-link">กลับสู่หน้าหลักผู้ดูแล</a> -->
    </div>
</body>
</html>

