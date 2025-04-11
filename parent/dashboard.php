<?php
require '../includes/session.php';
if (!isLoggedIn() || !isParent()) {
    header("Location: ../login.php");
    exit;
}
echo "Welcome, Parent " . $_SESSION['name'];
?>
<a href="../logout.php">Logout</a>
