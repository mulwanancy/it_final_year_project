<?php
include '../config/connect.php';
$error = "";
$success = "";
$showLogin = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nurse_number = mysqli_real_escape_string($conn, $_POST['nurse_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM nurses WHERE nurse_number='$nurse_number' AND email='$email' LIMIT 1");

    if(mysqli_num_rows($check) == 1){
        $nurse = mysqli_fetch_assoc($check);
        if(!empty($nurse['password'])){
            $error = "❌ You have already registered. Please login.";
        } else {
            $update = mysqli_query($conn, "UPDATE nurses SET password='$hashedPassword' WHERE nurse_number='$nurse_number' AND email='$email'");
            if($update){
                // ✅ NEW: Redirect directly to login with success flag
                header("Location: index.php?registered=success");
                exit();
            } else {
                $error = "❌ Failed to register: " . mysqli_error($conn);
            }
        }
    } else {
        $error = "❌ Nurse number and email do not match any existing nurse.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - NurseShift Payroll</title>
<style>
body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin:0; padding:0; }

/* Navbar like homepage */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #0b3d91;
    padding: 20px 40px;
    min-height: 80px;
}

.navbar .logo-container {
    display: flex;
    align-items: center;
}

.navbar .logo-container img {
    height: 80px;
    margin-right: 10px;
}

.navbar .logo-text {
    display: flex;
    flex-direction: column;
    color: #ffcc00;
}

.navbar .logo {
    font-size: 24px;
    font-weight: bold;
}

.navbar .tagline {
    font-size: 14px;
    font-style: italic;
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
}

.navbar ul li a:hover {
    color: #ffcc00;
}

/* Container */
.container { 
    width: 350px; 
    margin: 50px auto; 
    padding: 20px; 
    background: white; 
    border-radius: 8px; 
    box-shadow: 0 2px 5px rgba(0,0,0,0.2); 
}

input, button { width: 100%; padding: 10px; margin: 5px 0; border-radius: 4px; border: 1px solid #ccc; }
button { background: #0b3d91; color: white; border: none; cursor: pointer; }
button:hover { background: #062964; }

.error { color: red; text-align: center; }
.success { color: green; text-align: center; }
h2 { text-align: center; color: #0b3d91; }

a { text-decoration: none; color: #0b3d91; font-weight: bold; }
.eye-toggle { position: absolute; right: 10px; top: 12px; cursor: pointer; color:#0b3d91; }
.input-wrapper { position: relative; }

</style>
</head>
<body>

<!-- Navbar same as homepage -->
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
        <li><a href="register.php">Register</a></li>
    </ul>
</div>

<div class="container">
    <h2>Register Here</h2>

    <?php
    if ($error) echo "<p class='error'>$error</p>";
    if ($success) echo "<p class='success'>$success</p>";
    ?>

    <form method="POST" action="register.php">
        <input type="text" name="nurse_number" placeholder="Nurse Number" required>
        <input type="email" name="email" placeholder="Email" required>
        <div class="input-wrapper">
            <input type="password" id="regPassword" name="password" placeholder="Create Password" required>
            <span id="toggleRegPassword" class="eye-toggle">👁️</span>
        </div>
        <button type="submit">Register</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        <a href="index.php">Go back to Login</a>
    </p>
</div>

<script>
const toggleRegPassword = document.querySelector('#toggleRegPassword');
const regPassword = document.querySelector('#regPassword');

toggleRegPassword.addEventListener('click', function () {
    const type = regPassword.getAttribute('type') === 'password' ? 'text' : 'password';
    regPassword.setAttribute('type', type);
    this.textContent = type === 'password' ? '👁️' : '🙈';
});
</script>

</body>
</html>
