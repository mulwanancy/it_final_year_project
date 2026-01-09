<?php
// faq.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FAQ - NurseShift Management</title>
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

    /* FAQ content */
    .content {
      max-width: 800px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }

    .content h2 {
      color: #0b3d91;
      margin-bottom: 20px;
    }

    .content p {
      margin-bottom: 15px;
      line-height: 1.5;
    }
    .content a {
      color: #0b3d91;
      text-decoration: underline;
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

<!-- FAQ Section -->
<div class="content">
  <h2>Frequently Asked Questions</h2>

  <p><strong>Q1: What is NurseShift Payroll?</strong><br> 
     A system for managing nurses’ shifts, attendance, and payroll efficiently.</p>

  <p><strong>Q2: Who can use this system?</strong><br> 
     Nurses and administrators in healthcare institutions.</p>

  <p><strong>Q3: How do I reset my account?</strong><br> 
     Use the <a href="reset.php">Reset Page</a> to request an account reset.</p>

  <p><strong>Q4: How do I register as a new user?</strong><br> 
     Click on the <a href="register.php">Register</a> link and fill in your details.</p>

  <p><strong>Q5: Can I view my shift schedule?</strong><br>
     Yes, after logging in, navigate to your profile to view assigned shifts.</p>

  <p><strong>Q6: How do I update my contact information?</strong><br>
     Go to your profile page and update your contact details. Changes are saved automatically.</p>

  <p><strong>Q7: What should I do if I forget my password?</strong><br>
     Click on <a href="forgot_password.php">Forgot Password</a> and follow the instructions to reset it.</p>

  <p><strong>Q8: How is my salary calculated?</strong><br>
     The system calculates your salary based on attendance, shifts worked, and allowances.</p>

  <p><strong>Q9: Can administrators make changes to shifts?</strong><br>
     Yes, only administrators can assign or adjust nurse shifts and attendance records.</p>

  <p><strong>Q10: Is my data secure in the system?</strong><br>
     Yes, NurseShift Payroll uses secure protocols to ensure your data is safe and confidential.</p>
</div>

</body>
</html>
