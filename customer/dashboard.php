<?php
include '../includes/db_connection.php';
include '../includes/session.php';
include '../includes/product_manager.php';
include '../includes/order_manager.php';

require_customer();

$pm = new ProductManager($db);
$om = new OrderManager($db);
$user = get_session_user();

// Get customer orders
$orders = $om->get_user_orders($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Melody Masters</title>
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="/products.php">Products</a></li>
                <li><a href="/cart.php">🛒 Cart</a></li>
                <li><a href="/public/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>My Account</h1>
        
        <!-- User Info -->
        <div class="card mb-3">
            <h2>Account Information</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['full_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Member Since:</strong> <?php echo date('M d, Y'); ?></p>
        </div>

        <!-- Order History -->
        <div class="card">
            <h2>Order History</h2>
            
            <?php if (empty($orders)): ?>
                <p>You haven't placed any orders yet.</p>
                <a href="/products.php" class="btn btn-primary">Start Shopping</a>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td>£<?php echo number_format($order['total_price'], 2); ?></td>
                                <td>
                                    <span style="padding: 0.5rem 1rem; border-radius: 4px; background-color: var(--light-bg);">
                                        <?php echo htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/customer/order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-small">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
