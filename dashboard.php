<?php
session_start();
include '../config/connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM nurses WHERE id='$user_id' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Normalize role safely
$role = isset($user['role']) ? strtolower(trim($user['role'])) : 'nurse';
$_SESSION['role'] = $role;

// Fetch payroll for this nurse
$nurse_number = $user['nurse_number'];
$payroll_sql = "SELECT * FROM payroll WHERE nurse_number='$nurse_number' ORDER BY id DESC LIMIT 1";
$payroll_result = mysqli_query($conn, $payroll_sql);
$payroll = mysqli_fetch_assoc($payroll_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - NurseShift Management</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #000;
}

/* Navbar */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #0b3d91;
    padding: 20px 40px;
    min-height: 90px;
}

.navbar .logo-container {
    display: flex;
    align-items: center;
}

.navbar .logo-container img {
    height: 80px;
    margin-right: 10px;
}

.navbar .logo-text {
    display: flex;
    flex-direction: column;
}

.navbar .logo {
    font-size: 24px;
    font-weight: bold;
    color: #ffcc00;
}

.navbar .tagline {
    font-size: 14px;
    font-style: italic;
    color: #ffcc00;
    margin-top: 2px;
}

.navbar ul {
    list-style: none;
    display: flex;
    gap: 25px;
    margin: 0;
    padding: 0;
}

.navbar ul li a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
}

.navbar ul li a:hover {
    color: #ffcc00;
}

/* Greeting box */
.greeting-box {
    display: inline-block;
    padding: 10px 20px;
    margin: 20px 40px 10px 40px;
    background: #0b3d91;
    color: #fff;
    font-weight: bold;
    font-size: 18px;
    border-radius: 8px;
    max-width: fit-content;
}

/* Links Box - Vertical */
.links-box {
    max-width: 300px;
    margin: 10px 40px 30px 40px;
    padding: 20px;
    background-color: #e8e8e8;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    gap: 15px; /* space between links */
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

.links-box a {
    text-decoration: none;
    color: #0b3d91;
    font-weight: bold;
    font-size: 16px;
    padding: 12px 20px;
    background: #fff;
    border-radius: 5px;
    transition: all 0.3s;
    text-align: center;
}

.links-box a:hover {
    background-color: #db9d17b6;
    color: #fff;
}

/* Table styling */
.container {
    max-width: 900px;
    margin: 0 40px 40px 40px;
    padding: 25px;
    background: #e8e8e8;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

h2 {
    margin-top: 0;
    color: #0b3d91;
}

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
}

th, td {
    text-align: left;
    padding: 12px;
}

th {
    color: #000;
    font-weight: bold;
    width: 35%;
}

td {
    color: #000;
    font-weight: normal;
}

tr:nth-child(even) td {
    background: #dcdcdc;
}

tr:hover td {
    background: #db9d17b6;
}

/* Logout button */
.logout-btn {
    display: inline-block;
    padding: 12px 25px;
    background: #0b3d91;
    color: #fff;
    font-weight: bold;
    border-radius: 5px;
    text-decoration: none;
    transition: background 0.3s;
    margin-top: 15px;
}

.logout-btn:hover {
    background: #092c6a;
}
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

<!-- Greeting box -->
<div class="greeting-box">
    Hello, <?php echo htmlspecialchars($user['first_name'] . " " . $user['last_name']); ?>!
</div>

<!-- Links box (Vertical) -->
<div class="links-box">
    <a href="notices.php">📢 Notice to the staff</a>
    <a href="update_profile.php">📝 Update your personal information</a>
    <a href="payroll.php">💰 Download your payroll</a>
</div>

<!-- User Details -->
<div class="container">
    <h2>Your Details</h2>
    <table>
        <tr><th>Nurse Number</th><td><?php echo htmlspecialchars($user['nurse_number']); ?></td></tr>
        <tr><th>First Name</th><td><?php echo htmlspecialchars($user['first_name']); ?></td></tr>
        <tr><th>Last Name</th><td><?php echo htmlspecialchars($user['last_name']); ?></td></tr>
        <tr><th>Contact</th><td><?php echo htmlspecialchars($user['contact']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
        <tr><th>Hire Date</th><td><?php echo htmlspecialchars($user['hire_date']); ?></td></tr>
        <tr><th>Basic Salary</th><td><?php echo number_format($user['basic_salary'], 2); ?></td></tr>
        <tr><th>Shift</th><td><?php echo htmlspecialchars($user['shift_name']); ?></td></tr> 
        <tr><th>Status</th><td><?php echo htmlspecialchars($user['status']); ?></td></tr>
        <tr><th>Role</th><td><?php echo ucfirst($role); ?></td></tr>
    </table>

    <!-- Payroll Details -->
    <h2>Your Payroll</h2>
    <?php if ($payroll): ?>
        <table>
            <tr><th>Month</th><td><?php echo htmlspecialchars($payroll['month']); ?></td></tr>
            <tr><th>Basic Salary</th><td><?php echo number_format($payroll['basic_salary'], 2); ?></td></tr>
            <tr><th>Allowances</th><td><?php echo number_format($payroll['allowances'], 2); ?></td></tr>
            <tr><th>Deductions</th><td><?php echo number_format($payroll['deductions'], 2); ?></td></tr>
            <tr><th>Total Salary</th><td><?php echo number_format($payroll['total_salary'], 2); ?></td></tr>
        </table>
    <?php else: ?>
        <p>No payroll details found.</p>
    <?php endif; ?>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
