<?php
include 'db.php';


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$client_name      = $_POST['client_name'] ?? '';
$client_email     = $_POST['client_email'] ?? '';
$client_phone     = $_POST['client_phone'] ?? '';
$complaint_title  = $_POST['complaint_title'] ?? '';
$complaint_desc   = $_POST['complaint_desc'] ?? '';
$category         = $_POST['category'] ?? '';
$urgency          = $_POST['urgency'] ?? '';
$preferred_date   = $_POST['preferred_date'] ?? '';
$staff            = $_POST['staff'] ?? [];

// Validate required fields
if (empty($client_name) || empty($client_email) || empty($complaint_title)) {
    die("Required fields are missing");
}

// Insert complaint into DB
$stmt = $conn->prepare("INSERT INTO complaints (client_name, client_email, client_phone, complaint_title, complaint_desc, category, urgency, preferred_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $client_name, $client_email, $client_phone, $complaint_title, $complaint_desc, $category, $urgency, $preferred_date);
$stmt->execute();
$complaint_id = $stmt->insert_id;

// Assign staff
if (!empty($staff)) {
    foreach ($staff as $staff_id) {
        $staff_id = (int)$staff_id;
        $stmt2 = $conn->prepare("INSERT INTO complaint_staff (complaint_id, staff_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $complaint_id, $staff_id);
        $stmt2->execute();
    }
}

// Upload files
if (!empty($_FILES['files']['name'][0])) {
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }

    $file_count = count($_FILES['files']['name']);
    for ($i = 0; $i < $file_count; $i++) {
        if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
            $file_name = time() . "_" . basename($_FILES['files']['name'][$i]);
            $file_path = "uploads/" . $file_name;

            if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $file_path)) {
                $stmt3 = $conn->prepare("INSERT INTO complaint_files (complaint_id, file_path) VALUES (?, ?)");
                $stmt3->bind_param("is", $complaint_id, $file_path);
                $stmt3->execute();
            }
        }
    }
}

// Send email to client and admin
$subject = "Complaint Received: " . $complaint_title;

$message = "
<html>
<head><title>Complaint Details</title></head>
<body>
    <h2>Your Complaint Has Been Submitted</h2>
    <p><strong>Name:</strong> {$client_name}</p>
    <p><strong>Email:</strong> {$client_email}</p>
    <p><strong>Phone:</strong> {$client_phone}</p>
    <p><strong>Title:</strong> {$complaint_title}</p>
    <p><strong>Description:</strong> {$complaint_desc}</p>
    <p><strong>Category:</strong> {$category}</p>
    <p><strong>Urgency:</strong> {$urgency}</p>
    <p><strong>Preferred Date:</strong> {$preferred_date}</p>
    <p>We will review your complaint and get back to you shortly.</p>
</body>
</html>
";

$headers  = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
$headers .= "From: noreply@chapter2.pk" . "\r\n"; // Replace with real domain

// Send to client
mail($client_email, $subject, $message, $headers);

// Send to you (admin)
$admin_email = "shamsulhaq08@gmail.com"; // Replace with your email
mail($admin_email, "New Complaint Submitted", $message, $headers);

// Final output
echo "Complaint submitted successfully!";
$conn->close();
?>
