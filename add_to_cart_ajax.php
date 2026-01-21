<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output
ini_set('log_errors', 1);

require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

header('Content-Type: application/json');

try {
    $base = BASE_PATH;
    $pm = new ProductManager($db);

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

    // Prepare response with updated cart data
    $cart = $_SESSION['cart'];
    $cart_items = [];
    $subtotal = 0;

    foreach ($cart as $pid => $qty) {
        $product = $pm->get_product($pid);
        if ($product) {
            $item_total = $product['price'] * $qty;
            $cart_items[] = [
                'product_id' => $pid,
                'product_name' => $product['product_name'],
                'price' => $product['price'],
                'quantity' => $qty,
                'item_total' => $item_total,
                'image' => $product['image'],
                'type' => $product['product_type']
            ];
            $subtotal += $item_total;
        }
    }

    // Calculate shipping
    $shipping = 0;
    if (count($cart_items) > 0) {
        $has_physical = false;
        foreach ($cart_items as $item) {
            if ($item['type'] === 'physical') {
                $has_physical = true;
                break;
            }
        }
        if ($has_physical && $subtotal > 0) {
            $shipping = 10;
        }
    }

    $total = $subtotal + $shipping;

    echo json_encode([
        'success' => true,
        'message' => 'Item added to cart',
        'cart_items' => $cart_items,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'total' => $total,
        'cart_count' => count($cart_items)
    ]);
    exit();
}

echo json_encode([
    'success' => false,
    'message' => 'Invalid request'
]);
exit();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
    exit();
}
?>
