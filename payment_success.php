<?php
session_start();
require_once('config/db.php');
require_once('config/razorpay.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html?error=loginrequired');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: cart.php');
    exit;
}

$razorpay_payment_id = $_POST['razorpay_payment_id'] ?? '';
$razorpay_order_id   = $_POST['razorpay_order_id']   ?? '';
$razorpay_signature  = $_POST['razorpay_signature']  ?? '';
$local_order_id      = (int) ($_POST['local_order_id'] ?? 0);

// ── Verify the payment signature — THIS is what stops anyone from faking a "successful" payment ──
// Razorpay signs order_id + "|" + payment_id with our secret key (HMAC SHA256).
// If we recompute that same signature and it doesn't match what they sent back,
// the request did not genuinely come from Razorpay and must be rejected.
$generated_signature = hash_hmac(
    'sha256',
    $razorpay_order_id . '|' . $razorpay_payment_id,
    RAZORPAY_KEY_SECRET
);

if (!hash_equals($generated_signature, $razorpay_signature)) {
    // Signature mismatch — do NOT mark the order as paid
    $stmt = $conn->prepare("UPDATE orders SET status = 'failed' WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $local_order_id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    die("Payment verification failed. If money was deducted, it will be refunded automatically. Please contact support.");
}

// Signature is valid — mark the order as paid
$stmt = $conn->prepare("UPDATE orders SET status = 'paid', razorpay_payment_id = ? WHERE id = ? AND user_id = ?");
$stmt->bind_param("sii", $razorpay_payment_id, $local_order_id, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

// Clear the cart now that payment is confirmed
$_SESSION['cart'] = [];

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Order Confirmed — Rudyard Watches</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assest/css/style.css">
</head>
<body style="background:var(--navy);min-height:100vh;display:flex;align-items:center;justify-content:center;">

<div style="text-align:center;color:#fff;max-width:480px;padding:40px;">
  <div style="font-size:60px;color:#4caf87;margin-bottom:20px;">✓</div>
  <h1 style="font-family:'Playfair Display',serif;margin-bottom:12px;">Order Confirmed</h1>
  <p style="color:var(--muted);margin-bottom:8px;">Thank you for your purchase.</p>
  <p style="color:var(--muted);font-size:13px;margin-bottom:32px;">
    Payment ID: <?= htmlspecialchars($razorpay_payment_id) ?>
  </p>
  <a href="index.php" class="rw-gold-btn" style="display:inline-block;">Continue Shopping</a>
</div>

</body>
</html>