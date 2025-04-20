<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}

$success = false;
$reference_no = "";

// Assign fee to parent
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $parent_id = $_POST['parent_id'];
    $amount = $_POST['amount'];
    $due_date = $_POST['due_date'];

    // Validate amount
    $today = date('Y-m-d');

    if ($amount <= 0) {
        $error = "Amount must be a positive value.";
    } elseif ($due_date < $today) {
        $error = "Due date cannot be in the past.";
    }    
    else {
        $reference_no = 'FEE' . strtoupper(uniqid());

        $stmt = $conn->prepare("INSERT INTO fees (user_id, amount, due_date, reference_no) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $parent_id, $amount, $due_date, $reference_no);

        if ($stmt->execute()) {
            $success = true; // ✅ flag to show success and start JS redirect
        } else {
            $error = "Failed to assign fee. Please try again.";
        }
    }
}

// Fetch all parents
$parents = $conn->query("SELECT id, name FROM users WHERE role = 'parent'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Assign Fees - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
    }
    .card {
      border-radius: 1rem;
      max-width: 600px;
      margin: 10px auto;
    }
    .btn {
      border-radius: 0.75rem;
      font-weight: 600;
      padding: 10px 15px;
    }
    .dashboard-heading {
      color: #0d6efd;
      font-weight: bold;
    }
    .form-select, .form-control {
      border-radius: 0.75rem;
    }
    .container {
      padding-top: 2px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">🏫 School Fee Submission SaaS - Admin</span>
    <div class="d-flex">
      <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
      <a href="../logout.php" class="btn btn-outline-light ms-2">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <h3 class="text-center mb-4 dashboard-heading">Assign Fee to Parent</h3>

  <!-- Success message -->
  <?php if ($success): ?>
    <div class="alert alert-success text-center">
      ✅ Fee assigned successfully!<br>
      Reference No: <strong><?= htmlspecialchars($reference_no) ?></strong><br>
      Redirecting to dashboard...
    </div>
    <script>
      setTimeout(() => {
        window.location.href = "dashboard.php";
      }, 4000); // 4 seconds
    </script>
  <?php endif; ?>

  <!-- Error message -->
  <?php if (isset($error)): ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
  <?php endif; ?>

  <!-- Form -->
  <form method="POST" class="card p-4 shadow-sm">
    <div class="mb-3">
      <label>Select Parent</label>
      <select name="parent_id" class="form-select" required>
        <option value="">Select Parent</option>
        <?php while ($p = $parents->fetch_assoc()) { ?>
            <option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
        <?php } ?>
      </select>
    </div>

    <div class="mb-3">
      <label>Amount (PKR)</label>
      <input type="number" name="amount" class="form-control" min="1" required>
    </div>

    <div class="mb-3">
      <label>Due Date</label>
      <input type="date" name="due_date" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-success w-100">Assign Fee</button>
  </form>
</div>

</body>
</html>
