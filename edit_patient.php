<?php
include("../config/connect.php");

// Get patient ID from URL
if(!isset($_GET['id'])){
    die("Patient ID not specified.");
}

$id = intval($_GET['id']);

// Fetch existing patient data
$result = mysqli_query($conn, "SELECT * FROM patients WHERE id=$id");
if(!$result || mysqli_num_rows($result) == 0){
    die("Patient not found.");
}
$patient = mysqli_fetch_assoc($result);

// Handle form submission
if(isset($_POST['update_patient'])){
    $admitted_by = mysqli_real_escape_string($conn, $_POST['admitted_by']);
    $patient_name = mysqli_real_escape_string($conn, $_POST['patient_name']);
    $admission_date = mysqli_real_escape_string($conn, $_POST['admission_date']);
    $discharge_date = mysqli_real_escape_string($conn, $_POST['discharge_date']);
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);

    $sql_update = "UPDATE patients SET 
                    admitted_by='$admitted_by',
                    patient_name='$patient_name',
                    admission_date='$admission_date',
                    discharge_date=".($discharge_date ? "'$discharge_date'" : "NULL").",
                    diagnosis='$diagnosis'
                   WHERE id=$id";

    if(mysqli_query($conn, $sql_update)){
        header("Location: patients.php"); // redirect back to patients page
        exit;
    } else {
        die("Error updating patient: " . mysqli_error($conn));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Patient</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f6f8; color: #333; }
        h2 { color: #0b3d91; }
        form input, form select, form button { padding: 8px; margin: 5px 0; width: 250px; }
        form button { width: auto; background-color: #0b3d91; color: white; border: none; cursor: pointer; padding: 8px 15px; }
        form button:hover { background-color: #062964; }
    </style>
</head>
<body>
    <h2>Edit Patient</h2>
    <form method="POST" action="">
        Nurse Admitting:
        <select name="admitted_by" required>
            <?php 
            $nurses = mysqli_query($conn, "SELECT nurse_number FROM nurses");
            while($n = mysqli_fetch_assoc($nurses)){
                $selected = ($n['nurse_number'] == $patient['admitted_by']) ? "selected" : "";
                echo "<option value='".$n['nurse_number']."' $selected>".$n['nurse_number']."</option>";
            }
            ?>
        </select><br>
        Patient Name: <input type="text" name="patient_name" value="<?php echo htmlspecialchars($patient['patient_name']); ?>" required><br>
        Admission Date: <input type="date" name="admission_date" value="<?php echo $patient['admission_date']; ?>" required><br>
        Discharge Date: <input type="date" name="discharge_date" value="<?php echo $patient['discharge_date']; ?>"><br>
        Diagnosis: <input type="text" name="diagnosis" value="<?php echo htmlspecialchars($patient['diagnosis']); ?>"><br><br>

        <button type="submit" name="update_patient">Update Patient</button>
        <a href="patients.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>
