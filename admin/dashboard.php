<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if connection was successful
if (!$conn) {
    header("Location: " . SITE_URL . "/index.php");
    exit;
}

requireLogin();
requireAdmin();

// Get statistics
$stats = [];

// Total products
$products_result = preparedQuery($conn, "SELECT COUNT(*) as count FROM products");
$stats['products'] = $products_result->fetch_assoc()['count'];

// Total users
$users_result = preparedQuery($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
$stats['users'] = $users_result->fetch_assoc()['count'];

// Total orders
$orders_result = preparedQuery($conn, "SELECT COUNT(*) as count FROM orders");
$stats['orders'] = $orders_result->fetch_assoc()['count'];

// Total revenue
$revenue_result = preparedQuery($conn, "SELECT SUM(total_amount) as total FROM orders WHERE order_status != 'Cancelled'");
$stats['revenue'] = $revenue_result->fetch_assoc()['total'] ?? 0;

// Low stock items (stock <= 5)
$low_stock_query = "SELECT COUNT(*) as count FROM products WHERE stock <= ? AND product_type = 'physical'";
$low_stock_result = preparedQuery($conn, $low_stock_query, [5], "i");
$stats['low_stock'] = $low_stock_result->fetch_assoc()['count'];

$low_stock_items = preparedQuery($conn, "SELECT product_name, brand, stock FROM products WHERE stock <= ? AND product_type = 'physical' LIMIT 5", [5], "i");

// Recent orders
$recent_orders = preparedQuery($conn, "SELECT o.*, u.full_name FROM orders o 
                               JOIN users u ON o.user_id = u.user_id 
                               ORDER BY o.order_date DESC LIMIT 5");

$page_title = 'Admin Dashboard - Melody Masters';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-shield-alt"></i> Admin Dashboard</h1>
        </div>
        
        <div class="admin-layout animate-fade-in-up">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <nav class="admin-nav">
                    <a href="dashboard.php" class="active">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_products.php">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="manage_users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="manage_orders.php">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                </nav>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card animate-fade-in-up">
                        <div class="stat-icon" style="background: #3498db;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['products']; ?></h3>
                            <p>Total Products</p>
                        </div>
                    </div>
                    
                    <div class="stat-card animate-fade-in-up">
                        <div class="stat-icon" style="background: #2ecc71;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['users']; ?></h3>
                            <p>Total Customers</p>
                        </div>
                    </div>
                    
                    <div class="stat-card animate-fade-in-up">
                        <div class="stat-icon" style="background: #f39c12;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['orders']; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #e74c3c;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['low_stock']; ?></h3>
                            <p>Low Stock Items</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon" style="background: #9b59b6;">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo formatPrice($stats['revenue']); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
                
                <div class="admin-grid-two">
                    <!-- Recent Orders -->
                    <div class="admin-section-box">
                        <h2>Recent Orders</h2>
                        
                        <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td>#<?php echo $order['order_id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo strtolower($order['order_status']); ?>">
                                                        <?php echo $order['order_status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="manage_orders.php?view=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-primary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No orders yet</p>
                        <?php endif; ?>
                    </div>

                    <!-- Low Stock Alerts -->
                    <div class="admin-section-box">
                        <h2>Low Stock Alerts</h2>
                        <?php if ($low_stock_items && $low_stock_items->num_rows > 0): ?>
                            <ul class="admin-list">
                                <?php while ($item = $low_stock_items->fetch_assoc()): ?>
                                    <li class="admin-list-item warning">
                                        <div class="list-item-content">
                                            <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                            <span><?php echo htmlspecialchars($item['brand']); ?></span>
                                        </div>
                                        <span class="stock-count badge badge-danger">Stock: <?php echo $item['stock']; ?></span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                            <a href="manage_products.php" class="view-all">View All Products</a>
                        <?php else: ?>
                            <div class="empty-notice">
                                <i class="fas fa-check-circle"></i>
                                <p>All physical products are well stocked.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
