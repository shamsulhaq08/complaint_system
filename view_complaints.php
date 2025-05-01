<?php
include 'db.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM complaints ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Complaints</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }

        .complaint-box {
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 5px solid #4CAF50;
            cursor: pointer;
            box-shadow: 0 1px 6px rgba(0,0,0,0.1);
        }

        .complaint-box:hover {
            background: #f9f9f9;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 6px;
            width: 80%;
            max-width: 700px;
            position: relative;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: black;
        }

        .files a {
            display: block;
            margin-bottom: 5px;
            color: #007bff;
        }
    </style>
</head>
<body>

<h1>Complaint List</h1>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $complaint_id = $row['id'];
        $modal_id = "modal_" . $complaint_id;

        // Basic box
        echo "<div class='complaint-box' onclick=\"document.getElementById('$modal_id').style.display='block'\">";
        echo "Name:<strong>" . htmlspecialchars($row['complaint_title']) . "</strong><br>";
        echo htmlspecialchars($row['client_name']) . " - " . htmlspecialchars($row['urgency']);
        echo "</div>";

        // Modal popup
        echo "<div id='$modal_id' class='modal'>";
        echo "<div class='modal-content'>";
        echo "<span class='close' onclick=\"document.getElementById('$modal_id').style.display='none'\">&times;</span>";
        echo "<h2>" . htmlspecialchars($row['complaint_title']) . "</h2>";
        echo "<p><strong>Client:</strong> " . htmlspecialchars($row['client_name']) . " (" . htmlspecialchars($row['client_email']) . ")</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['client_phone']) . "</p>";
        echo "<p><strong>Description:</strong><br>" . nl2br(htmlspecialchars($row['complaint_desc'])) . "</p>";
        echo "<p><strong>Category:</strong> " . htmlspecialchars($row['category']) . "</p>";
        echo "<p><strong>Urgency:</strong> " . htmlspecialchars($row['urgency']) . "</p>";
        echo "<p><strong>Preferred Date:</strong> " . htmlspecialchars($row['preferred_date']) . "</p>";

        // Assigned staff
        $staff_sql = "SELECT s.name FROM complaint_staff cs JOIN staff s ON cs.staff_id = s.id WHERE cs.complaint_id = $complaint_id";
        $staff_result = $conn->query($staff_sql);
        if ($staff_result->num_rows > 0) {
            echo "<p><strong>Assigned Staff:</strong><br>";
            while ($staff_row = $staff_result->fetch_assoc()) {
                echo htmlspecialchars($staff_row['name']) . "<br>";
            }
            echo "</p>";
        }

        // Files
        $file_sql = "SELECT file_path FROM complaint_files WHERE complaint_id = $complaint_id";
        $file_result = $conn->query($file_sql);
        if ($file_result->num_rows > 0) {
            echo "<p><strong>Attached Files:</strong><br>";
            while ($file_row = $file_result->fetch_assoc()) {
                $file = htmlspecialchars($file_row['file_path']);
                echo "<a href='$file' target='_blank'>View File</a>";
            }
            echo "</p>";
        }

        echo "</div></div>";
    }
} else {
    echo "<p>No complaints found.</p>";
}
$conn->close();
?>

<script>
// Close modals when clicking outside of them
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
};
</script>

</body>
</html>
