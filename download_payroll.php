<?php
session_start();
include("../config/connect.php");

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=payroll.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID','Nurse Number','Basic Salary','Allowances','Deductions','Net Pay']);

$role = $_SESSION['role'] ?? 'nurse';

if($role == 'nurse'){
    $nurse_number = '1'; // replace with session nurse number
    $result = mysqli_query($conn, "SELECT * FROM payroll WHERE nurse_number='$nurse_number'");
}else{
    $result = mysqli_query($conn, "SELECT * FROM payroll");
}

while($row = mysqli_fetch_assoc($result)){
    fputcsv($output, [
        $row['id'],
        $row['nurse_number'],
        $row['basic_salary'],
        $row['allowances'],
        $row['deductions'],
        $row['basic_salary'] + $row['allowances'] - $row['deductions']
    ]);
}
fclose($output);
exit;
?>
