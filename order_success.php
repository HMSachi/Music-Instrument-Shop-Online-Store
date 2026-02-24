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
        <div class="success-card animate-fade-in">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Order Confirmed!</h1>
            <p class="order-number">Order #<?php echo $order_id; ?></p>
            <p>Thank you for your purchase. Your order is being processed and will be shipped soon.</p>
            
            <div class="order-summary-box">
                <h3>Order Summary</h3>
                <div class="summary-list">
                    <?php 
                    $has_digital = false;
                    while($item = $items->fetch_assoc()): 
                        if ($item['product_type'] === 'digital') $has_digital = true;
                    ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($item['product_name']); ?> x <?php echo $item['quantity']; ?></span>
                            <span><?php echo formatPrice($item['price'] * $item['quantity']); ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div class="summary-totals">
                    <div class="total-row">
                        <span>Shipping:</span>
                        <span><?php echo formatPrice($order['shipping_cost']); ?></span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Grand Total:</span>
                        <span><?php echo formatPrice($order['total_amount']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($has_digital): ?>
                <div class="digital-notice">
                    <i class="fas fa-download"></i>
                    <h4>Digital Components Ready</h4>
                    <p>Your digital products are ready for download in your dashboard.</p>
                    <a href="customer/dashboard.php" class="btn btn-secondary">
                        Go to Downloads
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="success-actions">
                <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                <a href="customer/dashboard.php" class="btn btn-outline">View Order History</a>
            </div>
        </div>
    </div>
</section>

<?php 
// Clear the session variable so they don't see the success page again by accident/refresh
// unset($_SESSION['last_order_id']); 
include 'includes/footer.php'; 
?>
