<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$message = '';
$error = '';

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "UPDATE orders SET order_status = '$status' WHERE order_id = $order_id";
    
    if ($conn->query($sql)) {
        $message = 'Order status updated successfully!';
    } else {
        $error = 'Failed to update order status';
    }
}

// Get all orders
$orders = $conn->query("SELECT o.*, u.full_name, u.email FROM orders o 
                        JOIN users u ON o.user_id = u.user_id 
                        ORDER BY o.order_date DESC");

$page_title = 'Manage Orders - Admin';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-shopping-cart"></i> Manage Orders</h1>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="admin-layout">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <nav class="admin-nav">
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_products.php">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="manage_users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="manage_orders.php" class="active">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                </nav>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                <div class="admin-section-box">
                    <h2>All Orders</h2>
                    
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($order['email']); ?></small>
                                        </td>
                                        <td><?php echo formatPrice($order['total_amount']); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <select name="status" onchange="this.form.submit()" class="status-select">
                                                    <option value="Pending" <?php echo $order['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                    <option value="Processing" <?php echo $order['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                    <option value="Shipped" <?php echo $order['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                    <option value="Delivered" <?php echo $order['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                    <option value="Cancelled" <?php echo $order['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                </select>
                                                <input type="hidden" name="update_status">
                                            </form>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <button class="btn-sm btn-primary view-order" data-order-id="<?php echo $order['order_id']; ?>">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="order-details" id="order-<?php echo $order['order_id']; ?>" style="display: none;">
                                        <td colspan="6">
                                            <div class="order-details-box">
                                                <h4>Order Items</h4>
                                                <?php
                                                $items_sql = "SELECT oi.*, p.product_name, p.image FROM order_items oi 
                                                              JOIN products p ON oi.product_id = p.product_id 
                                                              WHERE oi.order_id = " . $order['order_id'];
                                                $items = $conn->query($items_sql);
                                                ?>
                                                <table class="items-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Image</th>
                                                            <th>Product</th>
                                                            <th>Quantity</th>
                                                            <th>Price</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($item = $items->fetch_assoc()): ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="admin-product-thumb">
                                                                        <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                                             alt="thumb"
                                                                             onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder.jpg';">
                                                                    </div>
                                                                </td>
                                                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                                                <td><?php echo $item['quantity']; ?></td>
                                                                <td><?php echo formatPrice($item['price']); ?></td>
                                                                <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                                            </tr>
                                                        <?php endwhile; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No orders found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.querySelectorAll('.view-order').forEach(button => {
    button.addEventListener('click', function() {
        const orderId = this.dataset.orderId;
        const detailsRow = document.getElementById('order-' + orderId);
        
        if (detailsRow.style.display === 'none') {
            detailsRow.style.display = 'table-row';
            this.textContent = 'Hide';
            this.classList.add('active');
        } else {
            detailsRow.style.display = 'none';
            this.textContent = 'View';
            this.classList.remove('active');
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
