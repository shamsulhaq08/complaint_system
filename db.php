<?php
// db.php

$host = 'localhost';          // Hostname
$user = 'root';               // MySQL username
$password = '12345';               // MySQL password (empty by default for XAMPP)
$database = 'complaint_system';  // Your database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set character set to UTF-8
$conn->set_charset("utf8");
?>
