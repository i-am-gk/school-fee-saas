<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isParent()) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all PAID fees and payment details
$sql = "
    SELECT f.amount, f.due_date, f.status, 
           p.payment_method, p.reference_no, p.payment_date
    FROM fees f
    JOIN payments p ON f.id = p.fee_id
    WHERE f.user_id = $user_id
    ORDER BY p.payment_date DESC
";

$result = $conn->query($sql);
?>

<h2>Your Paid Fee Receipts</h2>

<?php if ($result->num_rows > 0) { ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Reference No</th>
            <th>Payment Date</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td>Rs. <?= $row['amount'] ?></td>
                <td><?= $row['due_date'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['payment_method'] ?></td>
                <td><?= $row['reference_no'] ?></td>
                <td><?= $row['payment_date'] ?></td>
            </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <p>No payments found.</p>
<?php } ?>

<a href="dashboard.php">Back to Dashboard</a>
