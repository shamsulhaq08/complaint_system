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
    <title>Edit Staff<</title>
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="styles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>


<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
    <?php include 'sidebar.php'; ?>
    </aside>

    <!-- Main content -->
    <main class="admin-main">
    <div class="admin-header">
    <h1>Edit Staff</h1>
        </div>

        <div class="container">
       
        <?php
include 'db.php';

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM staff WHERE id = $id");

if ($result->num_rows > 0) {
    $staff = $result->fetch_assoc();
} else {
    echo "Staff not found.";
    exit;
}

if (isset($_POST['update'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $designation = $conn->real_escape_string($_POST['designation']);
    $imagePath = $staff['image'];

    // Handle new image upload if available
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageName = basename($_FILES["image"]["name"]);
        $imagePath = $targetDir . time() . "_" . $imageName;
        move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath);
    }

    $conn->query("UPDATE staff SET name='$name', email='$email', designation='$designation', image='$imagePath' WHERE id=$id");
    echo "<script>alert('Staff updated successfully.'); window.location='add_staff.php';</script>";
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($staff['name']) ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($staff['email']) ?>"></label><br>
    <label>Designation: <input type="text" name="designation" value="<?= htmlspecialchars($staff['designation']) ?>"></label><br>
    <label>Photo: <input type="file" name="image"> (Leave blank to keep existing)</label><br>
    <img src="<?= $staff['image'] ?>" width="80"><br><br>
    <button type="submit" name="update">Update</button>
</form>

    </div>
</div>

   
    </main>

 
</body>
</html>
