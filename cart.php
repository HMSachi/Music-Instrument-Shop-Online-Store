<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

require_customer();

$base = BASE_PATH;
$pm = new ProductManager($db);

$cart = $_SESSION['cart'] ?? [];
$cart_items = [];
$subtotal = 0;

foreach ($cart as $product_id => $quantity) {
    $product = $pm->get_product($product_id);
    if ($product) {
        $item_total = $product['price'] * $quantity;
        $cart_items[] = [
            'product_id' => $product_id,
            'product_name' => $product['product_name'],
            'price' => $product['price'],
            'quantity' => $quantity,
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
        $shipping = 10; // Flat rate
    }
}

$total = $subtotal + $shipping;

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $new_qty = (int)$_POST['quantity'];

    if ($new_qty <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else {
        $_SESSION['cart'][$product_id] = $new_qty;
    }

    header('Location: ' . $base . '/cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Melody Masters</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/products.php">Products</a></li>
                <?php if (is_logged_in()): ?>
                    <li><a href="<?php echo $base; ?>/customer/dashboard.php">My Account</a></li>
                    <li><a href="<?php echo $base; ?>/cart.php">Cart (<?php echo count($cart_items); ?>)</a></li>
                    <li><a href="<?php echo $base; ?>/public/logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
            <div class="card">
                <p>Your cart is empty.</p>
                <a href="<?php echo $base; ?>/products.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin: 2rem 0;">
                <!-- Cart Items -->
                <div>
                    <?php foreach ($cart_items as $item): ?>
                        <div class="card" style="margin-bottom: 1rem; display: grid; grid-template-columns: 120px 1fr 120px; gap: 1.5rem; align-items: start;">
                            <!-- Image -->
                            <div style="height: 120px; background: #F7F9FA; border-radius: 4px; overflow: hidden;">
                                <?php if (!empty($item['image'])): ?>
                                    <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">No Image</div>
                                <?php endif; ?>
                            </div>

                            <!-- Details -->
                            <div>
                                <h3><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                <p style="color: #7F8C8D; margin: 0.5rem 0;">Price: $<?php echo number_format($item['price'], 2); ?></p>
                                <form method="POST" action="" style="display: flex; gap: 0.5rem; align-items: center; margin-top: 0.75rem;">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <label for="qty_<?php echo $item['product_id']; ?>" style="margin: 0;">Qty:</label>
                                    <input type="number" id="qty_<?php echo $item['product_id']; ?>" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width: 60px; padding: 0.4rem;">
                                    <button type="submit" class="btn btn-secondary btn-small">Update</button>
                                </form>
                            </div>

                            <!-- Price -->
                            <div style="text-align: right;">
                                <div style="font-size: 1.2rem; font-weight: 700; color: var(--accent);">
                                    $<?php echo number_format($item['item_total'], 2); ?>
                                </div>
                                <a href="<?php echo $base; ?>/remove_from_cart.php?id=<?php echo $item['product_id']; ?>" style="color: #C0392B; text-decoration: none; font-size: 0.9rem; display: block; margin-top: 0.5rem;">Remove</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Summary -->
                <div class="card" style="height: fit-content;">
                    <h3>Order Summary</h3>
                    <div style="border-bottom: 1px solid #D6DDE3; padding-bottom: 1rem; margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Subtotal:</span>
                            <span>$<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Shipping:</span>
                            <span><?php echo ($shipping > 0) ? '$' . number_format($shipping, 2) : 'Free'; ?></span>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem;">
                        <span>Total:</span>
                        <span style="color: var(--accent);">$<?php echo number_format($total, 2); ?></span>
                    </div>
                    <a href="<?php echo $base; ?>/checkout.php" class="btn btn-primary" style="display: block; text-align: center; width: 100%; padding: 0.75rem;">Proceed to Checkout</a>
                    <a href="<?php echo $base; ?>/products.php" class="btn btn-secondary" style="display: block; text-align: center; width: 100%; padding: 0.75rem; margin-top: 0.75rem;">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
