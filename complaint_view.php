<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
?>

<?php
include 'db.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$complaints_per_page = 8;
$offset = ($page - 1) * $complaints_per_page;

$total_sql = "SELECT COUNT(*) AS total FROM complaints";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_complaints = $total_row['total'];
$total_pages = ceil($total_complaints / $complaints_per_page);

$sql = "SELECT * FROM complaints ORDER BY id DESC LIMIT $complaints_per_page OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Complaints</title>
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<style>

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
    padding: 2px;
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
.pagination a {
display: inline-block;
padding: 8px 12px;
margin: 0 4px;
background-color: #2e86de;
color: white;
border-radius: 5px;
text-decoration: none;
}
.pagination span {
padding: 8px 12px;
margin: 0 4px;
font-weight: bold;
}

/* Modal Background */
.modal {
display: none; /* Hidden by default */
position: fixed;
z-index: 1000;
left: 0;
top: 0;
width: 100%;
height: 100%;
overflow: auto;
background-color: rgba(0,0,0,0.5); /* Black with opacity */
}

/* Modal Content Box */
.modal-content {
background-color: #fff;
margin: 10% auto;
padding: 10px;
border: 1px solid #888;
width: 80%;
max-width: 600px;
border-radius: 8px;
box-shadow: 0 5px 15px rgba(0,0,0,0.3);
font-family: Arial, sans-serif;
color: #333;
}

/* Close Button */
.close {
color: #aaa;
float: right;
font-size: 28px;
font-weight: bold;
cursor: pointer;
transition: color 0.3s ease;
}

.close:hover,
.close:focus {
color: #000;
}

/* Paragraphs inside modal */
.modal-content p {
margin: 15px 0;
line-height: 1.5;
}

/* FontAwesome Icons */
.modal-content i {
color: #007BFF;
margin-right: 8px;
}

.file-item {
margin-bottom: 15px;
}

.file-item img {
border: 2px solid #ddd;
border-radius: 5px;
}

.file-item audio {
width: 100%;
max-width: 300px;
margin-top: 10px;
}

.file-item a {
color: #007bff;
text-decoration: none;
}

.file-item a:hover {
text-decoration: underline;
}
form select {
    padding: 5px;
    margin-right: 5px;
}
form button {
    padding: 5px 10px;
    background-color: #28a745;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 4px;
}
form button:hover {
    background-color: #218838;
}

button, .action-buttons a {
    background-color:rgb(116, 116, 116);
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    box-sizing: border-box;
    display: inline-block;
    transition: background-color 0.3s;
}
</style>
<div class="admin-wrapper">
    <aside class="admin-sidebar">
        <?php include 'sidebar.php'; ?>
    </aside>
    <main class="admin-main">
        <div class="admin-header">
        <div class="container">
            <h1>Complaint List</h1>

<?php
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $complaint_id = $row['id'];
        $modal_id = "modal_" . $complaint_id;
        $status = htmlspecialchars($row['status']);
        $badge_color = 'gray';
        if ($status == 'Pending') $badge_color = 'orange';
        elseif ($status == 'Working') $badge_color = 'blue';
        elseif ($status == 'Completed') $badge_color = 'green';

        echo "<div class='complaint-box' onclick=\"document.getElementById('$modal_id').style.display='block'\">";
        echo "<i class='fa fa-user'></i> Customer Name: <strong>" . htmlspecialchars($row['client_name']) . "</strong> &nbsp;&nbsp;&nbsp;";
        echo "<i class='fa fa-calendar'></i> Incident Date: <strong>" . htmlspecialchars($row['preferred_date']) . "</strong> &nbsp;&nbsp;&nbsp;";
        echo "<i class='fa fa-book'></i> Status: <strong style='color:#ed7f00'>" . htmlspecialchars($row['status']) . "</strong><br>";
        echo "</div>";

        echo "<div id='$modal_id' class='modal'>";
        echo "<div class='modal-content'>";

        echo "  <div class='status-buttons' data-complaint-id='" . $row['id'] . "'> <strong>Status:</strong> ";
        $statuses = ['Pending', 'Working', 'Completed'];
        foreach ($statuses as $s) {
            $button_style = ($status == $s) ? "style='background-color:#00a100; color:white;'" : "";
            echo "<button class='status-btn' data-status='$s' $button_style>$s</button> ";
        }
        echo "<span class='status-msg' style='margin-left: 10px; color: green;'></span>";
        echo "</div>";
        
                
        
        echo "<span class='close' onclick=\"document.getElementById('$modal_id').style.display='none'\" style='font-size: 24px; font-weight: bold; color: #ff0000; position: absolute; top: 10px; right: 15px; cursor: pointer;'>&times;</span>";

        echo "<p><i class='fa fa-user'></i> <strong>Customer Name:</strong> " . htmlspecialchars($row['client_name']) . " (" . htmlspecialchars($row['client_email']) . ")</p>";
        echo "<p><i class='fa fa-phone'></i> <strong>Phone:</strong> " . htmlspecialchars($row['client_phone']) . "</p>";
        echo "<p><i class='fa fa-file-alt'></i> <strong>Description:</strong>" . nl2br(htmlspecialchars($row['complaint_desc'])) . "</p>";
        echo "<p><i class='fa fa-calendar'></i> <strong>Incident Date:</strong> " . htmlspecialchars($row['preferred_date']) . "</p>";


        // Staff names
        $staff_ids_string = $row['staff_ids'];
        if (!empty($staff_ids_string)) {
            $staff_ids_array = array_filter(array_map('intval', explode(',', $staff_ids_string)));
            if (count($staff_ids_array) > 0) {
                $staff_ids_list = implode(',', $staff_ids_array);
                $staff_sql = "SELECT name FROM staff WHERE id IN ($staff_ids_list)";
                $staff_result = $conn->query($staff_sql);
                if ($staff_result && $staff_result->num_rows > 0) {
                    echo "<p><strong>Complaint Against :</strong><br>";
                    while ($staff_row = $staff_result->fetch_assoc()) {
                        $staff_name = htmlspecialchars($staff_row['name']);
                        echo "<div style='margin-bottom: 5px;'>👤 $staff_name</div>";
                    }
                    echo "</p>";
                } else {
                    echo "<p><strong>Complaint Against:</strong> Not found</p>";
                }
            } else {
                echo "<p><strong>Complaint Against Staff:</strong> None</p>";
            }
        } else {
            echo "<p><strong>Complaint Against Staff:</strong> None</p>";
        }

        // Files
        $file_sql = "SELECT file_path FROM complaint_files WHERE complaint_id = $complaint_id";
        $file_result = $conn->query($file_sql);

        if ($file_result->num_rows > 0) {
            echo "<p><strong>Attached Files:</strong><br>";
            while ($file_row = $file_result->fetch_assoc()) {
                $file = htmlspecialchars($file_row['file_path']);
                $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    echo "<img src='$file' style='max-width: 200px;'><br><a href='$file' target='_blank'>View Image</a><br>";
                } elseif (in_array($file_extension, ['mp3', 'wav'])) {
                    echo "<audio controls src='$file'></audio><br><a href='$file' target='_blank'>Listen</a><br>";
                } else {
                    echo "<a href='$file' target='_blank'>Listen Customer Voice</a><br>";
                }
            }
            echo "</p>";
        }

        echo "</div></div>";
    }

    echo "<div class='pagination' style='margin-top: 20px; text-align:center;'>";
    if ($page > 1) {
        echo "<a href='?page=" . ($page - 1) . "'>&laquo; Prev</a> ";
    }
    echo "<span> Page $page of $total_pages </span>";
    if ($page < $total_pages) {
        echo " <a href='?page=" . ($page + 1) . "'>Next &raquo;</a>";
    }
    echo "</div>";
} else {
    echo "<p>No complaints found.</p>";
}
$conn->close();
?>

<script>
document.querySelectorAll('.status-buttons').forEach(group => {
    const buttons = group.querySelectorAll('.status-btn');
    const complaintId = group.getAttribute('data-complaint-id');
    const statusMsg = group.querySelector('.status-msg');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            const newStatus = button.getAttribute('data-status');

            // Send AJAX request
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `complaint_id=${complaintId}&status=${encodeURIComponent(newStatus)}`
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === 'success') {
                    // Update button styles
                    buttons.forEach(btn => btn.style.backgroundColor = '');
                    button.style.backgroundColor = '#444';
                    button.style.color = 'white';

                    // Show confirmation
                    statusMsg.textContent = "Status updated!";
                    setTimeout(() => statusMsg.textContent = '', 3000);

                    // Refresh the page after 300ms
                    setTimeout(() => {
                        location.reload();
                    }, 300);
                } else {
                    statusMsg.textContent = "Failed to update.";
                }
            });
        });
    });
});

window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(function(modal) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    });
};

// Handle close button click (×) - no page refresh here
document.querySelectorAll('.modal .close').forEach(closeBtn => {
    closeBtn.addEventListener('click', () => {
        const modal = closeBtn.closest('.modal');
        modal.style.display = "none";
    });
});

</script>
</main>
</div>
</body>
</html>