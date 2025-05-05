<?php
include 'db.php'; // Update path to your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = intval($_POST['complaint_id']);
    $new_status = $_POST['status'];

    if (in_array($new_status, ['Pending', 'Working', 'Completed'])) {
        $stmt = $conn->prepare("UPDATE complaints SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $complaint_id);
        $stmt->execute();
        $stmt->close();
    }
}
header("Location: complaint_view.php");
exit;
