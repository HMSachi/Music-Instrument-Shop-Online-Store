<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';

require_customer();

$base = BASE_PATH;

// Accept both GET and POST for flexibility
$product_id = null;
if (isset($_GET['id'])) {
    $product_id = (int)$_GET['id'];
} elseif (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
}

if ($product_id && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

// Redirect back to referring page or cart
$redirect = $_POST['referrer'] ?? $_GET['referrer'] ?? $base . '/cart.php';
header('Location: ' . $redirect);
exit();
?>
