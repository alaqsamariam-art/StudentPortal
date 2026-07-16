<?php

// ===============================
// Database Configuration
// ===============================

$host = "localhost";
$username = "root";
$password = "";
$database = "student_portal";

// Create Connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check Connection
if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// UTF-8 Support
mysqli_set_charset($conn, "utf8");

?>