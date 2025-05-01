<?php
// Add these lines before creating the mysqli connection
$servername = "localhost";
$username = "root";
$password = "12345"; // or "" if no password
$dbname = "complaint_system";

// Now create the connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>