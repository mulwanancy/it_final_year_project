<?php
// reset.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Page - NurseShift Management</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f2f2f2;
      color: #000;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #0b3d91;
      padding: 20px 40px;
      min-height: 90px;
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

    /* Reset form */
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
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .login-box h2 {
      margin-bottom: 20px;
      color: #0b3d91; /* Blue heading */
    }
    .login-box input[type="text"],
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
      background: #0b3d91;
      color: #ffcc00; 
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .login-box button:hover {
      background: #094080;
    }
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
    <li><a href="index.php">Home</a></li>
    <li><a href="faq.php">FAQ</a></li>
    <li><a href="reset.php">Reset Page</a></li>
  </ul>
</div>

<!-- Reset Form -->
<div class="login-container">
  <div class="login-box">
    <h2>Reset Account</h2>
    <form action="reset_process.php" method="POST">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="email" name="email" placeholder="Enter Email" required>
      <button type="submit">Request Reset</button>
    </form>
    <!-- New informational line -->
    <p style="margin-top:15px; font-size:14px; color:#555;">
      Your username is your nurse number.
    </p>
  </div>
</div>

</body>
</html>
