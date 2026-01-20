<?php
include 'includes/db_connection.php';
include 'includes/session.php';
include 'includes/order_manager.php';

require_customer();

$om = new OrderManager($db);
$message = '';

// Handle remove from cart
if (isset($_GET['remove'])) {
    $result = $om->remove_from_cart($_GET['remove']);
    $message = $result['message'];
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_quantity'])) {
    $result = $om->update_cart_quantity($_POST['product_id'], $_POST['quantity']);
    $message = $result['message'];
}

$cart_details = $om->get_cart_details();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Melody Masters</title>
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
        <h1>Shopping Cart</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if (empty($cart_details['items'])): ?>
            <div class="card text-center">
                <h2>Your cart is empty</h2>
                <p>Start shopping to add items to your cart.</p>
                <a href="/products.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 3rem;">
                <!-- Cart Items -->
                <div>
                    <div class="card">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_details['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <a href="/product.php?id=<?php echo $item['product_id']; ?>" style="color: var(--secondary-color); text-decoration: none;">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </a>
                                            <?php if ($item['type'] === 'Digital'): ?>
                                                <small style="color: var(--light-text);"> (Digital)</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>£<?php echo number_format($item['price'], 2); ?></td>
                                        <td>
                                            <form method="POST" action="" style="display: inline-flex; gap: 0.5rem;">
                                                <input type="hidden" name="update_quantity" value="1">
                                                <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" style="width: 60px;">
                                                <button type="submit" class="btn btn-secondary btn-small">Update</button>
                                            </form>
                                        </td>
                                        <td>£<?php echo number_format($item['item_total'], 2); ?></td>
                                        <td>
                                            <a href="/cart.php?remove=<?php echo $item['product_id']; ?>" class="btn btn-warning btn-small" onclick="return confirm('Remove from cart?');">Remove</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Order Summary -->
                <div>
                    <div class="card">
                        <h2>Order Summary</h2>
                        <div style="margin: 2rem 0;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                                <span>Subtotal:</span>
                                <strong>£<?php echo number_format($cart_details['subtotal'], 2); ?></strong>
                            </div>
                            
                            <?php if ($cart_details['has_physical']): ?>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                                    <span>Shipping:</span>
                                    <strong>
                                        <?php if ($cart_details['shipping'] === 0): ?>
                                            FREE
                                        <?php else: ?>
                                            £<?php echo number_format($cart_details['shipping'], 2); ?>
                                        <?php endif; ?>
                                    </strong>
                                </div>
                                <?php if ($cart_details['shipping'] === 0): ?>
                                    <p style="color: var(--success-color); font-size: 0.9rem; margin-bottom: 1rem;">✓ Free shipping applied (over £100)</p>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <div style="display: flex; justify-content: space-between; font-size: 1.2rem;">
                                <strong>Total:</strong>
                                <strong style="color: var(--secondary-color);">£<?php echo number_format($cart_details['total'], 2); ?></strong>
                            </div>
                        </div>

                        <a href="/checkout.php" class="btn btn-primary" style="width: 100%; padding: 1rem; text-align: center; display: block; margin-bottom: 1rem;">
                            Proceed to Checkout
                        </a>
                        
                        <a href="/products.php" class="btn btn-secondary" style="width: 100%; padding: 1rem; text-align: center; display: block;">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
