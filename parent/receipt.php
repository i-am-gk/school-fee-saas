<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isParent()) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch all paid fees with payment details
$sql = "
   SELECT f.amount, f.due_date, f.status, 
       p.payment_method, p.reference_no, p.created_at
FROM fees f
JOIN payments p ON f.id = p.fee_id
WHERE f.user_id = $user_id
ORDER BY p.created_at DESC

";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Receipts - Parent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f4f9;
      font-family: Arial, sans-serif;
    }

    .navbar {
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .container {
      max-width: 1000px;
      margin-top: 40px;
    }

    .card {
      border-radius: 0.75rem;
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
      margin-bottom: 30px;
    }

    .card-title {
      font-weight: bold;
      font-size: 1.3rem;
      color: #333;
    }

    .btn-outline-dark {
      font-weight: 600;
    }

    .alert-info {
      font-size: 1.2rem;
    }

    table {
      width: 100%;
      margin-top: 30px;
      border-radius: 0.5rem;
      border-collapse: collapse;
      overflow: hidden;
    }

    th, td {
      padding: 1.2rem;
      text-align: center;
      border: 1px solid #ddd;
    }

    th {
      background-color: #f4f4f4;
      color: #555;
    }

    td {
      background-color: #fff;
      color: #333;
    }

    td:hover {
      background-color: #f9f9f9;
    }

    tr:nth-child(even) td {
      background-color: #f9f9f9;
    }

    .btn-view {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 0.5rem;
      font-weight: bold;
    }

    .btn-view:hover {
      background-color: #0056b3;
      cursor: pointer;
    }

    .table-responsive {
      overflow-x: auto;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <span class="navbar-brand">Parent - Fee Receipts</span>
    <div class="d-flex">
      <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
      <a href="../logout.php" class="btn btn-outline-light ms-2">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <h3 class="text-center mb-4">Your Payment Receipts</h3>

  <?php if ($result->num_rows > 0) { ?>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Amount</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Payment Method</th>
            <th>Reference No</th>
            <th>Payment Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td>Rs. <?= $row['amount'] ?></td>
              <td><?= $row['due_date'] ?></td>
              <td><?= ucfirst($row['status']) ?></td>
              <td><?= ucfirst($row['payment_method']) ?></td>
              <td><?= $row['reference_no'] ?></td>
              <td><?= $row['created_at'] ?></td>
              <td>
                <a href="receipt-details.php?ref=<?= $row['reference_no'] ?>" class="btn btn-view">View Details</a>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  <?php } else { ?>
    <div class="alert alert-info text-center">You haven't made any payments yet.</div>
  <?php } ?>
</div>

</body>
</html>
