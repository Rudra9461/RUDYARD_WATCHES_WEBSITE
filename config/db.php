<?php
$conn = new mysqli('sql107.infinityfree.com', 'if0_42170791', '04102005Rudra', 'if0_42170791_rudyard');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>