<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isParent()) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get all pending fees
$fees = $conn->query("SELECT id, amount, due_date, reference_no FROM fees WHERE user_id = $user_id AND status = 'Pending'");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fee_id = $_POST['fee_id'];
    $method = $_POST['payment_method'];

    // Insert payment
    $ref = $_POST['ref_display']; // Comes from the selected fee
    $stmt = $conn->prepare("INSERT INTO payments (fee_id, payment_method, reference_no) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $fee_id, $method, $ref);
    $stmt->execute();

    // Update fee status
    $conn->query("UPDATE fees SET status = 'Paid' WHERE id = $fee_id");
    echo "<p style='color:green;'>Payment successful for Reference: $ref</p>";
    // Reload to reflect update
    header("Refresh:2");
}
?>

<h2>Pay Your Fees</h2>

<?php if ($fees->num_rows > 0) { ?>
    <form method="POST">
        <label>Select Fee Reference:</label><br>
        <select name="fee_id" required onchange="updateRef(this)">
            <option value="">Select Reference</option>
            <?php while ($f = $fees->fetch_assoc()) { ?>
                <option value="<?= $f['id'] ?>" data-ref="<?= $f['reference_no'] ?>">
                    <?= $f['reference_no'] ?> - Rs. <?= $f['amount'] ?> (Due: <?= $f['due_date'] ?>)
                </option>
            <?php } ?>
        </select><br><br>

        <label>Selected Reference:</label><br>
        <input type="text" id="ref_display" name="ref_display" readonly required><br><br>

        <label>Payment Method:</label><br>
        <select name="payment_method" required>
            <option value="">Select</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Bank Transfer">Bank Transfer</option>
        </select><br><br>

        <button type="submit">Pay Now</button>
    </form>

    <script>
    function updateRef(select) {
        const selectedOption = select.options[select.selectedIndex];
        const ref = selectedOption.getAttribute('data-ref');
        document.getElementById('ref_display').value = ref || '';
    }
    </script>
<?php } else { ?>
    <p>No pending fees to pay.</p>
<?php } ?>

<a href="dashboard.php">Back to Dashboard</a>
