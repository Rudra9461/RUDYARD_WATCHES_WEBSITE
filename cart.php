<?php
session_start();
require_once('config/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html?error=loginrequired');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$grandTotal = 0;

if (!empty($cart)) {
    $ids = array_map('intval', array_keys($cart));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("SELECT id, name, brand, price, image_path FROM products WHERE id IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $qty = $cart[$row['id']];
        $subtotal = $row['price'] * $qty;
        $grandTotal += $subtotal;
        $cartItems[] = [
            'id'       => $row['id'],
            'name'     => $row['name'],
            'brand'    => $row['brand'],
            'price'    => $row['price'],
            'image'    => $row['image_path'],
            'qty'      => $qty,
            'subtotal' => $subtotal
        ];
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Your Cart — Rudyard Watches</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

<div class="container" style="max-width:900px;padding:60px 20px;">
  <h1 style="font-family:'Playfair Display',serif;color:var(--navy);margin-bottom:8px;">Your Cart</h1>
  <p style="color:#777;margin-bottom:36px;">Review your selected timepieces before checkout.</p>

  <?php if (empty($cartItems)): ?>
    <div style="text-align:center;padding:60px 20px;background:#fff;border-radius:12px;">
      <p style="font-size:18px;color:#777;margin-bottom:20px;">Your cart is empty.</p>
      <a href="index.php#collection" class="rw-gold-btn" style="display:inline-block;">Browse Watches</a>
    </div>
  <?php else: ?>

    <div style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.06);">
      <?php foreach ($cartItems as $item): ?>
        <div style="display:flex;align-items:center;gap:20px;padding:20px;border-bottom:1px solid #f0ece4;">
          <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width:90px;height:90px;object-fit:cover;border-radius:8px;">
          <div style="flex:1;">
            <p style="font-size:11px;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;color:var(--gold);margin:0 0 4px;"><?= htmlspecialchars($item['brand']) ?></p>
            <h5 style="font-family:'Playfair Display',serif;margin:0 0 6px;color:var(--navy);"><?= htmlspecialchars($item['name']) ?></h5>
            <p style="margin:0;color:#777;font-size:14px;">₹<?= number_format($item['price'], 2) ?> each</p>
          </div>

          <form action="cart_actions.php" method="POST" style="display:flex;align-items:center;gap:8px;">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
            <input type="number" name="qty" value="<?= $item['qty'] ?>" min="1" max="20" style="width:60px;padding:6px;border:1px solid #ddd;border-radius:6px;text-align:center;">
            <button type="submit" style="background:var(--navy);color:#fff;border:none;border-radius:6px;padding:6px 12px;font-size:13px;cursor:pointer;">Update</button>
          </form>

          <p style="font-weight:700;color:var(--navy);font-family:'Playfair Display',serif;min-width:110px;text-align:right;">₹<?= number_format($item['subtotal'], 2) ?></p>

          <form action="cart_actions.php" method="POST">
            <input type="hidden" name="action" value="remove">
            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
            <button type="submit" style="background:none;border:none;color:#e07070;font-size:18px;cursor:pointer;" title="Remove">&times;</button>
          </form>
        </div>
      <?php endforeach; ?>

      <div style="display:flex;justify-content:space-between;align-items:center;padding:24px;background:var(--navy);">
        <span style="color:var(--muted);font-size:15px;">Grand Total</span>
        <span style="color:var(--gold);font-size:24px;font-weight:700;font-family:'Playfair Display',serif;">₹<?= number_format($grandTotal, 2) ?></span>
      </div>
    </div>

    <div style="margin-top:28px;display:flex;justify-content:space-between;">
      <a href="index.php#collection" style="color:var(--navy);text-decoration:none;font-weight:600;">&larr; Continue Shopping</a>
      <a href="checkout.php" class="rw-gold-btn" style="display:inline-block;">Proceed to Checkout</a>
    </div>

  <?php endif; ?>
</div>

</body>
</html>