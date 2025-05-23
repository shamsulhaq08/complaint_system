<!DOCTYPE html>
<html>
<head>
    <title>Add Staff</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: reuse your CSS -->
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <style>
        table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 30px;
    font-family: Arial, sans-serif;
}


/* Table container */
.table-container {
    max-width: 1000px;
    margin: auto;
    overflow-x: auto;
}

/* Stylish Table */
table {
    width: 62%;
    border-collapse: collapse;
    margin-top: 30px;
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.06);
    border-radius: 10px;
    overflow: hidden;
    margin: auto;
}

th, td {
    padding: 12px 20px;
    text-align: left;
}

th {
    background-color: #343a40;
    color: white;
}

tr {
    border-bottom: 1px solid #dee2e6;
}

tr:hover {
    background-color: #f1f1f1;
}

td img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
}

/* Buttons */
.action-buttons a {
    padding: 6px 12px;
    margin-right: 5px;
    text-decoration: none;
    border-radius: 5px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
    color: white;
    transition: 0.2s;
}

.action-buttons .edit {
    background-color: #28a745;
}

.action-buttons .edit:hover {
    background-color: #218838;
}

.action-buttons .delete {
    background-color: #dc3545;
}

.action-buttons .delete:hover {
    background-color: #c82333;
}

/* Responsive Table: Convert to cards on small screens */
@media screen and (max-width: 768px) {
    table, thead, tbody, th, td, tr {
        display: block;
    }

    thead {
        display: none;
    }

    tr {
        margin-bottom: 15px;
        background-color: white;
        padding: 10px;
        border-radius: 10px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }

    td {
        position: relative;
        padding-left: 50%;
        text-align: left;
        border: none;
    }

    td::before {
        content: attr(data-label);
        position: absolute;
        left: 20px;
        top: 12px;
        font-weight: bold;
        color: #6c757d;
        white-space: nowrap;
    }

    td img {
        width: 100px;
        height: 100px;
        margin-top: 10px;
    }

    .action-buttons {
        text-align: center;
        margin-top: 10px;
    }
}
        </style>
<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $designation = trim($_POST['designation']);
    $image = $_FILES['image'];

    // Validate and handle file upload
    $imagePath = null;
    if ($image['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($image['type'], $allowedTypes)) {
            $message = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($image['size'] > $maxSize) {
            $message = "Image size exceeds 2MB limit.";
        } else {
            $imageName = uniqid() . "_" . basename($image['name']);
            $imagePath = "uploads/" . $imageName;

            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
                $message = "Failed to upload image.";
                $imagePath = null;
            }
        }
    }

    // Insert into DB using prepared statement
    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO staff (name, email, designation, image) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("ssss", $name, $email, $designation, $imagePath);

        if ($stmt->execute()) {
            echo "<script>alert('Staff added successfully!');</script>";
        } else {
            echo "<script>alert('Database error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!-- HTML Form -->
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <?php include 'sidebar.php'; ?>
    </aside>

    <main class="admin-main">
        <div class="admin-header">
            <h1>Add Staff</h1>
        </div>

        <div class="container">
            <?php if (!empty($message)) : ?>
                <div style="margin-bottom: 20px; color: green;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form action="add_staff.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-column">
                        <label for="name">Name:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-column">
                        <label for="email">Email:</label>
                        <input type="email" name="email">
                    </div>
                    <div class="form-column">
                        <label for="designation">Designation:</label>
                        <input type="text" name="designation">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-column">
                        <label for="image">Photo:</label>
                        <input type="file" name="image" accept="image/*">
                    </div>
                </div>

                <button type="submit">Add Staff</button>
            </form>
        </div>
    </main>
</div>




</body>
</html>
