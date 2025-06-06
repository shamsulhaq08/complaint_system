<?php
include 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['submit_feedback'])) {
    $client_name = $_POST['client_name'] ?? '';
    $client_email = $_POST['client_email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $feedback_title = $_POST['feedback_title'] ?? '';
    $feedback_desc = $_POST['feedback_desc'] ?? '';

    // Validate required fields
    if (!empty($client_name) && !empty($client_email) && !empty($feedback_title)) {
        // Prepare SQL (note: 5 placeholders for 5 columns)
        $stmt = $conn->prepare("INSERT INTO feedback (client_name, client_email, phone, feedback_title, feedback_desc) VALUES (?, ?, ?, ?, ?)");

        // Check if prepare failed
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind and execute
        $stmt->bind_param("sssss", $client_name, $client_email, $phone, $feedback_title, $feedback_desc);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Feedback submitted successfully!');</script>";
    } else {
        echo "<script>alert('All fields are required!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h1> Feedback</h1>
    <form method="POST">
        <input type="text" name="client_name" placeholder="Your Name" required>
        <input type="email" name="client_email" placeholder="Your Email" required>
        <input type="text" name="phone" placeholder="Your Whatsapp Number" required>
        <input type="text" name="feedback_title" placeholder="Feedback Title" required>
        <textarea name="feedback_desc" placeholder="Feedback Description" rows="4" required></textarea>
        <button type="submit" name="submit_feedback">Submit Feedback</button>
    </form>
</div>

</body>
</html>
