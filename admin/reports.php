<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Total parents
$total_parents = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'parent'")->fetch_assoc()['total'];

// Fees summary
$total_fees = $conn->query("SELECT 
    SUM(CASE WHEN status = 'Paid' THEN amount ELSE 0 END) AS total_paid,
    SUM(CASE WHEN status = 'Pending' THEN amount ELSE 0 END) AS total_pending,
    COUNT(CASE WHEN status = 'Paid' THEN 1 END) AS paid_count,
    COUNT(CASE WHEN status = 'Pending' THEN 1 END) AS pending_count
 FROM fees")->fetch_assoc();
?>

<h2>Fee Reports & Analytics</h2>

<table border="1" cellpadding="10" cellspacing="0">
    <tr><th>Total Parents</th><td><?= $total_parents ?></td></tr>
    <tr><th>Total Amount Collected</th><td>Rs. <?= $total_fees['total_paid'] ?: 0 ?></td></tr>
    <tr><th>Total Amount Pending</th><td>Rs. <?= $total_fees['total_pending'] ?: 0 ?></td></tr>
    <tr><th>Fees Paid</th><td><?= $total_fees['paid_count'] ?> students</td></tr>
    <tr><th>Fees Pending</th><td><?= $total_fees['pending_count'] ?> students</td></tr>
</table>

<br>
<a href="dashboard.php">Back to Dashboard</a>
