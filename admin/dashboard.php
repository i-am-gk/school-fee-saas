<?php
require '../includes/session.php';
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}
echo "Welcome, Admin " . $_SESSION['name'];
?>
<a href="reports.php">View Reports & Analytics</a> |
<a href="fee-setup.php">Assign Fee</a>
<a href="send-reminders.php">Send Fee Reminders</a>
<a href="../logout.php">Logout</a>
