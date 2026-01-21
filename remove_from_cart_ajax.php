<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

header('Content-Type: application/json');

$base = BASE_PATH;
$pm = new ProductManager($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    // Remove from cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
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
        'message' => 'Item removed from cart',
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
?>
