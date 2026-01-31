<?php
// Database configuration
$host = "localhost";
$username = "gym_user";
$password = "gym123";
$database = "gym_simple";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

// Function to sanitize input
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($input))));
}

// Function to show alert
function showAlert($type, $message) {
    echo "<div class='alert alert-$type'>$message</div>";
}
?>