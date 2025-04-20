<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_users'])) {
    foreach ($_POST['selected_users'] as $fee_id) {
        // Fetch fee details again to ensure safety
        $stmt = $conn->prepare("
            SELECT u.id AS user_id, u.email, f.amount, f.due_date
            FROM users u 
            JOIN fees f ON u.id = f.user_id 
            WHERE f.id = ?
        ");
        $stmt->bind_param("i", $fee_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $message = "Reminder: Rs. {$row['amount']} is due by {$row['due_date']}. Please make your payment.";

        $insert = $conn->prepare("INSERT INTO reminders (user_id, fee_id, message) VALUES (?, ?, ?)");
        $insert->bind_param("iis", $row['user_id'], $fee_id, $message);
        $insert->execute();
    }

    $success = true;
}

// Fetch all parents with pending fees
$sql = "
    SELECT u.id AS user_id, u.name, u.email, f.id AS fee_id, f.amount, f.due_date 
    FROM users u 
    JOIN fees f ON u.id = f.user_id 
    WHERE f.status = 'Pending'
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Send Reminders - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .navbar {
      background-color: #007bff;
    }
    .container {
      margin-top: 30px;
    }
    .list-group-item {
      background-color: #ffffff;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 10px;
      transition: all 0.3s ease-in-out;
    }
    .list-group-item:hover {
      background-color: #e9ecef;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    .alert {
      font-size: 1.2rem;
    }
    .card-header {
      background-color: #007bff;
      color: white;
      font-size: 1.2rem;
      text-align: center;
      border-radius: 8px 8px 0 0;
    }
    .card-body {
      padding: 20px;
    }
    .btn-primary {
      background-color: #007bff;
      border: none;
      font-weight: bold;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <span class="navbar-brand">Admin - Fee Reminders</span>
    <div class="d-flex">
      <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
      <a href="../logout.php" class="btn btn-outline-light ms-2">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <div class="card">
    <div class="card-header">
      <h4>Send Reminder to Selected Parents</h4>
    </div>
    <div class="card-body">
      <?php if (isset($success)) { ?>
        <div class="alert alert-success text-center">Reminders sent successfully!</div>
      <?php } ?>

      <?php if ($result->num_rows > 0) { ?>
        <form method="POST">
          <div class="list-group">
            <?php while ($row = $result->fetch_assoc()) { ?>
              <label class="list-group-item shadow-sm">
                <input type="checkbox" name="selected_users[]" value="<?= $row['fee_id'] ?>" class="form-check-input me-2">
                <strong><?= $row['name'] ?> (<?= $row['email'] ?>)</strong><br>
                Rs. <?= $row['amount'] ?> due by <?= $row['due_date'] ?>
              </label>
            <?php } ?>
          </div>
          <button type="submit" class="btn btn-primary mt-3">Send Selected Reminders</button>
        </form>
      <?php } else { ?>
        <div class="alert alert-info text-center">
          All parents are up to date. No reminders to send.
        </div>
      <?php } ?>
    </div>
  </div>
</div>

</body>
</html>
