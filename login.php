<?php
require 'includes/db.php';
require 'includes/session.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];

            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: parent/dashboard.php");
            }
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" required placeholder="Email"><br>
    <input type="password" name="password" required placeholder="Password"><br>
    <button type="submit">Login</button>
</form>
