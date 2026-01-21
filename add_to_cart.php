<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';

// Allow any user (logged in or not) to add to cart
// But redirect to login if they want to checkout

$base = BASE_PATH;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($quantity <= 0) $quantity = 1;

    // Initialize cart
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or update cart
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

header('Location: ' . $base . '/cart.php');
exit();
?>
