<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "12345";
$dbname = "complaint_system";

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
$urgency          = $_POST['urgency'] ?? ''; // Fixed typo (was 'urgenacy')
$preferred_date   = $_POST['preferred_date'] ?? '';
$staff            = $_POST['staff'] ?? []; // array of staff IDs (multiple)

// Validate required fields
if (empty($client_name) || empty($client_email) || empty($complaint_title)) {
    die("Required fields are missing");
}

// Insert complaint
$stmt = $conn->prepare("INSERT INTO complaints (client_name, client_email, client_phone, complaint_title, complaint_desc, category, urgency, preferred_date)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $client_name, $client_email, $client_phone, $complaint_title, $complaint_desc, $category, $urgency, $preferred_date);
$stmt->execute();
$complaint_id = $stmt->insert_id;

// Assign staff to complaint (multiple) - only if staff were selected
if (!empty($staff)) {
    foreach ($staff as $staff_id) {
        $staff_id = (int)$staff_id; // sanitize
        $stmt2 = $conn->prepare("INSERT INTO complaint_staff (complaint_id, staff_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $complaint_id, $staff_id);
        $stmt2->execute();
    }
}

// Handle multiple file uploads
if (!empty($_FILES['files']['name'][0])) {
    // Create uploads directory if it doesn't exist
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

echo "Complaint submitted successfully!";
$conn->close();
?>