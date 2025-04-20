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

// Payment log
$payments = $conn->query("SELECT 
    payments.*, 
    users.name AS parent_name, 
    fees.amount, 
    fees.reference_no 
FROM payments 
JOIN fees ON payments.fee_id = fees.id 
JOIN users ON fees.user_id = users.id 
ORDER BY payments.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Reports - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    .card { border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    .table th, .table td { vertical-align: middle; }
    .table-striped tbody tr:nth-of-type(odd) { background-color: #f9f9f9; }
    .table thead { background-color: #007bff; color: white; }
    .table-bordered { border: 1px solid #ddd; }
    .container { max-width: 1000px; }
    .navbar { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
  </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container-fluid">
    <span class="navbar-brand">Admin - Reports & Analytics</span>
    <div class="d-flex">
      <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
      <a href="../logout.php" class="btn btn-outline-light ms-2">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3 class="text-center mb-4">Fee Reports & Analytics</h3>

  <div class="card p-4 mb-4">
    <h5>Fee Summary</h5>
    <table class="table table-bordered table-striped">
      <tr>
        <th>Total Parents</th>
        <td><?= $total_parents ?></td>
      </tr>
      <tr>
        <th>Total Amount Collected</th>
        <td>Rs. <?= $total_fees['total_paid'] ?: 0 ?></td>
      </tr>
      <tr>
        <th>Total Amount Pending</th>
        <td>Rs. <?= $total_fees['total_pending'] ?: 0 ?></td>
      </tr>
      <tr>
        <th>Fees Paid</th>
        <td><?= $total_fees['paid_count'] ?> student(s)</td>
      </tr>
      <tr>
        <th>Fees Pending</th>
        <td><?= $total_fees['pending_count'] ?> student(s)</td>
      </tr>
    </table>
  </div>

  <div class="card p-4">
    <h5>Payment Transactions (Audit Log)</h5>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>#</th>
          <th>Reference No</th>
          <th>Parent</th>
          <th>Amount</th>
          <th>Payment Time</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        while ($row = $payments->fetch_assoc()):
        ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= htmlspecialchars($row['reference_no']) ?></td>
          <td><?= htmlspecialchars($row['parent_name']) ?></td>
          <td>Rs. <?= $row['amount'] ?></td>
          <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>
