<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config/connect.php';

session_start();
echo "<h2>Debug Test</h2>";

$sql = "SELECT * FROM nurses LIMIT 1";
$result = mysqli_query($conn, $sql);

if ($result) {
    echo "✅ Database connection works!<br>";
    $row = mysqli_fetch_assoc($result);
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "❌ SQL error: " . mysqli_error($conn);
}
?>
