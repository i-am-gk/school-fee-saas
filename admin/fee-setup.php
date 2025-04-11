<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $parent_id = $_POST['parent_id'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];
    $reference_no = 'FEE' . strtoupper(uniqid());

    $stmt = $conn->prepare("INSERT INTO fees (user_id, amount, due_date, reference_no) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idss", $parent_id, $amount, $due_date, $reference_no);
    $stmt->execute();
}


// Fetch all parents
$parents = $conn->query("SELECT id, name FROM users WHERE role = 'parent'");
?>

<h2>Assign Fee to Parent</h2>
<form method="POST">
    <select name="parent_id" required>
        <option value="">Select Parent</option>
        <?php while ($p = $parents->fetch_assoc()) { ?>
            <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
        <?php } ?>
    </select><br>
    <input type="number" name="amount" placeholder="Amount" required><br>
    <input type="date" name="due_date" required><br>
    <button type="submit">Assign Fee</button>
</form>
<a href="dashboard.php">Back to Dashboard</a>
