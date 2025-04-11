<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Fetch all parents with pending fees
$sql = "
    SELECT u.name, u.email, f.amount, f.due_date 
    FROM users u 
    JOIN fees f ON u.id = f.user_id 
    WHERE f.status = 'Pending'
";

$result = $conn->query($sql);

echo "<h2>Sending Reminders to Parents</h2>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Simulated email reminder
        $message = "
            Hello " . $row['name'] . ",\n
            This is a reminder to pay the pending fee of Rs. " . $row['amount'] . " due by " . $row['due_date'] . ".\n
            Please login to your Parent Dashboard to make the payment.
        ";

        echo "<div style='margin-bottom: 15px; padding: 10px; border: 1px solid #ccc;'>";
        echo "<strong>Email to:</strong> " . $row['email'] . "<br>";
        echo nl2br($message);
        echo "</div>";

        // You can uncomment this on a live server:
        // mail($row['email'], "Fee Payment Reminder", $message);
    }
} else {
    echo "All parents are up to date. No reminders needed.";
}
?>
<a href="dashboard.php">Back to Dashboard</a>
