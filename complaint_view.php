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
$complaints_per_page = 6;
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
.complaint-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
}

.complaint-table th {
    background-color: #374151;
    color: white;
    text-align: left;
    padding: 12px;
}

.complaint-table td {
    padding: 12px;
    background-color: white;
    border-top: 1px solid #ddd;
}

.status-label {
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: bold;
    display: inline-block;
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
// Assuming $conn and $result are already available from earlier code

if ($result->num_rows > 0) {
    echo "<table class='complaint-table'>";
    echo "<thead><tr>
        <th>Customer</th>
        <th>Incident Date</th>
        <th>Description</th>
        <th>Phone</th>
        <th>Staff</th>
        <th>Audio</th>
        <th>Image</th>
        <th>Status</th>
    </tr></thead><tbody>";

    while ($row = $result->fetch_assoc()) {
        $complaint_id = $row['id'];
        $modal_id = "modal_" . $complaint_id;
        $status = htmlspecialchars($row['status']);
        $badge_color = 'gray';
        if ($status == 'Pending') $badge_color = 'orange';
        elseif ($status == 'Working') $badge_color = 'blue';
        elseif ($status == 'Completed') $badge_color = 'green';

        // Fetch staff names
        $staff_names = "None";
        $staff_ids_string = $row['staff_ids'];
        if (!empty($staff_ids_string)) {
            $staff_ids_array = array_filter(array_map('intval', explode(',', $staff_ids_string)));
            if (!empty($staff_ids_array)) {
                $staff_ids_list = implode(',', $staff_ids_array);
                $staff_sql = "SELECT name FROM staff WHERE id IN ($staff_ids_list)";
                $staff_result = $conn->query($staff_sql);
                if ($staff_result && $staff_result->num_rows > 0) {
                    $staff_names_array = [];
                    while ($staff_row = $staff_result->fetch_assoc()) {
                        $staff_names_array[] = htmlspecialchars($staff_row['name']);
                    }
                    $staff_names = implode(', ', $staff_names_array);
                }
            }
        }

        // Fetch audio file
        $audio_sql = "SELECT file_path FROM complaint_files WHERE complaint_id = $complaint_id AND (file_path LIKE '%.mp3' OR file_path LIKE '%.webm')";
        $audio_result = $conn->query($audio_sql);
        $audio_link = "None";
        if ($audio_result && $audio_result->num_rows > 0) {
            $audio_row = $audio_result->fetch_assoc();
            $audio_file = htmlspecialchars($audio_row['file_path']);
            $audio_link = "<audio controls src='$audio_file'></audio>";
        }

        // Image thumbnail
        $img_thumb = "None";
        $thumb_sql = "SELECT file_path FROM complaint_files WHERE complaint_id = $complaint_id";
        $thumb_result = $conn->query($thumb_sql);
        if ($thumb_result && $thumb_result->num_rows > 0) {
            while ($thumb_row = $thumb_result->fetch_assoc()) {
                $file = htmlspecialchars($thumb_row['file_path']);
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $img_thumb = "<a href='$file' target='_blank'><img src='$file' style='max-height:50px; max-width:50px; border-radius:5px;'></a>";
                    break;
                }
            }
        }

        // Table Row
        echo "<tr onclick=\"openModal('$modal_id')\">";
        echo "<td>" . htmlspecialchars($row['client_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['preferred_date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['complaint_desc']) . "</td>";
        echo "<td>" . htmlspecialchars($row['client_phone']) . "</td>";
        echo "<td>" . $staff_names . "</td>";
        echo "<td>" . $audio_link . "</td>";
        echo "<td>$img_thumb</td>";
        echo "<td><span class='status-label' style='background-color: $badge_color; padding:4px 8px; border-radius:4px; color:white;'>" . $status . "</span></td>";
        echo "</tr>";

        // Modal Popup
        echo "<div id='$modal_id' class='modal' style='display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color: rgba(0,0,0,0.5); z-index:1000;'>";
        echo "<div class='modal-content' style='background:white; margin:5% auto; padding:20px; position:relative; width:60%; border-radius:10px;'>";

        // Close Button
        echo "<span class='close' onclick=\"closeModal('$modal_id')\" style='font-size: 24px; font-weight: bold; color: #ff0000; position: absolute; top: 10px; right: 15px; cursor: pointer;'>&times;</span>";

        // Modal Content
        echo "<h3>Complaint Details</h3>";
        echo "<p><strong>Submit Date & Time:</strong> " . htmlspecialchars($row['created_at']) . "</p>";
        echo "<p><strong>Customer:</strong> " . htmlspecialchars($row['client_name']) . " (" . htmlspecialchars($row['client_email']) . ")</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['client_phone']) . "</p>";
        echo "<p><strong>Description:</strong> " . nl2br(htmlspecialchars($row['complaint_desc'])) . "</p>";
        echo "<p><strong>Date:</strong> " . htmlspecialchars($row['preferred_date']) . "</p>";
        echo "<p><strong>Staff:</strong> " . $staff_names . "</p>";
        echo "<p><strong>Status:</strong> <span style='color:$badge_color; font-weight:bold;'>$status</span></p>";

        // Status Change Form
        echo "<form method='post' action='update_status.php' style='margin: 10px 0; padding: 0;'> 
            <input type='hidden' name='complaint_id' value='$complaint_id'>";
        $statuses = ['Pending' => 'orange', 'Working' => 'blue', 'Completed' => 'green'];
        foreach ($statuses as $s => $color) {
            $active_style = $s === $status 
                ? "background-color: $color; color: white; font-weight: bold;" 
                : "background-color: lightgray; color: black;";
            echo "<button type='submit' name='status' value='$s' style='margin-right:5px; padding:5px 10px; border:none; border-radius:5px; $active_style'>$s</button>";
        }
        echo "</form><br>";

        // Display Comments
        $comment_sql = "SELECT comment, created_at FROM complaint_comments WHERE complaint_id = $complaint_id ORDER BY created_at DESC";
        $comment_result = $conn->query($comment_sql);
        if ($comment_result && $comment_result->num_rows > 0) {
            echo "<br><br><div><strong>Comments:</strong><ul style='padding-left:20px;'>";
            while ($comment_row = $comment_result->fetch_assoc()) {
                echo "<li>" . nl2br(htmlspecialchars($comment_row['comment'])) . 
                     " <small>(" . $comment_row['created_at'] . ")</small></li>";
            }
            echo "</ul></div>";
        }

        // Display Uploaded Images
        $image_sql = "SELECT file_path FROM complaint_files WHERE complaint_id = $complaint_id AND (file_path LIKE '%.jpg' OR file_path LIKE '%.png' OR file_path LIKE '%.jpeg' OR file_path LIKE '%.gif')";
        $image_result = $conn->query($image_sql);
        if ($image_result && $image_result->num_rows > 0) {
            echo "<br><div><strong>Uploaded Images:</strong><br>";
            while ($img_row = $image_result->fetch_assoc()) {
                $img_path = htmlspecialchars($img_row['file_path']);
                echo "<a href='$img_path' target='_blank'><img src='$img_path' style='max-height:40px; margin:3px; border-radius:4px;'></a>";
            }
            echo "</div>";
        }

        // Comment & Upload Form
        echo "<form action='add_comment_file.php' method='POST' enctype='multipart/form-data' style='margin-top:10px; padding: 0;'>
            <input type='hidden' name='complaint_id' value='$complaint_id'>
            <label><strong>Add Comment:</strong></label><br>
            <textarea name='comment' rows='2' style='width:100%; border-radius:4px; margin-bottom:6px;'></textarea><br>
            <label><strong>Upload Image:</strong></label><br>
            <input type='file' name='file' accept='.jpg,.jpeg,.png,.gif' style='margin-bottom:8px;'><br>
            <button type='submit' style='background:#007BFF; color:white; padding:5px 10px; border:none; border-radius:5px;'>Submit</button>
        </form>";

        echo "</div></div>"; // End of modal content and modal box
    }

    echo "</tbody></table>";
} else {
    echo "<p>No complaints found.</p>";
}

$conn->close();
?>


<!-- JavaScript for modal functionality -->
<script>
    // Open modal
    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    // Close modal
    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

    // Close modal when clicking anywhere outside the modal content
    window.onclick = function(event) {
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    }
</script>

<!-- Pagination -->
<div class="pagination" style="margin-top: 20px; text-align:center;">
    <?php if ($page > 1) { echo "<a href='?page=" . ($page - 1) . "'>&laquo; Prev</a> "; } ?>
    <span> Page <?php echo $page; ?> of <?php echo $total_pages; ?> </span>
    <?php if ($page < $total_pages) { echo " <a href='?page=" . ($page + 1) . "'>Next &raquo;</a>"; } ?>
</div>


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