<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get username from session if available
$username = isset($_SESSION['admin_user']['username']) ? $_SESSION['admin_user']['username'] : 'Admin';
?>

<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2><i class="fas fa-user-shield"></i></h2>
        <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
    </div>
    <ul class="sidebar-nav">
        <li><a href="admin_portal.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="feedback_view.php"><i class="fas fa-comments"></i> Feedback</a></li>
        <li><a href="complaint_view.php"><i class="fas fa-exclamation-circle"></i> Complaints</a></li>

   <li class="has-submenu">
    <a href="#" class="submenu-toggle">
        <i class="fas fa-users"></i> Staff &nbsp;  &nbsp;     <i class="fas fa-caret-down submenu-arrow"></i>
    </a>
    <ul class="submenu">
        <li><a href="add_staff.php">Add Staff</a></li>
        <li><a href="view_staff.php">View Staff</a></li>
    </ul>
</li>

        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const submenuToggles = document.querySelectorAll('.submenu-toggle');

    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function (e) {
            e.preventDefault();
            const parentLi = this.parentElement;
            parentLi.classList.toggle('active');
        });
    });
});
</script>

