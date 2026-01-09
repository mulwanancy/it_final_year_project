<?php
session_start();
include '../config/connect.php'; // adjust path if needed

$error = '';

// ✅ NEW: Check if redirected after registration
$success = '';
if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
    $success = "✅ Registration successful! Please log in below.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Find nurse by email
    $sql = "SELECT * FROM nurses WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['first_name'] . " " . $row['last_name'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "❌ Wrong password.";
        }
    } else {
        $error = "❌ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>NurseShift Payroll</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: url('public/images/hero.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #000;
    }

    /* Navbar */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #0b3d91;
        padding: 20px 40px;
        min-height: 50px;
    }
    .navbar .logo-container {
        display: flex;
        align-items: center;
    }
    .navbar .logo-container img {
        height: 90px;
        margin-right: 20px;
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
        transition: color 0.3s;
    }
    .navbar ul li a:hover {
        color: #ffcc00;
    }

    /* Login form */
    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: calc(100vh - 90px);
    }
    .login-box {
        background: #fff;
        padding: 40px;
        border-radius: 10px;
        width: 360px;
        text-align: center;
        color: #000;
    }
    .login-box h2 {
        margin-bottom: 20px;
        color: #0b3d91; /* Blue heading */
    }
    .login-box input[type="text"],
    .login-box input[type="password"],
    .login-box input[type="email"] {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 5px;
        outline: none;
    }
    .login-box button {
        width: 100%;
        padding: 10px;
        border: none;
        background: #0b3d91; /* Normal blue */
        color: #fff; /* White text */
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: background 0.3s;
    }
    .login-box button:hover {
        background: #08306b; /* Darker blue on hover */
    }
    .eye-toggle {
        position: absolute;
        right: 10px;
        top: 12px;
        cursor: pointer;
        color: #0b3d91;
    }
    .input-wrapper {
        position: relative;
    }
    .login-box p {
        margin: 10px 0;
    }
    .login-box a {
        color: #0b3d91;
        font-weight: bold;
        text-decoration: none;
    }
    .login-box a:hover {
        text-decoration: underline;
    }
    /* ✅ NEW: Success message style */
    .success-msg {
        color: green;
        font-weight: bold;
        margin-bottom: 15px;
    }
</style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo-container">
        <img src="http://localhost/nurseshift_payroll/public/images/logo.jpg" alt="NurseShift Logo">
        <div class="logo-text">
            <div class="logo">NurseShift Management</div>
            <div class="tagline">Empowering maternity healthcare teams with seamless management.</div>
        </div>
    </div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="faq.php">FAQ</a></li>
        <li><a href="reset.php">Reset Page</a></li>
    </ul>
</div>

<!-- Login Form -->
<div class="login-container">
    <div class="login-box">
        <h2>Please fill in the following details to login</h2>

        <!-- ✅ NEW: Show success after registration -->
        <?php if(!empty($success)) { echo "<p class='success-msg'>$success</p>"; } ?>

        <?php if(!empty($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

        <form action="" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <div class="input-wrapper">
                <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                <span id="toggleLoginPassword" class="eye-toggle">👁️</span>
            </div>
            <button type="submit">Login</button>
        </form>

        <p style="margin:10px 0;"><a href="forgot_password.php" style="color:red; text-decoration: underline;">Forgot password?</a></p>

        <p>Are you a new user or don't have an account? <a href="register.php">Click here to register</a></p>
    </div>
</div>

<script>
const toggleLoginPassword = document.querySelector('#toggleLoginPassword');
const loginPassword = document.querySelector('#loginPassword');

toggleLoginPassword.addEventListener('click', function () {
    const type = loginPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    loginPassword.setAttribute('type', type);
    this.textContent = type === 'password' ? '👁️' : '🙈';
});
</script>

</body>
</html>
