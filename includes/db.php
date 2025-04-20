<?php
$host = 'localhost';
$user = 'root';
$pass = '12345';
$dbname = 'school_fee_saas';

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
