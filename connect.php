<?php
$servername = "sql113.infinityfree.com";   // Database host
$username   = "if0_40067296";              // Database username
$password   = "kamusiliu1993";             // Database password (the one you saw)
$dbname     = "if0_40067296_nurseshift_db"; // Database name

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
} else {
    // echo "✅ Connected successfully"; // (you can uncomment for testing)
}
?>
