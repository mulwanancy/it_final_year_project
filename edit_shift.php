<?php
include("../config/connect.php");

// Get the shift ID from the URL
if(!isset($_GET['id'])){
    echo "Shift ID not specified!";
    exit;
}

$id = intval($_GET['id']);

// Fetch existing shift data
$stmt = $conn->prepare("SELECT * FROM shifts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo "Shift not found!";
    exit;
}

$shift = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if(isset($_POST['save_shift'])){
    $shift_name = mysqli_real_escape_string($conn, $_POST['shift_name']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);

    $stmt = $conn->prepare("UPDATE shifts SET shift_name=?, start_time=?, end_time=? WHERE id=?");
    $stmt->bind_param("sssi", $shift_name, $start_time, $end_time, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: shifts.php"); // Redirect back to shifts list
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Shift</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f2f2f2; color: #333; padding: 20px; }
        h2 { color: #0B3D91; }
        form { background-color: #e6f0ff; border-radius: 5px; padding: 15px; width: 400px; }
        input[type=text], input[type=time], button { padding: 8px; margin: 5px 0; border-radius: 3px; width: 100%; }
        button { background-color: #0B3D91; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #064080; }
        a { color: #064080; text-decoration: none; }
    </style>
</head>
<body>

<h2>Edit Shift</h2>
<form method="POST" action="">
    Shift Name: <input type="text" name="shift_name" value="<?php echo htmlspecialchars($shift['shift_name']); ?>" required>
    Start Time: <input type="time" name="start_time" value="<?php echo $shift['start_time']; ?>" required>
    End Time: <input type="time" name="end_time" value="<?php echo $shift['end_time']; ?>" required>
    <button type="submit" name="save_shift">Save Changes</button>
</form>

<p><a href="shifts.php">Back to Shifts List</a></p>

</body>
</html>
