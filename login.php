<?php
require 'includes/db.php';
require 'includes/session.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Check if password matches the plain text password stored in the database
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];

            // Redirect based on user role
            if ($email === 'admin@gmail.com' && $password == 'admin123') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: parent/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - School Fee SaaS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #f1f8e9, #e3f2fd);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      border: none;
      border-radius: 1rem;
      background: #ffffff;
    }
    .form-control {
      border-radius: 0.75rem;
    }
    .btn-primary {
      border-radius: 0.75rem;
      padding: 10px;
      font-weight: 600;
    }
    .text-center a {
      color: #007bff;
      text-decoration: none;
    }
    .text-center a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

<div class="container d-flex align-items-center justify-content-center min-vh-100">
  <div class="col-md-5">
    <div class="card shadow-lg p-4">
      <h2 class="text-center text-primary fw-bold mb-1">🏫 School Fee Submission SaaS</h2>
      <h5 class="text-center text-primary mb-4">Welcome Back</h5>
      
      <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>

      <form method="POST">
        <div class="mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email address" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
      </form>

      <p class="mt-3 text-center">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
  </div>
</div>

</body>
</html>
