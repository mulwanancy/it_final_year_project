<?php
session_start();
include '../config/connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM nurses WHERE id='$user_id' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

$role = $user['role'] ?? 'nurse';
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $update = "UPDATE nurses 
               SET first_name='$first_name', last_name='$last_name', contact='$contact', email='$email' 
               WHERE id='$user_id'";
    if (mysqli_query($conn, $update)) {
        $message = "✅ Profile updated successfully!";
        // Refresh user data
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
    } else {
        $message = "❌ Failed to update profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Update Profile - NurseShift Payroll</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    background:#f4f6f9; 
    margin:0; 
    padding:0; 
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
.navbar .logo-container { display: flex; align-items: center; }
.navbar .logo-container img { height: 80px; margin-right: 10px; }
.navbar .logo-text { display: flex; flex-direction: column; color: #ffcc00; }
.navbar .logo { font-size: 24px; font-weight: bold; }
.navbar .tagline { font-size: 14px; font-style: italic; margin-top: 2px; }
.navbar ul { list-style: none; display: flex; gap: 25px; margin: 0; padding: 0; }
.navbar ul li a { color: #fff; text-decoration: none; font-size: 16px; }
.navbar ul li a:hover { color: #ffcc00; }

/* Container */
.container { 
    max-width:800px; 
    margin: 20px auto; 
    padding: 20px; 
    background: #f2f2f2; /* faded light grey */
    border-radius: 10px; 
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Heading box */
.container h2 {
    background: #0b3d91;
    color: white;
    padding: 10px 15px;
    border-radius: 6px;
    font-size: 18px;
}

/* Table */
table { 
    width: 100%; 
    border-collapse: collapse; 
    margin-bottom: 20px; 
}
th, td { 
    padding: 12px; 
    text-align: left; 
    border-bottom: 1px solid #ccc; 
}
th { 
    color: #000; 
    font-weight: bold; 
    width: 35%; 
    background: #f9f9f9;
}
td { 
    color: #000; 
    font-weight: normal; 
    background: #fff;
}
tr:hover td { background: #b6861fff; }

/* Form */
form input, form button { 
    width: 100%; 
    padding: 10px; 
    margin: 8px 0; 
    border-radius: 5px; 
    border: 1px solid #ccc; 
}
form button { 
    background-color: #0b3d91; 
    color: white; 
    border: none; 
    cursor: pointer; 
    font-weight: bold;
}
form button:hover { background-color: #cea126ff; }

.success { color: green; text-align: center; font-weight: bold; }
.error { color: red; text-align: center; font-weight: bold; }

a.back-link { 
    display: inline-block; 
    margin-top: 15px; 
    text-align:center; 
    color: #0b3d91; 
    font-weight: bold; 
    text-decoration: none; 
}
a.back-link:hover { text-decoration: underline; }
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
        <li><a href="update_profile.php">Update Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="container">
    <h2>Your Current Information</h2>

    <table>
        <tr><th>Nurse Number</th><td><?php echo htmlspecialchars($user['nurse_number']); ?></td></tr>
        <tr><th>First Name</th><td><?php echo htmlspecialchars($user['first_name']); ?></td></tr>
        <tr><th>Last Name</th><td><?php echo htmlspecialchars($user['last_name']); ?></td></tr>
        <tr><th>Contact</th><td><?php echo htmlspecialchars($user['contact']); ?></td></tr>
        <tr><th>Email</th><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
        <tr><th>Hire Date</th><td><?php echo htmlspecialchars($user['hire_date']); ?></td></tr>
        <tr><th>Basic Salary</th><td><?php echo number_format($user['basic_salary'],2); ?></td></tr>
        <tr><th>Shift</th><td><?php echo htmlspecialchars($user['shift_name']); ?></td></tr>
        <tr><th>Status</th><td><?php echo htmlspecialchars($user['status']); ?></td></tr>
        <tr><th>Role</th><td><?php echo ucfirst($role); ?></td></tr>
    </table>

    <?php if($message) echo "<p class='success'>$message</p>"; ?>

    <h2>Edit Your Information</h2>
    <form method="POST">
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" placeholder="First Name" required>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" placeholder="Last Name" required>
        <input type="text" name="contact" value="<?php echo htmlspecialchars($user['contact']); ?>" placeholder="Contact" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" placeholder="Email" required>
        <button type="submit">Update Profile</button>
    </form>

    <p style="text-align:center;">
        <a href="dashboard.php" class="back-link">⬅ Go Back to Dashboard</a>
    </p>
</div>

</body>
</html>
