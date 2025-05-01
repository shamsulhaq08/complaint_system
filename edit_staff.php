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
<head>
    <title>Edit Staff</title>
    <link rel="stylesheet" href="styles.css">
</head>

<form method="POST" enctype="multipart/form-data">
    <label>Name: <input type="text" name="name" value="<?= htmlspecialchars($staff['name']) ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($staff['email']) ?>"></label><br>
    <label>Designation: <input type="text" name="designation" value="<?= htmlspecialchars($staff['designation']) ?>"></label><br>
    <label>Photo: <input type="file" name="image"> (Leave blank to keep existing)</label><br>
    <img src="<?= $staff['image'] ?>" width="80"><br><br>
    <button type="submit" name="update">Update</button>
</form>
