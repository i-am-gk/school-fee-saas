<?php
require '../includes/session.php';
require '../includes/db.php';

if (!isLoggedIn() || !isParent()) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch reminders only for unpaid fees
$sql = "
    SELECT r.message, r.sent_at 
    FROM reminders r
    JOIN fees f ON r.fee_id = f.id
    WHERE r.user_id = $user_id AND f.status = 'Pending'
";
$reminders = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parent Dashboard - School Fee SaaS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f7fc;
      font-family: 'Arial', sans-serif;
    }
    .dashboard-heading {
      font-weight: bold;
      color: #007bff;
      font-size: 2rem;
      margin-bottom: 1rem;
    }
    .intro-heading {
      font-size: 1.8rem;
      font-weight: 600;
      color: #343a40;
      margin-bottom: 15px; /* Reduced margin */
    }
    .intro-description {
      font-size: 1.1rem;
      color: #6c757d;
      margin-bottom: 25px; /* Reduced margin */
    }
    .card-custom {
      border-radius: 1rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
      padding: 2rem;
    }
    .btn-custom {
      border-radius: 0.75rem;
      font-weight: 600;
      padding: 15px;
      transition: all 0.3s ease;
    }
    .btn-warning-custom {
      background-color: #ffc107;
      border-color: #ffc107;
    }
    .btn-warning-custom:hover {
      background-color: #e0a800; /* Lighter shade of yellow */
      border-color: #e0a800;
    }
    .btn-info-custom {
      background-color: #17a2b8;
      border-color: #17a2b8;
    }
    .btn-info-custom:hover {
      background-color: #138496; /* Darker shade of blue */
      border-color: #138496;
    }
    .alert-warning-custom {
      font-size: 1.1rem;
      background-color: #fff3cd;
      color: #856404;
    }
    .container {
      max-width: 1000px;
      margin-top: -30px; /* Move content upwards */
    }
    .navbar {
      border-bottom: 2px solid #ddd;
    }
    .navbar-brand {
      font-weight: bold;
    }
    .card-header {
      font-weight: bold;
      background-color: #f8f9fa;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <span class="navbar-brand">School Fee SaaS - Parent</span>
    <div class="d-flex">
      <a href="../logout.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
  <!-- Professional Introduction Section -->
  <div class="text-center">
    <h2 class="intro-heading">Your Own Fee Payment System</h2>
    <p class="intro-description">
      Welcome to the School Fee SaaS platform, designed to make fee management more efficient and transparent.
    </p>
  </div>

  <h3 class="text-center dashboard-heading">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></h3>

  <?php if ($reminders->num_rows > 0) { ?>
    <div class="alert alert-warning alert-warning-custom">
      <strong>You have unpaid fee reminders:</strong>
      <ul class="mb-0">
        <?php while ($r = $reminders->fetch_assoc()) { ?>
          <li><?= htmlspecialchars($r['message']) ?> (<?= date('d M Y', strtotime($r['sent_at'])) ?>)</li>
        <?php } ?>
      </ul>
    </div>
  <?php } ?>

  <div class="row g-4 justify-content-center">
    <!-- Fee Payment Section -->
    <div class="col-md-4">
      <div class="card card-custom">
        <div class="card-header text-center">
          <h5 class="mb-0">Pay Fee</h5>
        </div>
        <div class="card-body">
          <a href="pay-fee.php" class="btn btn-warning-custom w-100 btn-custom">Pay Now</a>
        </div>
      </div>
    </div>
    
    <!-- View Receipts Section -->
    <div class="col-md-4">
      <div class="card card-custom">
        <div class="card-header text-center">
          <h5 class="mb-0">View Receipts</h5>
        </div>
        <div class="card-body">
          <a href="receipt.php" class="btn btn-info-custom w-100 btn-custom text-white">View Receipts</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
