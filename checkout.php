<?php
session_start();
require_once('config/db.php');
require_once('config/razorpay.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html?error=loginrequired');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

// Fetch cart items + compute total (server-side, never trust client totals)
$ids = array_map('intval', array_keys($cart));
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$types = str_repeat('i', count($ids));

$stmt = $conn->prepare("SELECT id, name, brand, price, image_path FROM products WHERE id IN ($placeholders)");
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];
$grandTotal = 0;
while ($row = $result->fetch_assoc()) {
    $qty = $cart[$row['id']];
    $subtotal = $row['price'] * $qty;
    $grandTotal += $subtotal;
    $cartItems[] = array_merge($row, ['qty' => $qty, 'subtotal' => $subtotal]);
}
$stmt->close();

// Get the user's address from signup
$stmt = $conn->prepare("SELECT fname, lname, email, contact, address, pin_code FROM users WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Razorpay amount is in paise (smallest currency unit), so multiply by 100
$amountInPaise = (int) round($grandTotal * 100);

// Create the Razorpay order via their API
$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_USERPWD, RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'amount'   => $amountInPaise,
    'currency' => 'INR',
    'receipt'  => 'rcpt_' . $_SESSION['user_id'] . '_' . time()
]));
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    $conn->close();
    die("Sorry, we couldn't start the checkout process. Please try again later.");
}

$razorpayOrder = json_decode($response, true);

// Save a pending order in our own database, so we have a record even before payment completes
$stmt = $conn->prepare("INSERT INTO orders (user_id, razorpay_order_id, total_amount, status, shipping_address) VALUES (?, ?, ?, 'created', ?)");
$shippingAddress = $user['address'] . ', PIN: ' . $user['pin_code'];
$stmt->bind_param("isds", $_SESSION['user_id'], $razorpayOrder['id'], $grandTotal, $shippingAddress);
$stmt->execute();
$localOrderId = $stmt->insert_id;
$stmt->close();

// Save line items for this order
$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
foreach ($cartItems as $item) {
    $stmt->bind_param("iisdi", $localOrderId, $item['id'], $item['name'], $item['price'], $item['qty']);
    $stmt->execute();
}
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout — Rudyard Watches</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assest/css/style.css">
</head>
<body style="background:var(--body-bg);min-height:100vh;">

<nav class="navbar navbar-expand-md navbar-dark rw-nav sticky-top">
  <div class="container-fluid px-4">
    <a href="index.php" class="navbar-brand">
      <img src="assest/images/images-removebg-preview.png" height="52" alt="Rudyard Watches">
    </a>
  </div>
</nav>

<div class="container" style="max-width:700px;padding:60px 20px;">
  <h1 style="font-family:'Playfair Display',serif;color:var(--navy);margin-bottom:8px;">Checkout</h1>
  <p style="color:#777;margin-bottom:36px;">Confirm your order details below before paying.</p>

  <div style="background:#fff;border-radius:12px;padding:28px;margin-bottom:24px;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
    <h6 style="font-family:'Playfair Display',serif;color:var(--navy);margin-bottom:16px;">Order Summary</h6>
    <?php foreach ($cartItems as $item): ?>
      <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #f0ece4;font-size:14px;">
        <span><?= htmlspecialchars($item['brand'] . ' — ' . $item['name']) ?> &times; <?= $item['qty'] ?></span>
        <span>₹<?= number_format($item['subtotal'], 2) ?></span>
      </div>
    <?php endforeach; ?>
    <div style="display:flex;justify-content:space-between;padding-top:16px;font-weight:700;color:var(--navy);font-size:18px;">
      <span>Total</span>
      <span>₹<?= number_format($grandTotal, 2) ?></span>
    </div>
  </div>

  <div style="background:#fff;border-radius:12px;padding:28px;margin-bottom:24px;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
    <h6 style="font-family:'Playfair Display',serif;color:var(--navy);margin-bottom:16px;">Shipping To</h6>
    <p style="margin:0;font-size:14px;color:#555;line-height:1.7;">
      <?= htmlspecialchars($user['fname'] . ' ' . $user['lname']) ?><br>
      <?= htmlspecialchars($user['address']) ?>, PIN: <?= htmlspecialchars($user['pin_code']) ?><br>
      <?= htmlspecialchars($user['contact']) ?> · <?= htmlspecialchars($user['email']) ?>
    </p>
  </div>

  <button id="payBtn" class="rw-submit-btn" style="width:100%;">Pay ₹<?= number_format($grandTotal, 2) ?></button>
  <p style="text-align:center;font-size:12px;color:#999;margin-top:12px;">
    Test mode — no real payment will be charged.
  </p>
</div>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
  var options = {
    "key": "<?= RAZORPAY_KEY_ID ?>",
    "amount": "<?= $amountInPaise ?>",
    "currency": "INR",
    "name": "Rudyard Watches",
    "description": "Order Payment",
    "order_id": "<?= $razorpayOrder['id'] ?>",
    "prefill": {
      "name": "<?= htmlspecialchars($user['fname'] . ' ' . $user['lname'], ENT_QUOTES) ?>",
      "email": "<?= htmlspecialchars($user['email'], ENT_QUOTES) ?>",
      "contact": "<?= htmlspecialchars($user['contact'], ENT_QUOTES) ?>"
    },
    "theme": { "color": "#c9a84c" },
    "handler": function (response) {
      // Send payment confirmation details to our backend for verification
      var form = document.createElement('form');
      form.method = 'POST';
      form.action = 'payment_success.php';

      function addField(name, value) {
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = value;
        form.appendChild(input);
      }
      addField('razorpay_payment_id', response.razorpay_payment_id);
      addField('razorpay_order_id', response.razorpay_order_id);
      addField('razorpay_signature', response.razorpay_signature);
      addField('local_order_id', '<?= $localOrderId ?>');

      document.body.appendChild(form);
      form.submit();
    },
    "modal": {
      "ondismiss": function () {
        alert('Payment was cancelled. Your order has not been completed.');
      }
    }
  };

  document.getElementById('payBtn').addEventListener('click', function () {
    var rzp = new Razorpay(options);
    rzp.open();
  });
</script>

</body>
</html>