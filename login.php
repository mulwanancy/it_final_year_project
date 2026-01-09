<?php
session_start();
include '../config/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Clear previous sessions
    session_unset();
    session_destroy();
    session_start();

    // Find user by email
    $sql = "SELECT * FROM nurses WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['first_name'] . " " . $row['last_name'];

            // Set role exactly from DB
            $_SESSION['role'] = trim(strtolower($row['role'])); 

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
