<?php
session_start();
if (!isset($_SESSION['admin_user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal</title>
    <link rel="stylesheet" href="admin_styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php
include 'db.php';

// Fetch counts from the database
$complaint_count = $conn->query("SELECT COUNT(*) FROM complaints")->fetch_row()[0];
$feedback_count = $conn->query("SELECT COUNT(*) FROM feedback")->fetch_row()[0];
$staff_count = $conn->query("SELECT COUNT(*) FROM staff")->fetch_row()[0];
$file_count = $conn->query("SELECT COUNT(*) FROM complaint_files")->fetch_row()[0];

// Get the admin username for the logged-in user
$admin_user = $_SESSION['admin_user'];
$username = $admin_user['username'];
?>

<div class="admin-wrapper">
    <!-- Sidebar -->
    <aside class="admin-sidebar">
    <?php include 'sidebar.php'; ?>
    </aside>

    <!-- Main content -->
    <main class="admin-main">
        <div class="admin-header">
            <h1>Welcome to Admin Portal</h1>
        </div>

        <!-- Dashboard Stats -->
        <div class="dashboard-cards">
            <div class="card">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <h2><?= $complaint_count ?></h2>
                    <p>Total Complaints</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-comments"></i>
                <div>
                    <h2><?= $feedback_count ?></h2>
                    <p>Total Feedback</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-users"></i>
                <div>
                    <h2><?= $staff_count ?></h2>
                    <p>Registered Staff</p>
                </div>
            </div>
            <div class="card">
                <i class="fas fa-file-alt"></i>
                <div>
                    <h2><?= $file_count ?></h2>
                    <p>Uploaded Files</p>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
