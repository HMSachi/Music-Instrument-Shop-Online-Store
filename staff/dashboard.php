<?php
include '../includes/db_connection.php';
include '../includes/session.php';
include '../includes/product_manager.php';
include '../includes/order_manager.php';

require_staff();

$pm = new ProductManager($db);
$om = new OrderManager($db);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_stock') {
        $result = $pm->update_product($_POST['product_id'], ['stock' => $_POST['stock']]);
        $message = $result['message'];
    } elseif ($_POST['action'] === 'update_order_status') {
        $result = $om->update_order_status($_POST['order_id'], $_POST['status']);
        $message = $result['message'];
    }
}

$products = $pm->get_products();
$orders = $om->get_all_orders();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Melody Masters</title>
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="/staff/dashboard.php">Dashboard</a></li>
                <li><a href="/public/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Staff Dashboard</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo (strpos($message, 'successfully') !== false || strpos($message, 'updated') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Stock Management -->
        <div class="card mb-3">
            <h2>Inventory Management</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Update Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td>
                                <form method="POST" action="" style="display: flex; gap: 0.5rem;">
                                    <input type="hidden" name="action" value="update_stock">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" min="0" style="width: 80px;">
                                    <button type="submit" class="btn btn-primary btn-small">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Order Management -->
        <div class="card">
            <h2>Order Processing</h2>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Order Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                            <td>£<?php echo number_format($order['total_price'], 2); ?></td>
                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="update_order_status">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="status" onchange="this.form.submit();" style="padding: 0.5rem;">
                                        <option value="Pending" <?php echo ($order['status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo ($order['status'] === 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo ($order['status'] === 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo ($order['status'] === 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo ($order['status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <a href="/staff/order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary btn-small">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
