<?php
require_once 'config/config.php';
require_once 'config/database.php';

requireLogin();

// Check if we have an order ID to show
if (!isset($_SESSION['last_order_id'])) {
    redirect('customer/dashboard.php');
}

$order_id = $_SESSION['last_order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
$order_sql = "SELECT * FROM orders WHERE order_id = ? AND user_id = ?";
$order_result = preparedQuery($conn, $order_sql, [$order_id, $user_id], "ii");

if (!$order_result || $order_result->num_rows === 0) {
    redirect('index.php');
}

$order = $order_result->fetch_assoc();

// Get order items with product details
$items_sql = "SELECT oi.*, p.product_name, p.image 
              FROM order_items oi 
              JOIN products p ON oi.product_id = p.product_id 
              WHERE oi.order_id = ?";
$items = preparedQuery($conn, $items_sql, [$order_id], "i");

$page_title = 'Order Confirmed - Melody Masters';
include 'includes/header.php';
?>

<section class="success-section">
    <div class="container mini-container">
        <div class="success-card glass-card animate-fade-in-up" style="padding: 5rem 3rem; text-align: center;">
            <div class="success-icon" style="font-size: 5rem; color: var(--success); margin-bottom: 2.5rem; animation: float 3s ease-in-out infinite;">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Order Confirmed!</h1>
            <p class="order-number" style="font-size: 1.25rem; font-weight: 700; color: var(--primary); margin-bottom: 2.5rem;">Order Reference: #<?php echo $order_id; ?></p>
            <p style="font-size: 1.1rem; color: var(--text-light); max-width: 600px; margin: 0 auto 3rem;">Thank you for choosing Melody Masters. Your new instrument is being prepared for fulfillment and will be on its way to you shortly.</p>
            
            <div class="summary-box glass-card" style="max-width: 600px; margin: 0 auto 4rem; padding: 3rem; text-align: left;">
                <h3 style="margin-bottom: 2rem; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">Order Checklist</h3>
                <div class="summary-list">
                    <?php 
                    $has_digital = false;
                    $items->data_seek(0); // Reset result pointer
                    while($item = $items->fetch_assoc()): 
                    ?>
                        <div class="summary-item" style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--text);">
                            <strong><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></strong>
                            <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="summary-totals" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid var(--bg-main);">
                    <div class="total-row" style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--text-light);">
                        <span>Standard Shipping:</span>
                        <span><?php echo formatPrice($order['shipping_cost']); ?></span>
                    </div>
                    <div class="total-row grand-total" style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 800; color: var(--heading);">
                        <span>Total Paid:</span>
                        <span><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="success-actions" style="display: flex; gap: 1.5rem; justify-content: center;">
                <a href="shop.php" class="btn btn-primary btn-large">Continue Exploring</a>
                <a href="customer/dashboard.php" class="btn btn-outline btn-large">Track My Order</a>
            </div>
        </div>
    </div>
</section>

<?php 
// Clear the session variable so they don't see the success page again by accident/refresh
// unset($_SESSION['last_order_id']); 
include 'includes/footer.php'; 
?>
