<!DOCTYPE html>
<html>
<head>
    <title>Add Staff</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: reuse your CSS -->
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

<h1>Add New Staff</h1>

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

    <button type="submit" name="submit">Add Staff</button>
</form>

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
    echo "<table border='1' cellpadding='10' style='margin-top: 20px; border-collapse: collapse;'>";
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

</body>
</html>
