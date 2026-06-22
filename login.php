<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once('config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
    header('Location: login.html?error=invalid');
    exit;
}

$stmt = $conn->prepare("SELECT id, fname, lname, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: login.html?error=invalid');
    exit;
}

$user = $result->fetch_assoc();

if (!password_verify($password, $user['password'])) {
    header('Location: login.html?error=invalid');
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['fname']   = $user['fname'];
$_SESSION['lname']   = $user['lname'];

$stmt->close();
$conn->close();

header('Location: index.php?login=1');
exit;
?>