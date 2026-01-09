<?php
session_start();
include("../config/connect.php");

$role = $_SESSION['role'] ?? 'nurse'; // default to nurse

// Handle Add Patient
if(isset($_POST['add_patient'])){
    $admitted_by = mysqli_real_escape_string($conn, $_POST['admitted_by']);
    $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $admission_date = mysqli_real_escape_string($conn, $_POST['admission_date']);
    $discharge_date = mysqli_real_escape_string($conn, $_POST['discharge_date']);
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);

    mysqli_query($conn, "INSERT INTO patients (admitted_by, patient_name, admission_date, discharge_date, diagnosis) 
                         VALUES ('$admitted_by', '$patient_name', '$admission_date', ".($discharge_date ? "'$discharge_date'" : "NULL").", '$diagnosis')");
}

// Handle Delete Patient (admin only)
if($role=='admin' && isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM patients WHERE id=$id");
}

// Handle Search
$search_query = "";
if(isset($_GET['search'])){
    $search_query = $conn->real_escape_string($_GET['search']);
    $result = mysqli_query($conn, "SELECT * FROM patients WHERE patient_name LIKE '%$search_query%' OR admitted_by LIKE '%$search_query%' OR diagnosis LIKE '%$search_query%' ORDER BY admission_date DESC");
} else {
    $result = mysqli_query($conn, "SELECT * FROM patients ORDER BY admission_date DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Patients Management - NurseShift Payroll</title>
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

/* Container */
.container {
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #e8e8e8;
    border-radius: 10px;
}

/* Form styling */
form input, form select, form textarea, form button {
    padding: 8px;
    margin: 5px 0;
    width: 250px;
}
form textarea { width: 250px; height: 60px; }
form button {
    width: auto;
    background-color: #0b3d91;
    color: white;
    border: none;
    cursor: pointer;
    padding: 8px 15px;
}
form button:hover { background-color: #062964; }

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

<h2>Admit New Patient</h2>
<form method="POST" action="">
    Nurse Admitting:
    <select name="admitted_by" required>
        <?php 
        $nurses = mysqli_query($conn, "SELECT nurse_number FROM nurses");
        while($n = mysqli_fetch_assoc($nurses)){
            echo "<option value='".$n['nurse_number']."'>".$n['nurse_number']."</option>";
        }
        ?>
    </select><br>
    Patient Name: <input type="text" name="patient_name" required><br>
    Admission Date: <input type="date" name="admission_date" required><br>
    Discharge Date: <input type="date" name="discharge_date"><br>
    Diagnosis: <input type="text" name="diagnosis"><br>
    <button type="submit" name="add_patient">Admit Patient</button>
</form>

<div class="search-bar">
    <form method="GET">
        <input type="text" name="search" placeholder="Search by Nurse, Patient or Diagnosis" value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>
</div>

<h2>Existing Patients</h2>
<table>
<tr>
    <th>ID</th>
    <th>Admitted By</th>
    <th>Patient Name</th>
    <th>Admission Date</th>
    <th>Discharge Date</th>
    <th>Diagnosis</th>
    <th>Actions</th>
</tr>
<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['admitted_by']; ?></td>
    <td><?php echo $row['patient_name']; ?></td>
    <td><?php echo $row['admission_date']; ?></td>
    <td><?php echo $row['discharge_date']; ?></td>
    <td><?php echo $row['diagnosis']; ?></td>
    <td class="actions">
        <a class="edit" href="edit_patient.php?id=<?php echo $row['id']; ?>">Edit</a>
        <?php if($role=='admin'): ?>
            <a class="delete" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this patient?')">Delete</a>
        <?php endif; ?>
    </td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
