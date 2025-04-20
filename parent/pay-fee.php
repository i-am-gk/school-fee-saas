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
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pay Fee - Parent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
  <style>
    body {
      background-color: #f9f9f9;
    }
    .navbar {
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .container {
      max-width: 800px;
      margin-top: 50px;
    }
    .card {
      border-radius: 0.75rem;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .form-select, .form-control {
      border-radius: 0.5rem;
      padding: 1rem;
    }
    .btn-primary {
      border-radius: 0.75rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    .btn-primary:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
    }
    .alert {
      font-size: 1.1rem;
      margin-bottom: 30px;
    }
    .alert-success {
      background-color: #d4edda;
      border-color: #c3e6cb;
    }
    .alert-info {
      background-color: #d1ecf1;
      border-color: #bee5eb;
    }
    .card-title {
      font-weight: bold;
      font-size: 1.2rem;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-success">
  <div class="container-fluid">
    <span class="navbar-brand">Parent - Pay Fee</span>
    <div class="d-flex">
      <a href="dashboard.php" class="btn btn-outline-light">Dashboard</a>
      <a href="../logout.php" class="btn btn-outline-light ms-2">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <h3 class="text-center mb-4">Pay Your Pending Fees</h3>

  <div id="msgBox"></div>

  <?php if ($fees->num_rows > 0) { ?>
    <form id="paymentForm" class="card p-4">
      <div class="mb-3">
        <label>Select Fee Reference</label>
        <select name="fee_id" class="form-select" required onchange="updateRef(this)">
          <option value="">Select Reference</option>
          <?php while ($f = $fees->fetch_assoc()) { ?>
            <option value="<?= $f['id'] ?>" data-ref="<?= $f['reference_no'] ?>">
              <?= $f['reference_no'] ?> - Rs. <?= $f['amount'] ?> (Due: <?= $f['due_date'] ?>)
            </option>
          <?php } ?>
        </select>
      </div>

      <div class="mb-3">
        <label>Reference No</label>
        <input type="text" id="ref_display" name="ref_display" class="form-control" readonly required>
      </div>

      <div class="mb-3">
        <label>Payment Method</label>
        <select name="payment_method" class="form-select" required>
          <option value="">Select</option>
          <option value="Credit Card">Credit Card</option>
          <option value="Bank Transfer">Bank Transfer</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary w-100">Pay Now</button>
    </form>

    <script>
      function updateRef(select) {
        const ref = select.options[select.selectedIndex].getAttribute('data-ref');
        document.getElementById('ref_display').value = ref || '';
      }

      document.getElementById('paymentForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('process-payment.php', {
          method: 'POST',
          body: formData
        })
        .then(res => res.json())
        .then(data => {
          const msgBox = document.getElementById('msgBox');
          msgBox.innerHTML = `<div class='alert alert-${data.status === 'success' ? 'success' : 'danger'} text-center'>${data.message}</div>`;
          if (data.status === 'success') {
            setTimeout(() => window.location.reload(), 2000);
          }
        })
        .catch(() => {
          document.getElementById('msgBox').innerHTML = "<div class='alert alert-danger text-center'>An error occurred. Please try again.</div>";
        });
      });
    </script>

  <?php } else { ?>
    <div class="alert alert-info text-center">You have no pending fees to pay 🎉</div>
  <?php } ?>
</div>

</body>
</html>
