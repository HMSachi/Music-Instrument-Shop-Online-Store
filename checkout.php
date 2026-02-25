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

// Securely fetch products in cart
$placeholders = implode(',', array_fill(0, count($product_ids), '?'));
$types = str_repeat('i', count($product_ids));
$sql = "SELECT * FROM products WHERE product_id IN ($placeholders)";
$result = preparedQuery($conn, $sql, $product_ids, $types);

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
$shipping_cost = ($subtotal >= $shipping_threshold) ? 0.00 : 10.00;
$total = $subtotal + $shipping_cost;

// Handle checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security validation failed. Please try again.";
        redirect('checkout.php');
    }

    $user_id = $_SESSION['user_id'];
    $shipping_address = sanitizeInput($_POST['shipping_address']);
    $payment_method = sanitizeInput($_POST['payment_method']);
    
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
                // Final Stock Verification
                foreach ($cart_items as $item) {
                    if ($item['product_type'] === 'physical') {
                        $stock_check = preparedQuery($conn, "SELECT stock FROM products WHERE product_id = ? FOR UPDATE", [$item['product_id']], "i");
                        $current_stock = $stock_check->fetch_assoc()['stock'];
                        if ($current_stock < $item['quantity']) {
                            throw new Exception("Sorry, " . $item['product_name'] . " is now out of stock or has insufficient quantity.");
                        }
                    }
                }

                // Create order
                $order_sql = "INSERT INTO orders (user_id, total_amount, shipping_cost, order_status) 
                              VALUES (?, ?, ?, 'Processing')";
                
                $order_id = preparedQuery($conn, $order_sql, [$user_id, $total, $shipping_cost], "idd");
                
                if ($order_id) {
                    // Insert order items
                    foreach ($cart_items as $item) {
                        $product_id = $item['product_id'];
                        $quantity = $item['quantity'];
                        $price = $item['price'];
                        
                        $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                     VALUES (?, ?, ?, ?)";
                        preparedQuery($conn, $item_sql, [$order_id, $product_id, $quantity, $price], "iiid");
                        
                        // Update stock (only for physical products)
                        if ($item['product_type'] === 'physical') {
                            $update_stock = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                            preparedQuery($conn, $update_stock, [$quantity, $product_id], "ii");
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
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_result = preparedQuery($conn, $user_sql, [$_SESSION['user_id']], "i");
$user = $user_result->fetch_assoc();

$page_title = 'Checkout - Melody Masters';
include 'includes/header.php';
?>

<section class="checkout-section" style="padding: 6rem 0;">
    <div class="container">
        <div style="margin-bottom: 4rem; text-align: center;">
            <h1 class="text-gold">Secure Checkout</h1>
            <p style="color: var(--text-muted);">Please confirm your details and complete your order.</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error animate-fade-in" style="margin-bottom: 2rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <div class="checkout-grid animate-fade-in">
            <!-- Checkout Form -->
            <div class="checkout-main">
                <form method="POST" action="">
                    <?php echo csrfInput(); ?>
                    
                    <div class="glass-card" style="margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 2rem; color: var(--primary);">
                            <i class="fas fa-truck" style="margin-right: 0.5rem;"></i> Shipping Information
                        </h3>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label" for="full_name">Full Name</label>
                                <input type="text" id="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="email">Email Address</label>
                                <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top: 1.5rem;">
                            <label class="form-label" for="phone">Phone Number</label>
                            <input type="text" id="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" readonly>
                        </div>
                        
                        <div class="form-group" style="margin-top: 1.5rem;">
                            <label class="form-label" for="shipping_address">Delivery Address *</label>
                            <textarea id="shipping_address" name="shipping_address" class="form-control" rows="4" placeholder="Enter your full street address..." required><?php echo htmlspecialchars($user['address']); ?></textarea>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.5rem;">Please ensuring this matches your primary delivery location.</p>
                        </div>
                    </div>
                    
                    <div class="glass-card" style="margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 2rem; color: var(--primary);">
                            <i class="fas fa-wallet" style="margin-right: 0.5rem;"></i> Payment Method
                        </h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <label class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; cursor: pointer; transition: var(--transition); border-color: rgba(212, 175, 55, 0.2);">
                                <input type="radio" name="payment_method" value="cod" checked style="accent-color: var(--primary); width: 20px; height: 20px;">
                                <div>
                                    <h4 style="margin: 0;">Cash on Delivery</h4>
                                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Pay securely when your instruments arrive.</p>
                                </div>
                                <i class="fas fa-hand-holding-usd" style="margin-left: auto; font-size: 1.5rem; color: var(--primary);"></i>
                            </label>
                            
                            <label class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; cursor: pointer; transition: var(--transition);">
                                <input type="radio" name="payment_method" value="bank" style="accent-color: var(--primary); width: 20px; height: 20px;">
                                <div>
                                    <h4 style="margin: 0;">Bank Transfer</h4>
                                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Direct transfer to our business account.</p>
                                </div>
                                <i class="fas fa-university" style="margin-left: auto; font-size: 1.5rem; color: var(--primary);"></i>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="btn btn-primary btn-lg btn-block">
                        Confirm Purchase <i class="fas fa-check-circle" style="margin-left: 0.5rem;"></i>
                    </button>
                    <p style="text-align: center; margin-top: 1.5rem; color: var(--text-muted); font-size: 0.85rem;">
                        By placing your order, you agree to our <a href="#" style="color: var(--primary);">Terms of Service</a>.
                    </p>
                </form>
            </div>
            
            <!-- Side Summary -->
            <aside class="checkout-summary">
                <div class="summary-card">
                    <h3 style="margin-bottom: 2rem;">Order Review</h3>
                    
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 2rem; padding-right: 0.5rem;">
                        <?php foreach ($cart_items as $item): ?>
                            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                                <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                <div style="flex: 1;">
                                    <h5 style="margin: 0; font-size: 0.95rem; color: var(--text-main);"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                    <p style="margin: 0; font-size: 0.85rem; color: var(--text-muted);">Qty: <?php echo $item['quantity']; ?></p>
                                </div>
                                <span style="font-weight: 600; font-size: 0.95rem;"><?php echo formatPrice($item['subtotal']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div style="margin-bottom: 2rem;">
                        <div class="summary-line">
                            <span>Subtotal</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        <div class="summary-line">
                            <span>Shipping</span>
                            <?php if ($shipping_cost == 0): ?>
                                <span style="color: var(--success); font-weight: 600;">FREE</span>
                            <?php else: ?>
                                <span><?php echo formatPrice($shipping_cost); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="summary-total-line">
                            <span>Total Due</span>
                            <span class="text-gold"><?php echo formatPrice($total); ?></span>
                        </div>
                    </div>
                    
                    <div style="background: rgba(46, 213, 115, 0.05); padding: 1.5rem; border-radius: var(--radius-md); border: 1px dashed var(--success); display: flex; align-items: flex-start; gap: 1rem;">
                        <i class="fas fa-shield-check" style="color: var(--success); font-size: 1.25rem; margin-top: 0.25rem;"></i>
                        <div>
                            <p style="margin: 0; font-size: 0.9rem; font-weight: 600; color: var(--text-main);">Safe & Secure</p>
                            <p style="margin: 0; font-size: 0.8rem; color: var(--text-muted);">Your transaction is protected with 256-bit encryption.</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
