<?php
require 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Registration successful. <a href='login.php'>Login here</a>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<form method="POST">
    <input type="text" name="name" required placeholder="Full Name"><br>
    <input type="email" name="email" required placeholder="Email"><br>
    <input type="password" name="password" required placeholder="Password"><br>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="admin">Admin</option>
        <option value="parent">Parent</option>
    </select><br>
    <button type="submit">Register</button>
</form>
