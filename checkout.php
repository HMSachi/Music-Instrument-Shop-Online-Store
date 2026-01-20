<?php
include 'includes/db_connection.php';
include 'includes/session.php';
include 'includes/order_manager.php';

require_customer();

$om = new OrderManager($db);
$cart_details = $om->get_cart_details();
$message = '';

if (empty($cart_details['items'])) {
    header("Location: /cart.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['place_order'])) {
        $result = $om->create_order(
            $_SESSION['user_id'],
            $_POST['delivery_address'],
            $_POST['phone']
        );
        
        if ($result['success']) {
            header("Location: /order_confirmation.php?order_id=" . $result['order_id']);
            exit();
        } else {
            $message = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Melody Masters</title>
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="/products.php">Products</a></li>
                <li><a href="/customer/dashboard.php">My Account</a></li>
                <li><a href="/cart.php">🛒 Cart</a></li>
                <li><a href="/public/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Checkout</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <!-- Checkout Form -->
            <div>
                <div class="card">
                    <h2>Delivery Information</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="delivery_address">Delivery Address</label>
                            <textarea id="delivery_address" name="delivery_address" required placeholder="Enter your full delivery address..."></textarea>
                        </div>

                        <button type="submit" name="place_order" class="btn btn-success" style="width: 100%; padding: 1rem; font-size: 1.1rem;">
                            Place Order
                        </button>
                    </form>
                </div>
            </div>

            <!-- Order Summary -->
            <div>
                <div class="card">
                    <h2>Order Summary</h2>
                    
                    <div style="max-height: 400px; overflow-y: auto; margin-bottom: 2rem; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                        <?php foreach ($cart_details['items'] as $item): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--light-bg);">
                                <div>
                                    <p style="font-weight: 500;"><?php echo htmlspecialchars($item['name']); ?></p>
                                    <small style="color: var(--light-text);">Qty: <?php echo $item['quantity']; ?></small>
                                </div>
                                <p style="font-weight: 500;">£<?php echo number_format($item['item_total'], 2); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-bottom: 1rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Subtotal:</span>
                            <strong>£<?php echo number_format($cart_details['subtotal'], 2); ?></strong>
                        </div>
                        
                        <?php if ($cart_details['has_physical']): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Shipping:</span>
                                <strong>
                                    <?php if ($cart_details['shipping'] === 0): ?>
                                        FREE
                                    <?php else: ?>
                                        £<?php echo number_format($cart_details['shipping'], 2); ?>
                                    <?php endif; ?>
                                </strong>
                            </div>
                        <?php endif; ?>
                        
                        <div style="display: flex; justify-content: space-between; font-size: 1.2rem; padding-top: 1rem; border-top: 2px solid var(--border-color);">
                            <strong>Total:</strong>
                            <strong style="color: var(--secondary-color);">£<?php echo number_format($cart_details['total'], 2); ?></strong>
                        </div>
                    </div>

                    <a href="/cart.php" class="btn btn-outline" style="width: 100%; text-align: center; display: block;">← Back to Cart</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
