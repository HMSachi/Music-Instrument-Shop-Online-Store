<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$user_id = $_SESSION['user_id'];

// Get user details
$user_sql = "SELECT * FROM users WHERE user_id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

// Get recent orders
$orders_sql = "SELECT o.*, u.full_name FROM orders o 
               JOIN users u ON o.user_id = u.user_id 
               ORDER BY o.order_date DESC LIMIT 10";
$orders = $conn->query($orders_sql);

$page_title = 'Staff Dashboard - Melody Masters';
include '../includes/header.php';
?>

<section class="dashboard-section">
    <div class="container">
        <h1><i class="fas fa-user-tie"></i> Staff Dashboard</h1>
        
        <div class="dashboard-grid">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar">
                <div class="user-profile">
                    <i class="fas fa-user-circle"></i>
                    <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="badge badge-info">Staff</span>
                </div>
                
                <nav class="dashboard-nav">
                    <a href="#" class="active"><i class="fas fa-shopping-cart"></i> Orders</a>
                    <a href="<?php echo SITE_URL; ?>/index.php"><i class="fas fa-home"></i> Back to Site</a>
                    <a href="<?php echo SITE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </aside>
            
            <!-- Content -->
            <div class="dashboard-content">
                <div class="content-section">
                    <h2>Recent Orders</h2>
                    
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $orders->fetch_assoc()): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                        <td><?php echo formatPrice($order['total_amount']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo strtolower($order['order_status']); ?>">
                                                <?php echo $order['order_status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
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

<?php include '../includes/footer.php'; ?>
