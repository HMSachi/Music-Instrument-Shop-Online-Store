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
// Hook for applying admin-specific body themes
$body_class = 'admin-mode';
include '../includes/header.php';
?>

<!-- Admin Redesign Wrapper -->
<div class="admin-wrapper slide-up-fade">
    <!-- Floating Glass Sidebar -->
    <aside class="admin-sidebar-glass">
        <div class="admin-profile-badge">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
            </div>
            <h4>Site Administrator</h4>
            <div style="color: var(--admin-primary); font-size: 0.8rem; margin-top: 0.25rem;">Super Admin</div>
        </div>
        
        <nav class="admin-nav-menu">
            <a href="dashboard.php" class="admin-nav-item active">
                <i class="fas fa-satellite-dish"></i> Command Center
            </a>
            <a href="manage_products.php" class="admin-nav-item">
                <i class="fas fa-guitar"></i> Instrument Vault
            </a>
            <a href="manage_users.php" class="admin-nav-item">
                <i class="fas fa-users-cog"></i> User Base
            </a>
            <a href="manage_orders.php" class="admin-nav-item">
                <i class="fas fa-file-invoice-dollar"></i> Global Ledgers
            </a>
            
            <div class="admin-nav-divider"></div>
            
            <a href="<?php echo SITE_URL; ?>/index.php" class="admin-nav-item" style="color: var(--admin-primary);">
                <i class="fas fa-external-link-alt"></i> Storefront
            </a>
        </nav>
    </aside>

    <!-- Main Content Panel -->
    <main class="admin-main-content">
        <div class="admin-page-header">
            <h1 class="admin-page-title">Admin Dashboard</h1>
            <p class="admin-page-subtitle">Real-time metrics and system management.</p>
        </div>

        <!-- Glass Stat Cards -->
        <div class="admin-stats-grid">
            <div class="admin-stat-card delay-1">
                <div class="admin-stat-icon" style="background: rgba(52, 152, 219, 0.15); color: #3498db;">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $stats['products']; ?></h3>
                    <p>Total Assets</p>
                </div>
            </div>
            
            <div class="admin-stat-card delay-2">
                <div class="admin-stat-icon" style="background: rgba(46, 213, 115, 0.15); color: #2ecc71;">
                    <i class="fas fa-users"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $stats['users']; ?></h3>
                    <p>Active Users</p>
                </div>
            </div>
            
            <div class="admin-stat-card delay-3">
                <div class="admin-stat-icon" style="background: rgba(155, 89, 182, 0.15); color: #9b59b6;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $stats['orders']; ?></h3>
                    <p>Transactions</p>
                </div>
            </div>
            
            <div class="admin-stat-card delay-3" style="<?php echo $stats['low_stock'] > 0 ? 'border-color: rgba(231, 76, 60, 0.4);' : ''; ?>">
                <div class="admin-stat-icon" style="background: rgba(231, 76, 60, 0.15); color: #e74c3c;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="admin-stat-info">
                    <h3><?php echo $stats['low_stock']; ?></h3>
                    <p>Low Stock</p>
                </div>
            </div>
        </div>

        <div class="admin-data-grid">
            <!-- Recent Activity Panel -->
            <div class="admin-panel slide-up-fade delay-2">
                <div class="admin-panel-header">
                    <h2>Recent Transactions</h2>
                    <a href="manage_orders.php" class="admin-btn-outline">View Ledgers</a>
                </div>
                
                <?php if ($recent_orders && $recent_orders->num_rows > 0): ?>
                    <div class="admin-table-wrapper">
                        <table class="admin-modern-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Entity</th>
                                    <th>Volume</th>
                                    <th>Status</th>
                                    <th>Audit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $recent_orders->fetch_assoc()): ?>
                                    <tr>
                                        <td class="order-id">#<?php echo $order['order_id']; ?></td>
                                        <td style="font-weight: 500;"><?php echo htmlspecialchars($order['full_name']); ?></td>
                                        <td style="color: var(--admin-primary); font-weight: 600;">
                                            <?php echo formatPrice($order['total_amount']); ?>
                                        </td>
                                        <td>
                                            <?php
                                                $status_color = 'rgba(255,255,255,0.1)';
                                                $text_color = '#fff';
                                                switch(strtolower($order['order_status'])) {
                                                    case 'pending': $status_color = 'rgba(241,196,15,0.15)'; $text_color = '#f1c40f'; break;
                                                    case 'processing': $status_color = 'rgba(52,152,219,0.15)'; $text_color = '#3498db'; break;
                                                    case 'shipped': $status_color = 'rgba(155,89,182,0.15)'; $text_color = '#9b59b6'; break;
                                                    case 'delivered': $status_color = 'rgba(46,213,115,0.15)'; $text_color = '#2ecc71'; break;
                                                    case 'cancelled': $status_color = 'rgba(231,76,60,0.15)'; $text_color = '#e74c3c'; break;
                                                }
                                            ?>
                                            <span class="admin-badge" style="background: <?php echo $status_color; ?>; color: <?php echo $text_color; ?>">
                                                <?php echo $order['order_status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="manage_orders.php?view=<?php echo $order['order_id']; ?>" style="color: var(--admin-text-muted); transition: color 0.3s ease;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 0; color: var(--admin-text-muted);">
                        <i class="fas fa-inbox fa-3x" style="opacity: 0.5; margin-bottom: 1rem;"></i>
                        <p>No recent transactions matching current criteria.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Intelligent Alerts Panel -->
            <div class="admin-panel slide-up-fade delay-3">
                <div class="admin-panel-header">
                    <h2>System Alerts</h2>
                </div>
                
                <?php if ($low_stock_items && $low_stock_items->num_rows > 0): ?>
                    <div class="admin-warning-list">
                        <?php while ($item = $low_stock_items->fetch_assoc()): ?>
                            <div class="admin-warning-item">
                                <div>
                                    <div class="warning-item-title"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    <div class="warning-item-sub"><?php echo htmlspecialchars($item['brand']); ?> • ID Analysis Required</div>
                                </div>
                                <div style="font-weight: 800; color: #fff;">
                                    <?php echo $item['stock']; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div style="margin-top: 1.5rem; text-align: center;">
                        <a href="manage_products.php" class="admin-btn-outline" style="width: 100%; display: block;">Resolve Alerts</a>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem 0; color: var(--success);">
                        <i class="fas fa-shield-check fa-3x" style="margin-bottom: 1rem; color: #2ecc71;"></i>
                        <p>All systemic parameters nominal.<br><span style="color: var(--admin-text-muted); font-size: 0.85rem;">Zero critical alerts detected.</span></p>
                    </div>
                <?php endif; ?>
                
                <!-- Financials Snapshot -->
                <div style="margin-top: 2.5rem; padding-top: 2.5rem; border-top: 1px solid var(--admin-border);">
                    <div style="color: var(--admin-text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Net Capitalization</div>
                    <div style="font-size: 3rem; font-weight: 800; color: var(--admin-primary); line-height: 1; text-shadow: 0 0 20px rgba(212,175,55,0.4);">
                        <?php echo formatPrice($stats['revenue']); ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
