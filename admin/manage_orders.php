<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$message = '';
$error = '';

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security validation failed. Please try again.";
        redirect('admin/manage_orders.php');
    }

    $order_id = (int)$_POST['order_id'];
    $status = sanitizeInput($_POST['status']);
    
    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    
    if (preparedQuery($conn, $sql, [$status, $order_id], "si")) {
        $message = 'Order status updated successfully!';
    } else {
        $error = 'Failed to update order status';
    }
}

// Get all orders
$orders = preparedQuery($conn, "SELECT o.*, u.full_name, u.email FROM orders o 
                        JOIN users u ON o.user_id = u.user_id 
                        ORDER BY o.order_date DESC");

$page_title = 'Manage Orders - Admin';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container">
        <div style="margin-bottom: 4rem;">
            <h1 class="text-gold">Order Management</h1>
            <p style="color: var(--text-muted);">Process and fullfill global instrument acquisitions.</p>
        </div>
        
        <div class="admin-layout animate-fade-in">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="glass-card card-shimmer" style="padding: 2.5rem; position: sticky; top: 100px;">
                    <nav class="dashboard-nav">
                        <a href="dashboard.php">
                            <i class="fas fa-chart-line"></i> Analytics Overview
                        </a>
                        <a href="manage_products.php">
                            <i class="fas fa-guitar"></i> Instrument Inventory
                        </a>
                        <a href="manage_users.php">
                            <i class="fas fa-user-friends"></i> User Base
                        </a>
                        <a href="manage_orders.php" class="active">
                            <i class="fas fa-receipt"></i> Order Management
                        </a>
                    </nav>
                </div>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                <div class="admin-section-box card-shimmer">
                    <h2 style="margin-bottom: 2.5rem;">Fulfillment Repository</h2>
                    
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Acquirer</th>
                                        <th>Investment</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($order = $orders->fetch_assoc()): ?>
                                        <tr>
                                            <td style="font-family: monospace; font-weight: 700; color: var(--primary);">#<?php echo $order['order_id']; ?></td>
                                            <td>
                                                <div style="color: var(--text-main); font-weight: 700;"><?php echo htmlspecialchars($order['full_name']); ?></div>
                                                <div style="color: var(--text-muted); font-size: 0.8rem;"><?php echo htmlspecialchars($order['email']); ?></div>
                                            </td>
                                            <td style="color: var(--text-main); font-weight: 600;"><?php echo formatPrice($order['total_amount']); ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <?php echo csrfInput(); ?>
                                                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                    <select name="status" onchange="this.form.submit()" class="form-control-sm" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius-sm); padding: 0.25rem 0.5rem; outline: none; transition: var(--transition);">
                                                        <option value="Pending" <?php echo $order['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Processing" <?php echo $order['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="Shipped" <?php echo $order['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="Delivered" <?php echo $order['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                        <option value="Cancelled" <?php echo $order['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                    </select>
                                                    <input type="hidden" name="update_status">
                                                </form>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <div style="color: var(--text-muted); font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></div>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-secondary view-order" data-order-id="<?php echo $order['order_id']; ?>" style="min-width: 80px;">
                                                    Inspect
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="order-details-reveal" id="order-<?php echo $order['order_id']; ?>" style="display: none;">
                                            <td colspan="6">
                                                <div class="glass-card card-shimmer" style="margin: 2rem 0; padding: 3rem; background: rgba(255,255,255,0.01);">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
                                                        <h4 style="margin: 0; font-size: 1.25rem; color: var(--text-main);">Acquisition Manifest</h4>
                                                        <span style="color: var(--text-muted); font-size: 0.9rem;">Reference: #<?php echo $order['order_id']; ?></span>
                                                    </div>
                                                    
                                                    <?php
                                                    $items_sql = "SELECT oi.*, p.product_name, p.image FROM order_items oi 
                                                                  JOIN products p ON oi.product_id = p.product_id 
                                                                  WHERE oi.order_id = ?";
                                                    $items = preparedQuery($conn, $items_sql, [$order['order_id']], "i");
                                                    ?>
                                                    
                                                    <div class="table-container" style="border: 1px solid var(--border-color); border-radius: var(--radius-md);">
                                                        <table style="background: transparent;">
                                                            <thead style="background: rgba(255,255,255,0.02);">
                                                                <tr>
                                                                    <th style="padding: 1.25rem;">Instrument</th>
                                                                    <th style="padding: 1.25rem;">Quantity</th>
                                                                    <th style="padding: 1.25rem;">Unit Value</th>
                                                                    <th style="padding: 1.25rem; text-align: right;">Subtotal</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php while ($item = $items->fetch_assoc()): ?>
                                                                    <tr>
                                                                        <td style="padding: 1.5rem;">
                                                                            <div style="display: flex; align-items: center; gap: 1.5rem;">
                                                                                <div class="admin-product-thumb" style="width: 45px; height: 45px; flex-shrink: 0;">
                                                                                    <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" alt="">
                                                                                </div>
                                                                                <div style="color: var(--text-main); font-weight: 700;"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                                                            </div>
                                                                        </td>
                                                                        <td style="padding: 1.5rem; color: var(--text-main);"><?php echo $item['quantity']; ?></td>
                                                                        <td style="padding: 1.5rem;"><?php echo formatPrice($item['price']); ?></td>
                                                                        <td style="padding: 1.5rem; text-align: right; color: var(--primary); font-weight: 700;"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                                                    </tr>
                                                                <?php endwhile; ?>
                                                            </tbody>
                                                            <tfoot style="background: rgba(255,255,255,0.02);">
                                                                <tr>
                                                                    <td colspan="3" style="text-align: right; padding: 1.5rem; font-weight: 600; color: var(--text-muted);">Logistic Service</td>
                                                                    <td style="text-align: right; padding: 1.5rem; color: var(--text-main);"><?php echo formatPrice($order['shipping_cost']); ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" style="text-align: right; padding: 1.5rem; font-weight: 800; color: var(--text-main); font-size: 1.1rem;">Acquisition Total</td>
                                                                    <td style="text-align: right; padding: 1.5rem; color: var(--primary); font-weight: 800; font-size: 1.25rem;"><?php echo formatPrice($order['total_amount']); ?></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 5rem 0;">
                            <i class="fas fa-receipt" style="font-size: 3rem; color: rgba(255,255,255,0.05); margin-bottom: 2rem; display: block;"></i>
                            <p style="color: var(--text-muted);">No acquisitions found in the fulfillment repository.</p>
                        </div>
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
