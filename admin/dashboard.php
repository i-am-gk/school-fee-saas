<?php 
require '../includes/session.php';
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login.php");
    exit;
}

// Database connection
require '../includes/db.php';

// Fetch data for User Activity Summary
$reminders_sent_count = $conn->query("SELECT COUNT(*) AS reminders_sent FROM reminders WHERE is_read = 0")->fetch_assoc()['reminders_sent'];
$fees_paid_count = $conn->query("SELECT COUNT(*) AS fees_paid FROM fees WHERE status = 'Paid'")->fetch_assoc()['fees_paid'];
$fees_pending_count = $conn->query("SELECT COUNT(*) AS fees_pending FROM fees WHERE status = 'Pending'")->fetch_assoc()['fees_pending'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - School Fee SaaS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    .dashboard-heading {
      font-weight: bold;
      color: #0d6efd;
      font-size: 2rem;
    }
    .btn {
      border-radius: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn:hover {
      transform: translateY(-5px);
    }
    .card {
      border-radius: 0.75rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .card-body {
      text-align: center;
    }
    .icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #fff;
    }
    .container {
      max-width: 1200px;
    }
    .navbar {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold">🏫School Fee Submission SaaS - Admin</span>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-3"> <!-- Reduced margin-top -->
  <h3 class="text-center mb-2 dashboard-heading">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h3>
  <p class="text-center text-muted mb-4">Manage fees, reminders, and reports</p>

  <!-- User Activity Summary -->
  <div class="row g-4 justify-content-center mt-3"> <!-- Reduced margin-top -->
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <i class="bi bi-bell icon"></i> <!-- Bell icon for reminders -->
          <h5 class="card-title">Reminders Sent</h5>
          <p class="card-text"><?= $reminders_sent_count ?> reminders</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <i class="bi bi-credit-card icon"></i> <!-- Credit card icon for fees paid -->
          <h5 class="card-title">Fees Paid</h5>
          <p class="card-text"><?= $fees_paid_count ?> payments</p>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <i class="bi bi-exclamation-circle icon"></i> <!-- Exclamation icon for pending fees -->
          <h5 class="card-title">Pending Payments</h5>
          <p class="card-text"><?= $fees_pending_count ?> payments</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Action Cards -->
  <div class="row g-4 justify-content-center mt-3"> <!-- Reduced margin-top -->
    <!-- Fee Setup Card -->
    <div class="col-md-3">
      <div class="card bg-success text-white">
        <div class="card-body">
          <i class="bi bi-credit-card icon"></i>
          <h5 class="card-title">Assign Fees</h5>
          <p class="card-text">Set up and assign fees for the parents</p>
          <a href="fee-setup.php" class="btn btn-light w-100 p-3 shadow-sm">Go to Setup</a>
        </div>
      </div>
    </div>

    <!-- Send Reminders Card -->
    <div class="col-md-3">
      <div class="card bg-warning text-white">
        <div class="card-body">
          <i class="bi bi-bell icon"></i>
          <h5 class="card-title">Send Reminders</h5>
          <p class="card-text">Send reminders to parents with pending fees</p>
          <a href="send-reminders.php" class="btn btn-light w-100 p-3 shadow-sm">Send Now</a>
        </div>
      </div>
    </div>

    <!-- View Reports Card -->
    <div class="col-md-3">
      <div class="card bg-info text-white">
        <div class="card-body">
          <i class="bi bi-bar-chart-line icon"></i>
          <h5 class="card-title">View Reports</h5>
          <p class="card-text">Analyze payment data and generate reports</p>
          <a href="reports.php" class="btn btn-light w-100 p-3 shadow-sm">View Reports</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
