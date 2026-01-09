<?php
session_start();
include("../config/connect.php");

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user role from DB
$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM nurses WHERE id = '$user_id' LIMIT 1";
$res = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($res);
$role = $user['role'] ?? 'nurse';  // default to nurse if not found

// Handle Add Shift (Admin only)
if ($role == 'admin' && isset($_POST['add_shift'])) {
    $shift_name = mysqli_real_escape_string($conn, $_POST['shift_name']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    mysqli_query($conn, "INSERT INTO shifts (shift_name, start_time, end_time) VALUES ('$shift_name', '$start_time', '$end_time')");
}

// Handle Delete Shift (Admin only)
if ($role == 'admin' && isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM shifts WHERE id = $delete_id");
}

// Fetch all shifts
$result = mysqli_query($conn, "SELECT * FROM shifts ORDER BY shift_name");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shifts Management - NurseShift Payroll</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; color: #333; }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #0b3d91;
            padding: 20px 40px;
            min-height: 90px;
        }
        .navbar .logo-container { display: flex; align-items: center; }
        .navbar .logo-container img { height: 80px; margin-right: 10px; }
        .navbar .logo-text { display: flex; flex-direction: column; }
        .navbar .logo { font-size: 24px; font-weight: bold; color: #ffcc00; }
        .navbar .tagline { font-size: 14px; font-style: italic; color: #ffcc00; margin-top: 2px; }
        .navbar ul { list-style: none; display: flex; gap: 25px; margin: 0; padding: 0; }
        .navbar ul li a { color: #fff; text-decoration: none; font-size: 16px; }
        .navbar ul li a:hover { color: #ffcc00; }

        /* Container */
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; background: #e8e8e8; border-radius: 10px; }

        /* Form and table */
        form, table { background-color: #e6f0ff; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        input[type=text], input[type=time], button { padding: 8px; margin: 5px 0; border-radius: 3px; width: 100%; }
        button { background-color: #0b3d91; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #064080; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #999; padding: 10px; text-align: left; }
        a { color: #064080; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo-container">
        <img src="http://localhost/nurseshift_payroll/public/images/logo.jpg" alt="NurseShift Logo">
        <div class="logo-text">
            <div class="logo">NurseShift Payroll</div>
            <div class="tagline">Empowering maternity healthcare teams with seamless management.</div>
        </div>
    </div>
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="handover_reports.php">Handover Reports</a></li>
        <li><a href="patients.php">Patient List</a></li>
        <li><a href="attendance.php">Attendance</a></li>
        <li><a href="shifts.php">Shifts</a></li>
        <?php if($role === 'admin') { echo '<li><a href="switch_role.php">Switch Role</a></li>'; } ?>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="container">
<h2>Shifts Management</h2>

<!-- Admin Add Shift Form -->
<?php if ($role == 'admin'): ?>
    <h3>Add New Shift</h3>
    <form method="POST" action="">
        Shift Name: <input type="text" name="shift_name" required>
        Start Time: <input type="time" name="start_time" required>
        End Time: <input type="time" name="end_time" required>
        <button type="submit" name="add_shift">Add Shift</button>
    </form>
<?php endif; ?>

<h3>Existing Shifts</h3>
<table>
<tr>
    <th>ID</th>
    <th>Shift Name</th>
    <th>Start Time</th>
    <th>End Time</th>
    <?php if ($role == 'admin') echo "<th>Actions</th>"; ?>
</tr>
<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['shift_name']; ?></td>
    <td><?php echo $row['start_time']; ?></td>
    <td><?php echo $row['end_time']; ?></td>
    <?php if ($role == 'admin'): ?>
        <td>
            <a href="edit_shift.php?id=<?php echo $row['id']; ?>">Edit</a> |
            <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this shift?');">Delete</a>
        </td>
    <?php endif; ?>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
