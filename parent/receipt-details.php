<?php
require '../includes/db.php';
require '../includes/session.php';

if (!isLoggedIn() || !isParent()) {
    header("Location: ../login.php");
    exit;
}

if (isset($_GET['ref'])) {
    $reference_no = $_GET['ref']; // Get the reference number from the URL

    // Fetch the payment details using the reference number
    $sql = "
        SELECT f.amount, f.due_date, f.status, 
               p.payment_method, p.reference_no, p.created_at
        FROM fees f
        JOIN payments p ON f.id = p.fee_id
        WHERE p.reference_no = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reference_no);
    $stmt->execute();
    $result = $stmt->get_result();

    // If no result found, show an error message
    if ($result->num_rows == 0) {
        echo "<div class='alert alert-danger text-center mt-4'>No receipt found with that reference number.</div>";
        exit;
    }

    $receipt = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger text-center mt-4'>Reference number is missing in the URL.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Receipt Details - Parent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <span class="navbar-brand">Parent - Receipt Details</span>
    <div class="d-flex">
      <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
      <a href="../logout.php" class="btn btn-outline-light ms-2">Logout</a>
    </div>
  </div>
</nav>

<div class="container mt-4">
  <h3 class="text-center mb-4">Receipt Details</h3>

  <div class="card shadow-sm">
    <div class="card-body">
      <h5 class="card-title text-success">Fee Receipt</h5>
      <p class="card-text mb-1"><strong>Amount:</strong> Rs. <?= $receipt['amount'] ?></p>
      <p class="card-text mb-1"><strong>Due Date:</strong> <?= $receipt['due_date'] ?></p>
      <p class="card-text mb-1"><strong>Status:</strong> <?= ucfirst($receipt['status']) ?></p>
      <p class="card-text mb-1"><strong>Payment Method:</strong> <?= $receipt['payment_method'] ?></p>
      <p class="card-text mb-1"><strong>Reference No:</strong> <?= $receipt['reference_no'] ?></p>
      <p class="card-text"><strong>Payment Date:</strong> <?= date('d M Y, h:i A', strtotime($receipt['created_at'])) ?></p>
    </div>
  </div>

  <a href="receipt.php" class="btn btn-primary mt-4">← Back to Receipts</a>
</div>

</body>
</html>
