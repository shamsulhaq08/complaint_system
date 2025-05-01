<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
?>


<?php
include 'db.php';

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM feedback WHERE id = $id");
    echo "<script>alert('Feedback deleted!'); window.location='feedback_view.php';</script>";
    exit;
}

// Fetch all feedback
$result = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Staff</title>
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
    background-color: white;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.06);
    border-radius: 10px;
    overflow: hidden;
    margin: auto;
}
    </style>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
    <?php include 'sidebar.php'; ?>
    </aside>

    <!-- Main content -->
    <main class="admin-main">
        <div class="admin-header">
        <div class="container">
      <h1>View Staff</h1>
  
        
<?php
include 'db.php'; // Include connection file

if (isset($_POST['submit'])) {
    $name        = $conn->real_escape_string($_POST['name']);
    $email       = $conn->real_escape_string($_POST['email']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $imagePath   = "";

    // Handle file upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $imageName = basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . time() . "_" . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    // Insert into DB
    $sql = "INSERT INTO staff (name, email, designation, image)
            VALUES ('$name', '$email', '$designation', '$imagePath')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Staff added successfully.');</script>";
} else {
    echo "<script>alert('Error: " . addslashes($conn->error) . "');</script>";
}

    $conn->close();
}
?>


<?php
// Reconnect if closed above
include 'db.php';

// Fetch staff
$result = $conn->query("SELECT * FROM staff ORDER BY id DESC");

if ($result->num_rows > 0) {
    // echo "<h2 stye="text-align: center;">Staff List</h2>";
    echo "<table border='1'     width: 100%; cellpadding='10' style='margin-top: 20px; border-collapse: collapse;'>";
    echo "<tr>
    <th>Photo</th>
    <th>Name</th>
    <th>Email</th>
    <th>Designation</th>
    <th>Actions</th>
  </tr>";

          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><img src='" . $row['image'] . "' width='80' height='80'></td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['designation']) . "</td>";
            echo "<td class='action-buttons'>
                    <a href='edit_staff.php?id=" . $row['id'] . "' class='edit'>Edit</a>
                    <a href='delete_staff.php?id=" . $row['id'] . "' class='delete' onclick=\"return confirm('Are you sure you want to delete this staff?');\">Delete</a>
                  </td>";
            echo "</tr>";
        }

    echo "</table>";
} else {
    echo "<p>No staff records found.</p>";
}

$conn->close();
?>
    </div>
</div>

   
    </main>

 
</body>
</html>
