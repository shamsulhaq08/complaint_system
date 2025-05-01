<?php
include 'db.php';

$id = $_GET['id'];

// Optional: delete associated image file
$result = $conn->query("SELECT image FROM staff WHERE id=$id");
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (file_exists($row['image'])) {
        unlink($row['image']);
    }
}

// Delete record
$conn->query("DELETE FROM staff WHERE id=$id");
header("Location: add_staff.php");
?>
