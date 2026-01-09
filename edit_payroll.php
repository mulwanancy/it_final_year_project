<?php
include("../config/connect.php");

if(!isset($_GET['id'])) { die("No record ID provided"); }

$id = intval($_GET['id']);

// Fetch existing payroll
$res = mysqli_query($conn, "SELECT * FROM payroll WHERE id=$id");
if(!$res || mysqli_num_rows($res)==0) { die("Record not found"); }
$payroll = mysqli_fetch_assoc($res);

// Handle edit submission
if(isset($_POST['edit_payroll'])) {
    $basic_salary = $_POST['basic_salary'];
    $allowances = $_POST['allowances'];
    $deductions = $_POST['deductions'];

    mysqli_query($conn, "UPDATE payroll SET basic_salary='$basic_salary', allowances='$allowances', deductions='$deductions' WHERE id=$id");
    header("Location: payroll.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Payroll</title>
</head>
<body>
    <h2>Edit Payroll</h2>
    <form method="POST">
        Nurse Number: <?php echo $payroll['nurse_number']; ?><br><br>
        Basic Salary: <input type="number" name="basic_salary" value="<?php echo $payroll['basic_salary']; ?>" required><br>
        Allowances: <input type="number" name="allowances" value="<?php echo $payroll['allowances']; ?>"><br>
        Deductions: <input type="number" name="deductions" value="<?php echo $payroll['deductions']; ?>"><br>
        <button type="submit" name="edit_payroll">Save Changes</button>
    </form>
</body>
</html>
