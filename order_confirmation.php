<?php
include 'includes/db_connection.php';
include 'includes/session.php';
include 'includes/order_manager.php';

require_customer();

$om = new OrderManager($db);

if (!isset($_GET['order_id'])) {
    header("Location: /customer/dashboard.php");
    exit();
}

$order = $om->get_order($_GET['order_id'], $_SESSION['user_id']);

if (!$order) {
    header("Location: /customer/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Melody Masters</title>
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
        <div style="text-align: center; max-width: 700px; margin: 3rem auto;">
            <div class="card">
                <div style="font-size: 3rem; margin-bottom: 1rem;">✓</div>
                <h1 style="color: var(--success-color); margin-bottom: 1rem;">Order Confirmed!</h1>
                <p style="font-size: 1.1rem; margin-bottom: 2rem;">Thank you for your purchase.</p>

                <div style="background-color: var(--light-bg); padding: 2rem; border-radius: 8px; margin-bottom: 2rem; text-align: left;">
                    <h3>Order Details</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin: 1.5rem 0;">
                        <div>
                            <p style="color: var(--light-text); font-size: 0.9rem;">Order Number</p>
                            <p style="font-size: 1.3rem; font-weight: bold;">#{<?php echo $order['id']; ?></p>
                        </div>
                        <div>
                            <p style="color: var(--light-text); font-size: 0.9rem;">Order Date</p>
                            <p style="font-size: 1.3rem; font-weight: bold;"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
                        </div>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                        <h4>Items Ordered</h4>
                        <?php foreach ($order['items'] as $item): ?>
                            <div style="display: flex; justify-content: space-between; padding: 0.75rem 0;">
                                <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo $item['quantity']; ?></span>
                                <span>£<?php echo number_format($item['quantity'] * $item['price'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem; display: flex; justify-content: space-between; font-size: 1.2rem;">
                        <strong>Total Amount:</strong>
                        <strong style="color: var(--secondary-color);">£<?php echo number_format($order['total_price'], 2); ?></strong>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1.5rem;">
                        <p style="color: var(--light-text); font-size: 0.9rem;">Delivery Address</p>
                        <p><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                    </div>
                </div>

                <p style="color: var(--light-text); margin-bottom: 2rem;">
                    We've sent a confirmation email to your registered email address. You can track your order from your account dashboard.
                </p>

                <div style="display: flex; gap: 1rem;">
                    <a href="/customer/dashboard.php" class="btn btn-primary" style="flex: 1;">View Order History</a>
                    <a href="/products.php" class="btn btn-secondary" style="flex: 1;">Continue Shopping</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
