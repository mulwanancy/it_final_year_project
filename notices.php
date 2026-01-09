<?php
session_start();
include '../config/connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user role from DB
$user_id = $_SESSION['user_id'];
$sql = "SELECT role FROM nurses WHERE id = '$user_id' LIMIT 1";
$res = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($res);
$role = $user['role'] ?? 'nurse';  // default to nurse if not found

// Handle Add Notice (Admin only)
if ($role == 'admin' && isset($_POST['add_notice'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $date_posted = date('Y-m-d');

    mysqli_query($conn, "INSERT INTO notices (title, message, date_posted) VALUES ('$title', '$message', '$date_posted')");
}

// Handle Delete Notice (Admin only)
if ($role == 'admin' && isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM notices WHERE id = $delete_id");
}

// Fetch all notices
$result = mysqli_query($conn, "SELECT * FROM notices ORDER BY date_posted DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Staff Notices - NurseShift Payroll</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin:0; padding:0; }
        /* Navbar same as homepage */
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

        .container { max-width: 800px; margin: 20px auto; padding: 20px; background: #e8e8e8; border-radius: 10px; }
        form input, form textarea, form button { width: 100%; padding: 10px; margin: 5px 0; border-radius: 5px; border: 1px solid #ccc; }
        form button { background-color: #0b3d91; color: white; border: none; cursor: pointer; }
        form button:hover { background-color: #062964; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #0b3d91; padding: 10px; text-align: left; }
        th { background-color: #0b3d91; color: white; }
        tr:nth-child(even) { background-color: #e6f2ff; }
        tr:hover { background-color: #cce0ff; }
        .actions a { margin-right: 8px; text-decoration: none; font-weight: bold; color: #cc0000; }
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
        <li><a href="notice.php">Staff Notices</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>

<div class="container">
    <h2>Staff Notices</h2>

    <!-- Admin Add Notice Form -->
    <?php if ($role == 'admin'): ?>
        <form method="POST" action="">
            <input type="text" name="title" placeholder="Notice Title" required>
            <textarea name="message" placeholder="Notice Message" rows="4" required></textarea>
            <button type="submit" name="add_notice">Post Notice</button>
        </form>
    <?php endif; ?>

    <!-- Display all notices -->
    <table>
        <tr>
            <th>Title</th>
            <th>Message</th>
            <th>Date Posted</th>
            <?php if ($role == 'admin') echo "<th>Actions</th>"; ?>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['message']; ?></td>
            <td><?php echo $row['date_posted']; ?></td>
            <?php if ($role == 'admin'): ?>
                <td>
                    <a href="?delete_id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this notice?');">Delete</a>
                </td>
            <?php endif; ?>
        </tr>
        <?php } ?>
    </table>
</div>

</body>
</html>
