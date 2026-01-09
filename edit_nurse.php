<?php
include("../config/connect.php");

// Get nurse ID
if(!isset($_GET['id'])){
    die("No nurse ID provided.");
}
$id = $_GET['id'];

// Fetch nurse info
$stmt = $conn->prepare("SELECT * FROM nurses WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$nurse = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if(isset($_POST['update_nurse'])){
    $nurse_number = $_POST['nurse_number'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $hire_date = $_POST['hire_date'];
    $basic_salary = $_POST['basic_salary'];
    $status = $_POST['status'];
    $department = $_POST['department'];

    $stmt = $conn->prepare("UPDATE nurses SET nurse_number=?, first_name=?, last_name=?, contact=?, email=?, hire_date=?, basic_salary=?, status=?, department=? WHERE id=?");
    $stmt->bind_param("ssssssdssi", $nurse_number, $first_name, $last_name, $contact, $email, $hire_date, $basic_salary, $status, $department, $id);
    if($stmt->execute()){
        $message = "Nurse updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
    // Refresh data
    $stmt = $conn->prepare("SELECT * FROM nurses WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $nurse = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Nurse</title>
    <style>
        body { font-family: Arial; background-color: #f2f2f2; padding: 20px; }
        input, select, button { padding: 8px; margin: 5px 0; width: 200px; }
        button { background-color: #003366; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #001f4d; }
        .message { color: green; }
    </style>
</head>
<body>
    <h2>Edit Nurse</h2>
    <?php if(isset($message)) echo "<p class='message'>$message</p>"; ?>
    <form method="POST" action="">
        <input type="text" name="nurse_number" placeholder="Nurse Number" value="<?php echo $nurse['nurse_number']; ?>" required><br>
        <input type="text" name="first_name" placeholder="First Name" value="<?php echo $nurse['first_name']; ?>" required><br>
        <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $nurse['last_name']; ?>" required><br>
        <input type="text" name="contact" placeholder="Contact" value="<?php echo $nurse['contact']; ?>"><br>
        <input type="email" name="email" placeholder="Email" value="<?php echo $nurse['email']; ?>"><br>
        <input type="date" name="hire_date" value="<?php echo $nurse['hire_date']; ?>"><br>
        <input type="number" name="basic_salary" value="<?php echo $nurse['basic_salary']; ?>"><br>
        <select name="status">
            <option value="Active" <?php if($nurse['status']=="Active") echo "selected"; ?>>Active</option>
            <option value="On Leave" <?php if($nurse['status']=="On Leave") echo "selected"; ?>>On Leave</option>
            <option value="Resigned" <?php if($nurse['status']=="Resigned") echo "selected"; ?>>Resigned</option>
        </select><br>
        <input type="text" name="department" placeholder="Department" value="<?php echo $nurse['department']; ?>"><br>
        <button type="submit" name="update_nurse">Update Nurse</button>
    </form>
    <p><a href="nurses.php">Back to Nurses List</a></p>
</body>
</html>
