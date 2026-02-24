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

<section class="dashboard-section">
    <div class="container">
        <h1><i class="fas fa-layer-group"></i> Staff Operations</h1>
        
        <div class="dashboard-grid">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar">
                <div class="user-profile">
                    <i class="fas fa-user-shield"></i>
                    <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="badge badge-info"><?php echo ucfirst($user['role']); ?></span>
                </div>
                
                <nav class="dashboard-nav">
                    <a href="?tab=orders" class="<?php echo $tab === 'orders' ? 'active' : ''; ?>">
                        <i class="fas fa-shopping-cart"></i> Order Fulfillment
                    </a>
                    <a href="?tab=inventory" class="<?php echo $tab === 'inventory' ? 'active' : ''; ?>">
                        <i class="fas fa-boxes"></i> Inventory Control
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php"><i class="fas fa-home"></i> Back to Site</a>
                    <a href="<?php echo SITE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </aside>
            
            <!-- Content -->
            <div class="dashboard-content">
                <?php if ($tab === 'orders'): ?>
                    <div class="content-section">
                        <h2><i class="fas fa-shipping-fast"></i> Order Management</h2>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th>Tracking #</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['full_name']); ?></strong><br>
                                                <small><?php echo date('M d, Y', strtotime($order['order_date'])); ?></small>
                                            </td>
                                            <form action="process_actions.php" method="POST">
                                                <?php echo csrfInput(); ?>
                                                <input type="hidden" name="action" value="update_order">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <td>
                                                    <select name="status" class="form-control-sm">
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
                                                           placeholder="Unassigned" class="form-control-sm">
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php elseif ($tab === 'inventory'): ?>
                    <div class="content-section">
                        <h2><i class="fas fa-warehouse"></i> Stock Management</h2>
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Current Stock</th>
                                        <th>New Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($product = $products->fetch_assoc()): ?>
                                        <tr class="<?php echo $product['stock'] <= 5 ? 'vibrate-slow' : ''; ?>">
                                            <td>
                                                <div class="product-cell">
                                                    <img src="<?php echo SITE_URL; ?>/<?php echo $product['image']; ?>" class="thumb-sm" alt="">
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($product['product_name']); ?></strong><br>
                                                        <small><?php echo htmlspecialchars($product['brand']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $product['stock'] <= 5 ? 'badge-danger' : 'badge-success'; ?>">
                                                    <?php echo $product['stock']; ?>
                                                </span>
                                            </td>
                                            <form action="process_actions.php" method="POST">
                                                <?php echo csrfInput(); ?>
                                                <input type="hidden" name="action" value="update_stock">
                                                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                <td>
                                                    <input type="number" name="stock" value="<?php echo $product['stock']; ?>" 
                                                           min="0" class="form-control-sm" style="width: 80px;">
                                                </td>
                                                <td>
                                                    <button type="submit" class="btn btn-sm btn-secondary">Update Stock</button>
                                                </td>
                                            </form>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
