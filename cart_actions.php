<?php
session_start();
require_once('config/db.php');

// Must be logged in to use the cart
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html?error=loginrequired');
    exit;
}

$action     = $_POST['action']     ?? $_GET['action']     ?? '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : (isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0);

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {

    case 'add':
        if ($product_id > 0) {
            // Confirm the product actually exists before adding (avoid fake IDs)
            $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]++;
                } else {
                    $_SESSION['cart'][$product_id] = 1;
                }
                $_SESSION['cart_message'] = '✓ Added to cart!';
            }
            $stmt->close();
        }
        break;

    case 'remove':
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        break;

    case 'update':
        $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
        if ($product_id > 0) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id] = $qty;
            }
        }
        break;

    case 'clear':
        $_SESSION['cart'] = [];
        break;
}

$conn->close();

// Redirect back to wherever made sense — cart page for most actions.
// Only allow known internal pages, to prevent open-redirect abuse.
$requestedRedirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'cart.php';
$allowedRedirects = ['cart.php', 'index.php', 'index.php#collection', 'index.php#pricing'];
$redirect = in_array($requestedRedirect, $allowedRedirects, true) ? $requestedRedirect : 'cart.php';
header('Location: ' . $redirect);
exit;
?>