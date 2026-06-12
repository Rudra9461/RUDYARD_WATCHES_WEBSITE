<?php
require_once('config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form.html');
    exit;
}

$fname    = trim($_POST['fname']    ?? '');
$lname    = trim($_POST['lname']    ?? '');
$contact  = trim($_POST['contact']  ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$address  = trim($_POST['address']  ?? '');
$pin_code = trim($_POST['pin_code'] ?? '');

$errors = [];
if (empty($fname))                              $errors[] = "First name is required.";
if (empty($lname))                              $errors[] = "Last name is required.";
if (!preg_match('/^\d{10}$/', $contact))        $errors[] = "Enter a valid 10-digit contact number.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Enter a valid email address.";
if (strlen($password) < 6)                      $errors[] = "Password must be at least 6 characters.";
if (empty($address))                            $errors[] = "Address is required.";
if (!preg_match('/^\d{6}$/', $pin_code))        $errors[] = "Enter a valid 6-digit PIN code.";

if (!empty($errors)) {
    echo "<script>alert('" . addslashes(implode('\n', $errors)) . "'); window.history.back();</script>";
    exit;
}

$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    echo "<script>alert('This email is already registered.'); window.history.back();</script>";
    exit;
}
$check->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO users (fname, lname, contact, email, password, address, pin_code)
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssss", $fname, $lname, $contact, $email, $hashed, $address, $pin_code);

if ($stmt->execute()) {
    header('Location: index.html?registered=1');
    exit;
} else {
    echo "<script>alert('Something went wrong. Please try again.'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>