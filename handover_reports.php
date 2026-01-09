<?php
session_start();
include("../config/connect.php");

$role = $_SESSION['role'] ?? 'nurse'; // default to nurse

// Handle Add Report
if(isset($_POST['add_report'])){
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);
    $nurse_number = mysqli_real_escape_string($conn, $_POST['nurse_number']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $medication = mysqli_real_escape_string($conn, $_POST['medication']);
    $dose_given = mysqli_real_escape_string($conn, $_POST['dose_given']);
    $next_dose_time = mysqli_real_escape_string($conn, $_POST['next_dose_time']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $report_date = mysqli_real_escape_string($conn, $_POST['report_date']);
    $report_time = mysqli_real_escape_string($conn, $_POST['report_time']);

    $sql_insert = "INSERT INTO handover_reports 
        (patient_id, nurse_number, `condition`, medication, dose_given, next_dose_time, notes, report_date, report_time)
        VALUES ('$patient_id', '$nurse_number', '$condition', '$medication', '$dose_given', '$next_dose_time', '$notes', '$report_date', '$report_time')";
    
    mysqli_query($conn, $sql_insert);
}

// Handle Delete (admin only)
if($role=='admin' && isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM handover_reports WHERE id=$id");
}

// Handle Search
$search_query = "";
$where_clause = "";
if(isset($_GET['search']) && !empty($_GET['search'])){
    $search_query = $conn->real_escape_string($_GET['search']);
    $where_clause = "WHERE h.nurse_number LIKE '%$search_query%' OR p.patient_name LIKE '%$search_query%' OR h.condition LIKE '%$search_query%' OR h.medication LIKE '%$search_query%' OR h.notes LIKE '%$search_query%'";
}

// Fetch Reports
$sql_fetch = "SELECT h.*, p.patient_name FROM handover_reports h
              JOIN patients p ON h.patient_id = p.id
              $where_clause
              ORDER BY report_date DESC, report_time DESC";
$result = mysqli_query($conn, $sql_fetch);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Handover Reports - NurseShift Payroll</title>
<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    color: #333;
}

/* Navbar same as dashboard */
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

/* Form styling */
form input, form select, form textarea, form button {
    padding: 8px;
    margin: 5px 0;
    width: 200px;
}
form textarea {
    width: 300px;
    height: 60px;
}
form button {
    width: auto;
    background-color: #0b3d91;
    color: white;
    border: none;
    cursor: pointer;
    padding: 8px 15px;
}
form button:hover {
    background-color:  #0b3d91 ;
}

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

/* Container */
.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #e8e8e8;
    border-radius: 10px;
}
</style>
</head>
<body>

<!-- Navbar like dashboard -->
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

<h2>Search Reports</h2>
<form method="GET">
    <input type="text" name="search" placeholder="Search by Nurse, Patient, Condition, Medication, Notes" value="<?php echo htmlspecialchars($search_query); ?>">
    <button type="submit">Search</button>
</form>

<h2>Add Handover Report</h2>
<form method="POST" action="">
    Nurse: 
    <select name="nurse_number" required>
        <?php 
        $nurses = mysqli_query($conn, "SELECT nurse_number FROM nurses");
        while($n = mysqli_fetch_assoc($nurses)){
            echo "<option value='".$n['nurse_number']."'>".$n['nurse_number']."</option>";
        }
        ?>
    </select><br>

    Patient: 
    <select name="patient_id" required>
        <?php 
        $patients = mysqli_query($conn, "SELECT id, patient_name FROM patients");
        while($p = mysqli_fetch_assoc($patients)){
            echo "<option value='".$p['id']."'>".$p['patient_name']."</option>";
        }
        ?>
    </select><br>

    Condition: <input type="text" name="condition" required><br>
    Medication: <input type="text" name="medication"><br>
    Dose Given: <input type="text" name="dose_given"><br>
    Next Dose Time: <input type="time" name="next_dose_time"><br>
    Notes: <textarea name="notes"></textarea><br>
    Report Date: <input type="date" name="report_date" required><br>
    Report Time: <input type="time" name="report_time" required><br>
    <button type="submit" name="add_report">Add Report</button>
</form>

<h2>Existing Handover Reports</h2>
<table>
<tr>
    <th>ID</th>
    <th>Nurse</th>
    <th>Patient</th>
    <th>Condition</th>
    <th>Medication</th>
    <th>Dose Given</th>
    <th>Next Dose Time</th>
    <th>Notes</th>
    <th>Date</th>
    <th>Time</th>
    <th>Actions</th>
</tr>
<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo htmlspecialchars($row['id']); ?></td>
    <td><?php echo htmlspecialchars($row['nurse_number']); ?></td>
    <td><?php echo htmlspecialchars($row['patient_name']); ?></td>
    <td><?php echo htmlspecialchars($row['condition']); ?></td>
    <td><?php echo htmlspecialchars($row['medication']); ?></td>
    <td><?php echo htmlspecialchars($row['dose_given']); ?></td>
    <td><?php echo htmlspecialchars($row['next_dose_time']); ?></td>
    <td><?php echo htmlspecialchars($row['notes']); ?></td>
    <td><?php echo htmlspecialchars($row['report_date']); ?></td>
    <td><?php echo htmlspecialchars($row['report_time']); ?></td>
    <td class="actions">
        <a class="edit" href="edit_handover_reports.php?id=<?php echo $row['id']; ?>">Edit</a>
        <?php if($role=='admin'): ?>
            <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this report?')">Delete</a>
        <?php endif; ?>
    </td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
