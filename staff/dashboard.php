<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireStaff();

$user_id = $_SESSION['user_id'];

// Get current user details
$user_sql = "SELECT full_name, role FROM users WHERE user_id = ?";
$user_result = preparedQuery($conn, $user_sql, [$user_id], "i");
$user = $user_result->fetch_assoc();

// Get active tab
$tab = $_GET['tab'] ?? 'orders';

// Basic stats and orders
$orders_sql = "SELECT o.*, u.full_name FROM orders o 
               JOIN users u ON o.user_id = u.user_id 
               ORDER BY o.order_date DESC";
$orders = preparedQuery($conn, $orders_sql);

// Get products for stock management
$products_sql = "SELECT p.*, c.category_name FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 ORDER BY p.stock ASC";
$products = preparedQuery($conn, $products_sql);

$page_title = 'Staff Dashboard - Melody Masters';
include '../includes/header.php';
?>

<!-- Admin Redesign Wrapper -->
<div class="admin-wrapper slide-up-fade">
    <!-- Floating Glass Sidebar -->
    <aside class="admin-sidebar-glass">
        <div class="admin-profile-badge">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
            <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
            <div style="color: var(--admin-primary); font-size: 0.8rem; margin-top: 0.25rem;"><?php echo ucfirst($user['role']); ?> Operations</div>
        </div>
        
        <nav class="admin-nav-menu">
            <a href="?tab=orders" class="admin-nav-item <?php echo $tab === 'orders' ? 'active' : ''; ?>">
                <i class="fas fa-shipping-fast"></i> Order Fulfillment
            </a>
            <a href="?tab=inventory" class="admin-nav-item <?php echo $tab === 'inventory' ? 'active' : ''; ?>">
                <i class="fas fa-boxes"></i> Inventory Control
            </a>
            
            <div class="admin-nav-divider"></div>
            
            <a href="<?php echo SITE_URL; ?>/index.php" class="admin-nav-item" style="color: var(--admin-primary);">
                <i class="fas fa-external-link-alt"></i> Storefront
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="admin-nav-item" style="color: var(--error);">
                <i class="fas fa-sign-out-alt"></i> Sign Out
            </a>
        </nav>
    </aside>

    <!-- Main Content Panel -->
    <main class="admin-main-content">
        <div class="admin-page-header">
            <h1 class="admin-page-title">Staff Operations Center</h1>
            <p class="admin-page-subtitle">Monitor logistics and maintain the instrument inventory.</p>
        </div>
                <?php if ($tab === 'orders'): ?>
                    <div class="admin-section-box card-shimmer">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
                            <h2 style="margin: 0;">Order Manifest</h2>
                        </div>
                        
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Ref ID</th>
                                        <th>Acquirer</th>
                                        <th>Logistics Status</th>
                                        <th>Tracking Reference</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td style="color: var(--primary); font-family: monospace; font-weight: 700;">#<?php echo $order['order_id']; ?></td>
                                            <td>
                                                <div style="color: var(--text-main); font-weight: 600;"><?php echo htmlspecialchars($order['full_name']); ?></div>
                                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                                            </td>
                                            <form action="process_actions.php" method="POST">
                                                <?php echo csrfInput(); ?>
                                                <input type="hidden" name="action" value="update_order">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <td>
                                                    <select name="status" class="form-control-sm" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius-sm); padding: 0.25rem 0.5rem;">
                                                        <option value="Pending" <?php echo $order['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Processing" <?php echo $order['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="Shipped" <?php echo $order['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="Delivered" <?php echo $order['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                        <option value="Cancelled" <?php echo $order['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" name="tracking_number" 
                                                           value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>" 
                                                           placeholder="Assign Track ID" class="form-control-sm" 
                                                           style="width: 140px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius-sm); padding: 0.25rem 0.5rem;">
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-sm btn-primary">Sync</button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($tab === 'inventory'): ?>
                    <div class="admin-section-box">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
                            <h2 style="margin: 0;">Stock Procurement</h2>
                        </div>

                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Instrument</th>
                                        <th>Current Level</th>
                                        <th>Adjust Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($product = $products->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 1.25rem;">
                                                    <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                         style="width: 45px; height: 45px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                                    <div>
                                                        <div style="color: var(--text-main); font-weight: 600;"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($product['brand']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $product['stock'] <= 5 ? 'badge-cancelled' : 'badge-success'; ?>" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                                                    <?php echo $product['stock']; ?> Units
                                                </span>
                                                <?php if ($product['stock'] <= 5): ?>
                                                    <div style="color: var(--error); font-size: 0.7rem; font-weight: 700; margin-top: 0.25rem; text-transform: uppercase;">Low Stock Alert</div>
                                                <?php endif; ?>
                                            </td>
                                            <form action="process_actions.php" method="POST">
                                                <?php echo csrfInput(); ?>
                                                <input type="hidden" name="action" value="update_stock">
                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                <td>
                                                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" 
                                                           min="0" class="form-control-sm" 
                                                           style="width: 90px; background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius-sm); padding: 0.25rem 0.5rem;">
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-sm btn-secondary">Update</button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
