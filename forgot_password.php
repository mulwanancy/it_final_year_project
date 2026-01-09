<?php
// forgot_password.php
session_start();
include '../config/connect.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if email exists
    $sql = "SELECT * FROM nurses WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        // Normally you would send a reset link by email
        $message = "✅ A password reset link has been sent to $email (simulated).";
    } else {
        $message = "❌ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forgot Password - NurseShift Payroll</title>
<style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; }

    /* Navbar like dashboard */
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
    .navbar .logo-text { display: flex; flex-direction: column; }
    .navbar .logo { font-size: 24px; font-weight: bold; color: #ffcc00; }
    .navbar .tagline { font-size: 14px; font-style: italic; color: #ffcc00; margin-top: 2px; }
    .navbar ul { list-style: none; display: flex; gap: 25px; margin: 0; padding: 0; }
    .navbar ul li a { color: #fff; text-decoration: none; font-size: 16px; }
    .navbar ul li a:hover { color: #ffcc00; }

    /* Form container */
    .form-container {
        background: #fff;
        padding: 30px;
        max-width: 400px;
        margin: 50px auto;
        border-radius: 10px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        text-align: center;
    }

    input, button {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    button {
        background: #0b3d91;
        color: #fff;
        border: none;
        cursor: pointer;
    }
    button:hover { background: #092c6a; }

    .message { margin: 10px 0; color: green; }
    .error { margin: 10px 0; color: red; }

    p a { color: #0b3d91; text-decoration: none; }
    p a:hover { text-decoration: underline; }
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
        <li><a href="index.php">Login</a></li>
        <li><a href="forgot_password.php">Forgot Password</a></li>
    </ul>
</div>

<div class="form-container">
    <h2>Forgot Password</h2>

    <?php if ($message != "") { echo "<p class='message'>$message</p>"; } ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Send Reset Link</button>
    </form>

    <p><a href="index.php">Back to Login</a></p>
</div>

</body>
</html>
