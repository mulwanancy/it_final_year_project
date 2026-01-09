<?php
session_start();
include("../config/connect.php");

$role = $_SESSION['role'] ?? 'nurse'; // default to nurse

// Handle Search
$search_query = "";
if(isset($_GET['search'])){
    $search_query = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM attendance 
            WHERE nurse_number LIKE '%$search_query%' 
            OR shift_name LIKE '%$search_query%' 
            OR status LIKE '%$search_query%'
            ORDER BY date DESC";
} else {
    $sql = "SELECT * FROM attendance ORDER BY date DESC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Attendance - NurseShift Payroll</title>
<style>
body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f9; color: #333; }

/* Navbar like dashboard */
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

/* Table styling */
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
table, th, td { border: 1px solid #0b3d91; }
th, td { padding: 10px; text-align: left; }
th { background-color: #0b3d91; color: white; }
tr:nth-child(even) { background-color: #e6f2ff; }
tr:hover { background-color: #cce0ff; }
.actions a { margin-right: 8px; text-decoration: none; font-weight: bold; }
.actions a.edit { color: #006600; }
.actions a.delete { color: #cc0000; }

/* Search bar */
.search-bar { margin: 15px 0; }
.search-bar input { width: 300px; padding: 8px; }
.search-bar button { padding: 8px 15px; background-color: #0b3d91; color: white; border: none; cursor: pointer; }
.search-bar button:hover { background-color: #062964; }
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

<h2>Attendance Records</h2>

<div class="search-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by Nurse, Shift or Status" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<table>
<tr>
    <th>ID</th>
    <th>Nurse Number</th>
    <th>Date</th>
    <th>Time In</th>
    <th>Time Out</th>
    <th>Shift</th>
    <th>Status</th>
    <?php if($role=='admin'){ echo '<th>Actions</th>'; } ?>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['nurse_number']); ?></td>
    <td><?php echo htmlspecialchars($row['date']); ?></td>
    <td><?php echo htmlspecialchars($row['time_in']); ?></td>
    <td><?php echo htmlspecialchars($row['time_out']); ?></td>
    <td><?php echo htmlspecialchars($row['shift_name'] ?? 'N/A'); ?></td>
    <td><?php echo htmlspecialchars($row['status']); ?></td>
    <?php if($role=='admin'){ ?>
    <td class="actions">
        <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this record?')">Delete</a>
    </td>
    <?php } ?>
</tr>
<?php } ?>

</table>

</div>
</body>
</html>
