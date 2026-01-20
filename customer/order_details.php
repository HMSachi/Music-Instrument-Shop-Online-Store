<?php
include '../includes/db_connection.php';
include '../includes/session.php';
include '../includes/order_manager.php';

require_customer();

$om = new OrderManager($db);

if (!isset($_GET['id'])) {
    header("Location: /customer/dashboard.php");
    exit();
}

$order = $om->get_order($_GET['id'], $_SESSION['user_id']);

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
    <title>Order Details - Melody Masters</title>
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
        <h1>Order #<?php echo $order['id']; ?></h1>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            <div>
                <div class="card">
                    <h2>Order Items</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td>
                                        <a href="/product.php?id=<?php echo $item['product_id']; ?>" style="color: var(--secondary-color); text-decoration: none;">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>£<?php echo number_format($item['price'], 2); ?></td>
                                    <td>£<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="card">
                    <h2>Order Status</h2>
                    <div style="padding: 1rem; background-color: var(--light-bg); border-radius: 4px; margin-bottom: 1.5rem;">
                        <p style="color: var(--light-text); font-size: 0.9rem;">Current Status</p>
                        <p style="font-size: 1.3rem; font-weight: bold; color: var(--secondary-color);">
                            <?php echo htmlspecialchars($order['status']); ?>
                        </p>
                    </div>
                    
                    <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></p>
                    <p><strong>Total Amount:</strong> £<?php echo number_format($order['total_price'], 2); ?></p>
                    
                    <hr style="margin: 1.5rem 0;">
                    
                    <h3>Delivery Address</h3>
                    <p><?php echo nl2br(htmlspecialchars($order['delivery_address'])); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <a href="/customer/dashboard.php" class="btn btn-secondary">Back to My Orders</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
