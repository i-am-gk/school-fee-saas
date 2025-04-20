<?php
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center m-3'>Registration successful. <a href='login.php'>Login here</a></div>";
    } else {
        echo "<div class='alert alert-danger text-center m-3'>Error: " . $stmt->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - School Fee SaaS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #e0f7fa, #e8f5e9);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border: none;
      border-radius: 1rem;
      background: #ffffff;
    }
    .form-control, .form-select {
      border-radius: 0.75rem;
    }
    .btn-success {
      border-radius: 0.75rem;
      padding: 10px;
      font-weight: 600;
    }
    .text-center a {
      color: #28a745;
      text-decoration: none;
    }
    .text-center a:hover {
      text-decoration: underline;
    }

    @media (max-width: 576px) {
      .card {
        padding: 2rem 1rem !important;
      }
      h2 {
        font-size: 1.5rem;
      }
      h5 {
        font-size: 1rem;
      }
    }
  </style>
</head>

<body>

<div class="container d-flex align-items-center justify-content-center min-vh-100">
  <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
    <div class="card shadow-lg p-4">
      <h2 class="text-center text-primary fw-bold mb-1">🏫 School Fee Submission SaaS</h2>
      <h5 class="text-center text-success mb-4">Create Your Account</h5>
      <form method="POST">
        <div class="mb-3">
          <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email Address" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="mb-4">
          <select name="role" class="form-select" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="parent">Parent</option>
          </select>
        </div>
        <button type="submit" class="btn btn-success w-100">Register</button>
      </form>
      <p class="mt-3 mb-0 text-center">Already have an account? <a href="login.php">Login here</a></p>
    </div>
  </div>
</div>

</body>
</html>
