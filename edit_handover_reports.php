<?php
session_start();
include("../config/connect.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Get user role silently (used for permissions but NOT displayed)
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'nurse';

// Check if ID is provided
if(!isset($_GET['id'])){
    die("No report ID provided.");
}

$id = intval($_GET['id']);

// Fetch the report details
$stmt = $conn->prepare("SELECT * FROM handover_reports WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    die("Report not found.");
}

$report = $result->fetch_assoc();
$stmt->close();

// Handle form submission for update
if(isset($_POST['update_report'])){
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);
    $nurse_number = mysqli_real_escape_string($conn, $_POST['nurse_number']);
    $condition = mysqli_real_escape_string($conn, $_POST['condition']);
    $medication = mysqli_real_escape_string($conn, $_POST['medication']);
    $dose_given = mysqli_real_escape_string($conn, $_POST['dose_given']);
    $next_dose_time = mysqli_real_escape_string($conn, $_POST['next_dose_time']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);
    $report_date = mysqli_real_escape_string($conn, $_POST['report_date']);
    $report_time = mysqli_real_escape_string($conn, $_POST['report_time']);

    $stmt = $conn->prepare("UPDATE handover_reports SET patient_id=?, nurse_number=?, `condition`=?, medication=?, dose_given=?, next_dose_time=?, notes=?, report_date=?, report_time=? WHERE id=?");
    $stmt->bind_param("issssssssi", $patient_id, $nurse_number, $condition, $medication, $dose_given, $next_dose_time, $notes, $report_date, $report_time, $id);

    if($stmt->execute()){
        header("Location: handover_reports.php");
        exit();
    } else {
        die("Error updating report: " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Handover Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f8; color: #333; }
        h2 { color: #0b3d91; }
        form input, form select, form textarea, form button { padding: 8px; margin: 5px 0; width: 300px; }
        form button { background-color: #0b3d91; color: white; border: none; cursor: pointer; padding: 8px 15px; }
        form button:hover { background-color: #062964; }
    </style>
</head>
<body>
    <h2>Edit Handover Report</h2>
    <form method="POST" action="">
        Nurse: 
        <select name="nurse_number" required>
            <?php 
            $nurses = mysqli_query($conn, "SELECT nurse_number FROM nurses");
            while($n = mysqli_fetch_assoc($nurses)){
                $selected = ($n['nurse_number'] == $report['nurse_number']) ? "selected" : "";
                echo "<option value='".$n['nurse_number']."' $selected>".$n['nurse_number']."</option>";
            }
            ?>
        </select><br><br>

        Patient: 
        <select name="patient_id" required>
            <?php 
            $patients = mysqli_query($conn, "SELECT id, patient_name FROM patients");
            while($p = mysqli_fetch_assoc($patients)){
                $selected = ($p['id'] == $report['patient_id']) ? "selected" : "";
                echo "<option value='".$p['id']."' $selected>".$p['patient_name']."</option>";
            }
            ?>
        </select><br><br>

        Condition: <input type="text" name="condition" value="<?php echo htmlspecialchars($report['condition']); ?>" required><br><br>
        Medication: <input type="text" name="medication" value="<?php echo htmlspecialchars($report['medication']); ?>"><br><br>
        Dose Given: <input type="text" name="dose_given" value="<?php echo htmlspecialchars($report['dose_given']); ?>"><br><br>
        Next Dose Time: <input type="time" name="next_dose_time" value="<?php echo htmlspecialchars($report['next_dose_time']); ?>"><br><br>
        Notes: <textarea name="notes"><?php echo htmlspecialchars($report['notes']); ?></textarea><br><br>
        Report Date: <input type="date" name="report_date" value="<?php echo htmlspecialchars($report['report_date']); ?>" required><br><br>
        Report Time: <input type="time" name="report_time" value="<?php echo htmlspecialchars($report['report_time']); ?>" required><br><br>

        <button type="submit" name="update_report">Update Report</button>
    </form>
</body>
</html>
