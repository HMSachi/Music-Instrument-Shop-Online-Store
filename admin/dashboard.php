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
        <div style="margin-bottom: 4rem;">
            <h1 class="text-gold">Admin Command Center</h1>
            <p style="color: var(--text-muted);">Real-time metrics and management tools for Melody Masters.</p>
        </div>
        
        <div class="admin-layout animate-fade-in">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="glass-card" style="padding: 2.5rem; position: sticky; top: 100px;">
                    <div style="text-align: center; margin-bottom: 3rem;">
                        <div style="width: 80px; height: 80px; background: var(--gold-gradient); color: var(--bg-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin: 0 auto 1.5rem; box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);">
                            A
                        </div>
                        <h4 style="color: var(--text-main); margin: 0; font-size: 1.1rem;">Site Administrator</h4>
                    </div>
                    
                    <nav class="dashboard-nav">
                        <a href="dashboard.php" class="active">
                            <i class="fas fa-chart-line"></i> Analytics Overview
                        </a>
                        <a href="manage_products.php">
                            <i class="fas fa-guitar"></i> Instrument Inventory
                        </a>
                        <a href="manage_users.php">
                            <i class="fas fa-user-friends"></i> User Base
                        </a>
                        <a href="manage_orders.php">
                            <i class="fas fa-receipt"></i> Order Management
                        </a>
                        <a href="<?php echo SITE_URL; ?>/index.php" style="margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                            <i class="fas fa-external-link-alt"></i> Public Storefront
                        </a>
                    </nav>
                </div>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card card-shimmer">
                        <div class="stat-icon" style="background: rgba(52, 152, 219, 0.15); color: #3498db;">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['products']; ?></h3>
                            <p>Products</p>
                        </div>
                    </div>
                    
                    <div class="stat-card card-shimmer">
                        <div class="stat-icon" style="background: rgba(46, 213, 115, 0.15); color: #2ecc71;">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['users']; ?></h3>
                            <p>Customers</p>
                        </div>
                    </div>
                    
                    <div class="stat-card card-shimmer">
                        <div class="stat-icon" style="background: rgba(241, 196, 15, 0.15); color: #f1c40f;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['orders']; ?></h3>
                            <p>Total Orders</p>
                        </div>
                    </div>
                    
                    <div class="stat-card" style="<?php echo $stats['low_stock'] > 0 ? 'border-color: rgba(231, 76, 60, 0.4); background: rgba(231, 76, 60, 0.02);' : ''; ?>">
                        <div class="stat-icon" style="background: rgba(231, 76, 60, 0.15); color: #e74c3c;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['low_stock']; ?></h3>
                            <p>Low Stock</p>
                        </div>
                    </div>
                </div>
                
                <div class="admin-grid-two">
                    <!-- Recent Orders -->
                    <div class="admin-section-box">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
                            <h2 style="margin: 0;">Recent Transactions</h2>
                            <a href="manage_orders.php" class="view-all" style="margin: 0;">View All</a>
                        </div>
                        
                        <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer</th>
                                            <th>Volume</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                            <tr>
                                                <td style="color: var(--primary); font-family: monospace; font-weight: 700;">#<?php echo $order['order_id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                                <td style="color: var(--text-main); font-weight: 600;"><?php echo formatPrice($order['total_amount']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo strtolower($order['order_status']); ?>">
                                                        <?php echo $order['order_status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="manage_orders.php?view=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-secondary" style="min-width: 60px;">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 4rem 0;">
                                <p style="color: var(--text-muted);">No transactions recorded yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Low Stock Alerts -->
                    <div class="admin-section-box">
                        <h2 style="margin-bottom: 2.5rem;">Inventory Watchlist</h2>
                        <?php if ($low_stock_items && $low_stock_items->num_rows > 0): ?>
                            <div class="admin-list">
                                <?php while ($item = $low_stock_items->fetch_assoc()): ?>
                                    <div class="admin-list-item" style="border-left: 3px solid #e74c3c;">
                                        <div>
                                            <div style="color: var(--text-main); font-weight: 600; margin-bottom: 0.25rem;">
                                                <?php echo htmlspecialchars($item['product_name']); ?>
                                            </div>
                                            <div style="color: var(--text-muted); font-size: 0.85rem;">
                                                <?php echo htmlspecialchars($item['brand']); ?>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <span class="badge badge-cancelled" style="font-size: 0.7rem; padding: 0.25rem 0.75rem;">
                                                Remaining: <?php echo $item['stock']; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <a href="manage_products.php" class="view-all">Manage Full Inventory</a>
                        <?php else: ?>
                            <div class="empty-notice" style="text-align: center; padding: 4rem 0;">
                                <i class="fas fa-check-circle" style="font-size: 2.5rem; color: var(--success); margin-bottom: 1.5rem; display: block;"></i>
                                <p style="color: var(--text-muted);">All instruments are sufficiently stocked.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Financial Overview (Simulated) -->
                <div class="admin-section-box" style="margin-top: 3rem;">
                    <h2>Financial Performance</h2>
                    <div style="display: flex; gap: 4rem; align-items: center;">
                        <div style="flex: 1;">
                            <div style="color: var(--text-muted); font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Net Revenue</div>
                            <div class="text-gold" style="font-size: 3rem; font-weight: 800;"><?php echo formatPrice($stats['revenue']); ?></div>
                        </div>
                        <div style="flex: 2; height: 100px; background: rgba(212, 175, 55, 0.05); border: 1px dashed var(--border-color); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                            <i class="fas fa-chart-area" style="margin-right: 1rem;"></i> Revenue analytics visualization would render here
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
