<?php
session_start();

// Include database connection file
include_once('./function.php');
$objCon = connectDB(); // เชื่อมต่อฐานข้อมูล

// Check if user is logged in as admin
if (!isset($_SESSION['u_level']) || $_SESSION['u_level'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Define variables and initialize with empty values
$equipment_name = "";
$equipment_name_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate equipment name
    $input_name = trim($_POST["equipment_name"]);
    if (empty($input_name)) {
        $equipment_name_err = "Please enter an equipment name.";
    } else {
        $equipment_name = $input_name;
    }

    // Check input errors before inserting into database
    if (empty($equipment_name_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO equipment (equipment_name) VALUES (?)";

        if ($stmt = $objCon->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("s", $param_name);

            // Set parameters
            $param_name = $equipment_name;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to manage equipment page
                header("location: manage_equipment.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            $stmt->close();
        }
    }

    // Close connection
    $objCon->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="manage_style.css">

    <title>Add Equipment</title>
</head>
<body>
    <h1>Add Equipment</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Equipment Name</label>
            <input type="text" name="equipment_name" class="form-control <?php echo (!empty($equipment_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $equipment_name; ?>">
            <span class="invalid-feedback"><?php echo $equipment_name_err; ?></span>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <a href="manage_meeting_room.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</body>
</html>
