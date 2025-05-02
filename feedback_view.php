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
// Get the admin username for the logged-in user
$admin_user = $_SESSION['admin_user'];
$username = $admin_user['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback View</title>
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
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
            <div class="container">
               <h1>Feedback List</h1>
                    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Whatsapp</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Submitted</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="feedback-row" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['client_name']) ?>"
                                data-email="<?= htmlspecialchars($row['client_email']) ?>" data-phone="<?= htmlspecialchars($row['phone']) ?>"
                                data-title="<?= htmlspecialchars($row['feedback_title']) ?>" data-desc="<?= htmlspecialchars($row['feedback_desc']) ?>"
                                data-submitted="<?= $row['submitted_at'] ?>">
                                <td><?= htmlspecialchars($row['client_name']) ?></td>
                                <td><?= htmlspecialchars($row['client_email']) ?></td>
                                <td><?= htmlspecialchars($row['phone']) ?></td>
                                <td><?= htmlspecialchars($row['feedback_title']) ?></td>
                                <td><?= htmlspecialchars($row['feedback_desc']) ?></td>
                                <td><?= $row['submitted_at'] ?></td>
                                <td class="action-buttons">
                                 <a href="?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Delete this feedback?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
      
    </main>
</div>

<!-- Modal -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Feedback Details</h2>
        <p><strong>Name:</strong> <span id="modalName"></span></p>
        <p><strong>Email:</strong> <span id="modalEmail"></span></p>
        <p><strong>Whatsapp:</strong> <span id="modalPhone"></span></p>
        <p><strong>Title:</strong> <span id="modalTitle"></span></p>
        <p><strong>Description:</strong> <span id="modalDesc"></span></p>
        <p><strong>Submitted At:</strong> <span id="modalSubmitted"></span></p>
    </div>
</div>

<!-- JavaScript -->
<script>
    var modal = document.getElementById("feedbackModal");
    var span = document.getElementsByClassName("close")[0];
    var rows = document.querySelectorAll(".feedback-row");

    rows.forEach(function(row) {
        row.addEventListener("click", function() {
            document.getElementById("modalName").textContent = row.getAttribute("data-name");
            document.getElementById("modalEmail").textContent = row.getAttribute("data-email");
            document.getElementById("modalPhone").textContent = row.getAttribute("data-phone");
            document.getElementById("modalTitle").textContent = row.getAttribute("data-title");
            document.getElementById("modalDesc").textContent = row.getAttribute("data-desc");
            document.getElementById("modalSubmitted").textContent = row.getAttribute("data-submitted");
            modal.style.display = "block";
        });
    });

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>
