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
    <div class="container" style="max-width: 800px;">
        <div class="glass-card animate-fade-in" style="padding: 6rem 3rem;">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            
            <h1 class="text-gold" style="font-size: 3rem; margin-bottom: 1rem;">Order Confirmed!</h1>
            <div class="order-ref">Reference: #<?php echo $order_id; ?></div>
            
            <p style="color: var(--text-muted); font-size: 1.1rem; max-width: 600px; margin: 0 auto 4rem;">
                Thank you for choosing Melody Masters. Your new instrument is being prepared for fulfillment and will be on its way to you shortly.
            </p>
            
            <div class="glass-card" style="text-align: left; padding: 3rem; margin-bottom: 4rem;">
                <h3 style="margin-bottom: 2rem; color: var(--text-main); border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Order Summary</h3>
                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    <?php 
                    $items->data_seek(0);
                    while($item = $items->fetch_assoc()): 
                    ?>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="color: var(--text-main); font-weight: 500;"><?php echo htmlspecialchars($item['product_name']); ?></span>
                                <span style="color: var(--text-muted); font-size: 0.9rem; margin-left: 0.5rem;">x<?php echo $item['quantity']; ?></span>
                            </div>
                            <span style="color: var(--text-main);"><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 1rem; color: var(--text-muted);">
                        <span>Shipping</span>
                        <span><?php echo formatPrice($order['shipping_cost']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.5rem; font-weight: 700;">
                        <span style="color: var(--text-main);">Total Paid</span>
                        <span class="text-gold"><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1.5rem; justify-content: center;">
                <a href="shop.php" class="btn btn-secondary">
                    <i class="fas fa-store"></i> Continue Shopping
                </a>
                <a href="customer/dashboard.php" class="btn btn-primary">
                    <i class="fas fa-box"></i> View Dashboard
                </a>
            </div>
        </div>
    </div>
</section>

<?php 
// Clear the session variable so they don't see the success page again by accident/refresh
// unset($_SESSION['last_order_id']); 
include 'includes/footer.php'; 
?>
