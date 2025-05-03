<?php
// Database connection
include('db.php');

// Function to insert complaint
function insertComplaint($conn, $staff_ids, $complaint_desc, $client_name, $client_email, $client_phone = null, $preferred_date = null) {
    // Validate required fields
    if (empty($complaint_desc)) {
        throw new Exception("Complaint description is required");
    }
    if (empty($client_name)) {
        throw new Exception("Client name is required");
    }
 

    // Prepare the SQL statement
    $sql = "INSERT INTO complaints (
            staff_ids, 
            complaint_desc, 
            client_name, 
            client_email, 
            client_phone, 
            preferred_date
        ) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param(
        "ssssss",
        $staff_ids,
        $complaint_desc,
        $client_name,
        $client_email,
        $client_phone,
        $preferred_date
    );

    // Execute the statement
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    // Return the inserted ID
    return $stmt->insert_id;
}

// Process form submission
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $staff_ids = implode(',', $_POST['staff_ids'] ?? []);
        $complaint_desc = $_POST['complaint_desc'] ?? '';
        $client_name = $_POST['client_name'] ?? '';
        $client_email = $_POST['client_email'] ?? '';
        $client_phone = $_POST['client_phone'] ?? null;
        $preferred_date = $_POST['preferred_date'] ?? null;


        // Insert complaint
        $complaint_id = insertComplaint(
            $conn,
            $staff_ids,
            $complaint_desc,
            $client_name,
            $client_email,
            $client_phone,
            $preferred_date
           
        );

        // Handle file uploads
        if (!empty($_FILES['files']['name'][0])) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Process each file
            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['files']['error'][$key] === UPLOAD_ERR_OK) {
                    // Validate file
                    $file_name = basename($_FILES['files']['name'][$key]);
                    $file_size = $_FILES['files']['size'][$key];
                    $file_type = $_FILES['files']['type'][$key];
                    $target_path = $uploadDir . uniqid() . '_' . $file_name;

                    // Check file size (max 5MB)
                    if ($file_size > 5000000) {
                        throw new Exception("File $file_name is too large (max 5MB)");
                    }

                    // Check file type (images and PDFs)
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
                    if (!in_array($file_type, $allowed_types)) {
                        throw new Exception("Invalid file type for $file_name. Only JPG, PNG, GIF, and PDF are allowed.");
                    }

                    // Move uploaded file
                    if (move_uploaded_file($tmp_name, $target_path)) {
                        $safe_path = $conn->real_escape_string($target_path);
                        $conn->query("INSERT INTO complaint_files (complaint_id, file_path) VALUES ($complaint_id, '$safe_path')");
                    } else {
                        throw new Exception("Failed to upload $file_name");
                    }
                }
            }
        }

        echo "<script>alert('Complaint submitted successfully!');</script>";
       
        $message_type = 'success';
        
        // Clear form (optional)
        $_POST = [];
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

// Fetch staff for the dropdown
$staff_result = $conn->query("SELECT id, name FROM staff");
$staff_members = [];
if ($staff_result) {
    $staff_members = $staff_result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint</title>
    <style>
      body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    line-height: 1.6;
    max-width: 900px;
    margin: 0 auto;
    background-color: #f4f6f9;
    padding: 20px;
}

form {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 10px;
    margin: auto;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    color: #333;
    margin: 0;
}

/* Form layout */
.form-row {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.form-group {
    flex: 1 1 calc(33.33% - 20px); /* 3 columns with gap */
    min-width: 200px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-group {
        flex: 1 1 calc(50% - 20px); /* 2 columns on tablets */
    }
}

@media (max-width: 480px) {
    .form-group {
        flex: 1 1 100%; /* 1 column on phones */
    }
}

/* Inputs & labels */
label {
    font-weight: 600;
    
    display: block;
    color: #444;
}

.required {
    color: red;
}

input[type="text"],
input[type="email"],
input[type="date"],
input[type="datetime-local"],
textarea,
select {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 14px;
    background-color: #fefefe;
    box-sizing: border-box;
    transition: border-color 0.3s;
}

input:focus,
textarea:focus {
    border-color: #2e86de;
    outline: none;
}

textarea {
    resize: vertical;
    min-height: 100px;
}

/* File upload */
.file-upload {
    padding: 8px;
    margin-top: 5px;
}

.file-hint {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
}

/* Staff selection */
.staff-selection {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-top: 10px;
}

.staff-option {
    border: 2px solid transparent;
    padding: 2px;
    border-radius: 10px;
    background-color: #f9f9f9;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 80px;
}

.staff-option:hover {
    border-color: #ccc;
}

.staff-option img {
    width: 70px;
    height: 70px;
    object-fit: cover;
    border-radius: 50%;
    margin-bottom: 10px;
}

@media (max-width: 600px) {
    .staff-selection {
        justify-content: center;
    }

    .staff-option {
        width: 100px;
    }
}

/* Buttons */
button[type="submit"] {
    width: 100%;
    background-color: #4f46e5;
    color: white;
    padding: 12px;
    font-size: 16px;
    font-weight: bold;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #1b66c9;
}

/* Messages */
.message {
    padding: 10px;
    margin-bottom: 20px;
    border-radius: 4px;
}

.success {
    background-color: #dff0d8;
    color: #3c763d;
    border: 1px solid #d6e9c6;
}

.error {
    background-color: #f2dede;
    color: #a94442;
    border: 1px solid #ebccd1;
}

    </style>
</head>
<body>
    
    
    <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    <?php
// Ensure $conn is your mysqli connection
$query = "SELECT id, name, image FROM staff";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

    <form method="POST" action="" enctype="multipart/form-data">
    <h2>Submit a Complaint</h2>
        <div class="form-group">
            <label for="staff_ids" style="text-align: center;">Complaint Against (select one or more):</label>
            <div class="staff-selection">
        <?php while ($staff = $result->fetch_assoc()): ?>
            <label class="staff-option" required>
                <input type="checkbox" name="staff_ids[]" value="<?= $staff['id'] ?>" style="display: none;">
                <img src="<?= htmlspecialchars($staff['image']) ?>" alt="Staff">
                <div><?= htmlspecialchars($staff['name']) ?></div>
            </label>
        <?php endwhile; ?>
    </div>

    <script>
        document.querySelectorAll('.staff-option').forEach(option => {
            option.addEventListener('click', () => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                checkbox.checked = !checkbox.checked;
                option.style.borderColor = checkbox.checked ? '#2e86de' : 'transparent';
                option.style.backgroundColor = checkbox.checked ? '#e6f0ff' : '#f9f9f9';
            });
        });
    </script>
        </div>

        <div class="form-row">
        <div class="form-group">
            <label for="client_name">Your Name <span class="required">*</span></label>
            <input type="text" id="client_name" name="client_name" required>
        </div>

        <div class="form-group">
            <label for="client_email">Your Email </label>
            <input type="email" id="client_email" name="client_email" >
        </div>

        <div class="form-group">
            <label for="client_phone">Phone Number <span class="required">*</span></label>
            <input type="text" id="client_phone" name="client_phone" required>
        </div>
    </div>
    <div class="form-group">
            <label for="complaint_desc">Complaint Description <span class="required">*</span></label>
            <textarea id="complaint_desc" name="complaint_desc" required></textarea>
        </div>

    <div class="form-row">
        <div class="form-group">
            <label for="preferred_date">Incident Date</label>
          
            <input type="datetime-local" id="preferred_date" name="preferred_date">
        </div>

        <div class="form-group">
            <label for="files">Upload Image</label>
            <input type="file" id="files" name="files[]" multiple class="file-upload">
            <p class="file-hint">Upload JPG, PNG, PDF. Max 5MB each.</p>
        </div>

     
    </div>


    <button type="submit">Submit Complaint</button>
    </form>
</body>
</html>