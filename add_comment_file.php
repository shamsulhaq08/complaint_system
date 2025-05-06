<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $complaint_id = intval($_POST['complaint_id']);
    $comment = trim($_POST['comment']);

    // Insert comment
    if (!empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO complaint_comments (complaint_id, comment) VALUES (?, ?)");
        $stmt->bind_param("is", $complaint_id, $comment);
        $stmt->execute();
        $stmt->close();
    }

    // Upload file
    if (!empty($_FILES['file']['name'])) {
        $upload_dir = "uploads/";
        $file_name = basename($_FILES["file"]["name"]);
        $target_file = $upload_dir . time() . "_" . $file_name;

        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO complaint_files (complaint_id, file_path) VALUES (?, ?)");
            $stmt->bind_param("is", $complaint_id, $target_file);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: complaint_view.php?page=1");
    exit();
}
?>
