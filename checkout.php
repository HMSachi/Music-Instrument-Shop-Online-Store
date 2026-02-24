<?php
require_once 'config/config.php';
require_once 'config/database.php';

requireLogin();

$error = '';
$success = '';

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    redirect('cart.php');
}

// Get cart items
$product_ids = array_keys($_SESSION['cart']);
$ids = implode(',', $product_ids);

$sql = "SELECT * FROM products WHERE product_id IN ($ids)";
$result = $conn->query($sql);

$cart_items = [];
$subtotal = 0;

while ($product = $result->fetch_assoc()) {
    $product['quantity'] = $_SESSION['cart'][$product['product_id']];
    $product['subtotal'] = $product['price'] * $product['quantity'];
    $subtotal += $product['subtotal'];
    $cart_items[] = $product;
}

// Shipping Logic: Synchronize with cart.php (Free over £100, else £10)
$shipping_threshold = 100.00;
$shipping_cost = ($subtotal > $shipping_threshold) ? 0.00 : 10.00;
$total = $subtotal + $shipping_cost;

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $shipping_address = $conn->real_escape_string($_POST['shipping_address']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    
    if (empty($shipping_address)) {
        $error = 'Please provide a shipping address';
    } else {
        // Mock Payment Simulation (Briefly pause or just proceed for now)
        // In a real app, you'd verify payment status here
        $payment_status = 'Success'; // Simulated success
        
        if ($payment_status === 'Success') {
            // Start transaction
            $conn->begin_transaction();
            
            try {
                // Create order
                $order_sql = "INSERT INTO orders (user_id, total_amount, shipping_cost, order_status) 
                              VALUES ($user_id, $total, $shipping_cost, 'Processing')";
                
                if ($conn->query($order_sql)) {
                    $order_id = $conn->insert_id;
                    
                    // Insert order items
                    foreach ($cart_items as $item) {
                        $product_id = $item['product_id'];
                        $quantity = $item['quantity'];
                        $price = $item['price'];
                        
                        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                     VALUES ($order_id, $product_id, $quantity, $price)";
                        $conn->query($item_sql);
                        
                        // Update stock (only for physical products)
                        if ($item['product_type'] === 'physical') {
                            $update_stock = "UPDATE products SET stock = stock - $quantity WHERE product_id = $product_id";
                            $conn->query($update_stock);
                        }
                    }
                    
                    // Commit transaction
                    $conn->commit();
                    
                    // Clear cart
                    unset($_SESSION['cart']);
                    
                    // Redirect to dedicated success page
                    $_SESSION['last_order_id'] = $order_id;
                    redirect('order_success.php');
                } else {
                    throw new Exception('Failed to create order');
                }
            } catch (Exception $e) {
                $conn->rollback();
                $error = 'Order processing failed: ' . $e->getMessage();
            }
        } else {
            $error = 'Payment failed. Please check your payment details.';
        }
    }
}

// Get user details
$user_sql = "SELECT * FROM users WHERE user_id = " . $_SESSION['user_id'];
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

$page_title = 'Checkout - Melody Masters';
include 'includes/header.php';
?>

<section class="checkout-section">
    <div class="container">
        <h1><i class="fas fa-credit-card"></i> Checkout</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="checkout-grid">
            <!-- Checkout Form -->
            <div class="checkout-form">
                <form method="POST" action="">
                    <div class="form-section">
                        <h3>Shipping Information</h3>
                        
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" 
                                   value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="shipping_address">Shipping Address *</label>
                            <textarea id="shipping_address" name="shipping_address" rows="4" required><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>Payment Method</h3>
                        
                        <div class="payment-options">
                            <label class="radio-option">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <span>Cash on Delivery (COD)</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="payment_method" value="bank">
                                <span>Bank Transfer</span>
                            </label>
                            <label class="radio-option">
                                <input type="radio" name="payment_method" value="gcash">
                                <span>GCash</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="btn btn-primary btn-large btn-block">
                        <i class="fas fa-check"></i> Place Order
                    </button>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="order-summary">
                <h3>Order Summary</h3>
                
                <div class="summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                <p>Qty: <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?></p>
                            </div>
                            <span class="item-price"><?php echo formatPrice($item['subtotal']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-totals">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span><?php echo formatPrice($subtotal); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span><?php echo formatPrice($shipping_cost); ?></span>
                    </div>
                    <div class="summary-row summary-total">
                        <span>Total:</span>
                        <span><?php echo formatPrice($total); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
