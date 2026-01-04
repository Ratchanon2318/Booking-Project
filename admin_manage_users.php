<?php
session_start();

// Include database connection file
include_once('./function.php');
include_once('./menu_admin.php');

$objCon = connectDB(); // เชื่อมต่อฐานข้อมูล

// Check if user is logged in as admin
if (!isset($_SESSION['user_status']) || $_SESSION['user_status'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Check if user wants to view only users or admins
$user_status = isset($_GET['user_status']) ? $_GET['user_status'] : '';

// Determine the current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Number of records per page
$offset = ($page - 1) * $limit; // Calculate offset for SQL query

// Check if search term is set
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Construct the SQL query with search and pagination
$whereClause = "";
if ($user_status === 'Admin') {
    $whereClause .= "WHERE user_status = 'Admin' ";
} elseif ($user_status === 'User') {
    $whereClause .= "WHERE user_status = 'User' ";
}

if (!empty($search)) {
    $whereClause .= (!empty($whereClause) ? "AND " : "WHERE ") . "(user_username LIKE '%$search%' OR user_department LIKE '%$search%')";
}

$strSQL = "SELECT * FROM users $whereClause LIMIT $limit OFFSET $offset";
$result = $objCon->query($strSQL);

// Get total number of users for pagination
$totalSQL = "SELECT COUNT(*) as total FROM users $whereClause";
$totalResult = $objCon->query($totalSQL);
$totalRow = $totalResult->fetch_assoc();
$totalUsers = $totalRow['total'];
$totalPages = ceil($totalUsers / $limit);

// Display user data in a table
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/admin_manage_users.css">
    <title>จัดการข้อมูลผู้ใช้งาน</title>
</head>
<body>
    <h1>จัดการข้อมูลผู้ใช้งาน</h1>

    <div class="container">
        
    <div class="menu-and-search">
        <div class="menu-links">
            <a href="admin_manage_users.php" id="showAllUsers" class="menu-link">All Users</a> |
            <a href="admin_manage_users.php?user_status=Admin" class="menu-link">Admins</a> |
            <a href="admin_manage_users.php?user_status=User" class="menu-link">Users</a>  
        </div>
        <form method="GET" action="admin_manage_users.php" class="search-form">
            <input type="hidden" name="user_status" value="<?php echo $user_status; ?>">
            <input type="text" name="search" placeholder="ค้นหา..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">ค้นหา</button>
        </form>
    </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>แผนก</th>
                <th>สถานะ</th>
                <th>ดำเนินการ</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['user_username']; ?></td>
                    <td><?php echo $row['user_department']; ?></td>
                    <td><?php echo $row['user_status']; ?></td>
                    <td>
                        <a class="edit" href="#" onclick="openEditUserPopup('<?php echo $row['user_id']; ?>', '<?php echo $row['user_username']; ?>', '<?php echo $row['user_password']; ?>', '<?php echo $row['user_department']; ?>', '<?php echo $row['user_status']; ?>')">แก้ไข</a>
                        <a class="delete" href="delete_user.php?id=<?php echo $row['user_id']; ?>">ลบ</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&user_status=<?php echo $user_status; ?>&search=<?php echo htmlspecialchars($search); ?>" class="previous">Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&user_status=<?php echo $user_status; ?>&search=<?php echo htmlspecialchars($search); ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&user_status=<?php echo $user_status; ?>&search=<?php echo htmlspecialchars($search); ?>" class="next">Next</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Popup for editing a user -->
    <div id="editUserPopup" class="popup" style="display: none;">
    <h2>แก้ไขข้อมูลผู้ใช้งาน</h2>
    <form id="editUserForm" method="post" action="update_admin_user.php">
        <input type="hidden" id="editUserId" name="user_id" value="">
        <label for="editUsername">Username:</label>
        <input type="text" id="editUsername" name="user_username"><br><br>
        <label for="editPassword">New Password:</label>
        <input type="password" id="editPassword" name="user_password"><br><br>
        <label for="editDepartment">แผนก:</label>
        <input type="text" id="editDepartment" name="user_department"><br><br>
        <label for="editLevel">สถานะ:</label>
        <select id="editLevel" name="user_status">
            <option value="Admin">Admin</option>
            <option value="User">User</option>
        </select><br><br>
        <input type="submit" value="บันทึก">
        <button type="button" onclick="closeEditUserPopup()">ยกเลิก</button>
    </form>
</div>

    <!-- Popup for adding a user -->
    <div id="addUserPopup" class="popup" style="display: none;">
        <h2>เพิ่มผู้ใช้งาน</h2>
        <form method="post" action="add_user.php">
            <label for="user_username">Username:</label>
            <input type="text" id="user_username" name="user_username"><br><br>
            <label for="user_password">Password:</label>
            <input type="password" id="user_password" name="user_password"><br><br>
            <label for="user_department">แผนก:</label>
            <input type="text" id="user_department" name="user_department"><br><br>
            <label for="user_status">สถานะ:</label>
            <select id="user_status" name="user_status">
                <option value="Admin">Admin</option>
                <option value="User">User</option>
            </select><br><br>
            <input type="submit" value="เพิ่ม">
            <button type="button" onclick="closeAddUserPopup()">ยกเลิก</button>
        </form>
    </div>

    <div class="overlay" id="overlay" style="display: none;"></div>
    <div class="add-user-container">
        <a class="add" href="#" onclick="openAddUserPopup()">เพิ่มผู้ใช้งาน</a>
    </div>

    <script>
        function openEditUserPopup(userId, username, password, department, status) {
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUsername').value = username;
            document.getElementById('editPassword').value = password;
            document.getElementById('editDepartment').value = department;
            document.getElementById('editLevel').value = status;
            document.getElementById('editUserPopup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeEditUserPopup() {
            document.getElementById('editUserPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        function openAddUserPopup() {
            document.getElementById('addUserPopup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function closeAddUserPopup() {
            document.getElementById('addUserPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>
</body>
</html>
