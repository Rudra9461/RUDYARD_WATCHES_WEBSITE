<?php
// IMPORTANT: Replace 'YOUR_PASSWORD_HERE' with your actual MySQL root password
$conn = new mysqli('localhost', 'root', 'Rudra@1234', 'rudyard_watches');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>