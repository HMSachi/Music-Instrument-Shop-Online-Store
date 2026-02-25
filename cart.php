<?php
require_once 'config/config.php';
require_once 'config/database.php';

$message = '';
$message_type = 'success';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security validation failed. Please try again.";
        redirect('cart.php');
    }

    // Update Cart Quantity
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $product_id = (int)$product_id;
            $quantity = (int)$quantity;
            
            if ($quantity > 0) {
                // Check stock before updating using preparedQuery
                $stock_query = "SELECT stock FROM products WHERE product_id = ?";
                $stock_check = preparedQuery($conn, $stock_query, [$product_id], "i");
                
                if ($stock_check && $row = $stock_check->fetch_assoc()) {
                    $product_stock = $row['stock'];
                    $_SESSION['cart'][$product_id] = min($quantity, $product_stock);
                } else {
                    // Item no longer exists in DB
                    unset($_SESSION['cart'][$product_id]);
                }
            } else {
                unset($_SESSION['cart'][$product_id]);
            }
        }
        $message = 'Cart updated successfully!';
    }
    
    // Remove individual item
    if (isset($_POST['remove_item'])) {
        $product_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$product_id]);
        $message = 'Item removed from cart!';
    }
    
    // Clear entire cart
    if (isset($_POST['clear_cart'])) {
        unset($_SESSION['cart']);
        $message = 'Shopping cart cleared!';
        $message_type = 'info';
    }
}

// Get cart items details
$cart_items = [];
$subtotal = 0;

if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    
    // Securely fetch products in cart
    $placeholders = implode(',', array_fill(0, count($product_ids), '?'));
    $types = str_repeat('i', count($product_ids));
    $sql = "SELECT * FROM products WHERE product_id IN ($placeholders)";
    $result = preparedQuery($conn, $sql, $product_ids, $types);
    
    while ($result && $product = $result->fetch_assoc()) {
        $pid = $product['product_id'];
        $product['quantity'] = $_SESSION['cart'][$pid];
        $product['line_total'] = $product['price'] * $product['quantity'];
        $subtotal += $product['line_total'];
        $cart_items[] = $product;
    }
}

// Shipping Logic: Free over £100, else £10
$shipping_threshold = 100.00;
$shipping_cost = ($subtotal >= $shipping_threshold || $subtotal == 0) ? 0.00 : 10.00;
$grand_total = $subtotal + $shipping_cost;

$page_title = 'Shopping Cart - Melody Masters';
include 'includes/header.php';
?>

<section class="cart-section" style="padding: 6rem 0;">
    <div class="container">
        <div style="margin-bottom: 4rem; text-align: center;">
            <h1 class="text-gold">Your Shopping Cart</h1>
            <p style="color: var(--text-muted);">Review your selected instruments before checkout.</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> animate-fade-in" style="margin-bottom: 2rem;">
                <i class="fas fa-info-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cart_items)): ?>
            <div class="glass-card text-center" style="padding: 6rem 2rem;">
                <div style="font-size: 4rem; color: var(--border-color); margin-bottom: 2rem;">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h2>Your cart is empty</h2>
                <p style="color: var(--text-muted); max-width: 500px; margin: 1rem auto 2.5rem;">Looks like you haven't added any gear to your setup yet.</p>
                <a href="shop.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-store"></i> Browse Collection
                </a>
            </div>
        <?php else: ?>
            <div class="cart-grid-3 animate-fade-in">
                <div class="cart-main">
                    <form method="POST" action="">
                        <?php echo csrfInput(); ?>
                        <div class="glass-card" style="padding: 0; overflow: hidden;">
                            <table class="cart-table-modern">
                                <thead>
                                    <tr>
                                        <th>Instrument</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="cart-product-cell" style="display: flex; align-items: center; gap: 1.5rem;">
                                                    <div style="width: 80px; height: 80px; border-radius: var(--radius-sm); overflow: hidden; border: 1px solid var(--border-color); background: #000;">
                                                        <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>" 
                                                             style="width: 100%; height: 100%; object-fit: cover;">
                                                    </div>
                                                    <div>
                                                        <h4 style="margin: 0; font-size: 1rem;">
                                                            <a href="product.php?id=<?php echo $item['product_id']; ?>" style="color: var(--text-main);"><?php echo htmlspecialchars($item['product_name']); ?></a>
                                                        </h4>
                                                        <span style="font-size: 0.85rem; color: var(--text-muted);"><?php echo htmlspecialchars($item['brand']); ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="qty-control">
                                                    <button type="button" onclick="this.nextElementSibling.stepDown(); this.form.update_cart.click();"><i class="fas fa-minus"></i></button>
                                                    <input type="number" name="quantity[<?php echo $item['product_id']; ?>]" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           min="1" max="<?php echo $item['stock']; ?>" readonly>
                                                    <button type="button" onclick="this.previousElementSibling.stepUp(); this.form.update_cart.click();"><i class="fas fa-plus"></i></button>
                                                </div>
                                            </td>
                                            <td style="font-weight: 700; color: var(--text-main); font-size: 1.1rem;"><?php echo formatPrice($item['line_total']); ?></td>
                                            <td>
                                                <button type="submit" name="remove_item" value="1" 
                                                        onclick="this.form.product_id.value='<?php echo $item['product_id']; ?>'"
                                                        class="btn btn-logout btn-sm" style="padding: 0.5rem; width: 40px; height: 40px;" title="Remove Item">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <input type="hidden" name="product_id" value="">
                        
                        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                            <button type="submit" name="update_cart" id="update_cart" class="btn btn-secondary btn-sm" style="display: none;">
                                Update Cart
                            </button>
                            <button type="submit" name="clear_cart" class="btn btn-logout btn-sm" 
                                    onclick="return confirm('Completely clear your shopping manifest?')">
                                <i class="fas fa-dumpster"></i> Clear Entire Manifest
                            </button>
                        </div>
                    </form>
                </div>
                
                <aside class="cart-summary">
                    <div class="summary-card glass-card" style="padding: 2.5rem;">
                        <h3 style="margin-bottom: 2rem; font-size: 1.25rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Summary</h3>
                        
                        <div class="summary-line" style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; color: var(--text-muted);">
                            <span>Subtotal</span>
                            <span style="color: var(--text-main); font-weight: 600;"><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        
                        <div class="summary-line" style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; color: var(--text-muted);">
                            <span>Shipping</span>
                            <?php if ($shipping_cost == 0): ?>
                                <span style="color: var(--success); font-weight: 700;">FREE</span>
                            <?php else: ?>
                                <span style="color: var(--text-main); font-weight: 600;"><?php echo formatPrice($shipping_cost); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($subtotal > 0): ?>
                            <?php if ($subtotal >= $shipping_threshold): ?>
                                <div class="free-shipping-highlight">
                                    <i class="fas fa-shipping-fast"></i>
                                    <div>
                                        <p style="margin: 0; color: var(--success); font-weight: 700; font-size: 0.9rem;">Free Shipping Unlocked!</p>
                                        <p style="margin: 0; color: var(--text-muted); font-size: 0.75rem;">Your instruments will arrive at no extra cost.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div style="background: rgba(0, 0, 0, 0.03); padding: 1.5rem; border-radius: var(--radius-md); margin: 2rem 0;">
                                    <?php 
                                    $remaining = $shipping_threshold - $subtotal;
                                    $progress = ($subtotal / $shipping_threshold) * 100;
                                    ?>
                                    <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.5rem;">
                                        <span style="color: var(--text-muted);">Free shipping goal</span>
                                        <span style="color: var(--primary); font-weight: 700;"><?php echo round($progress); ?>%</span>
                                    </div>
                                    <div class="shipping-progress">
                                        <div class="shipping-progress-bar" style="width: <?php echo $progress; ?>%;"></div>
                                    </div>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem; text-align: center;">
                                        Add <span style="color: var(--primary); font-weight: 700;"><?php echo formatPrice($remaining); ?></span> for FREE shipping!
                                    </p>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="summary-total-line" style="margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 2rem; display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 800; font-size: 1.1rem; text-transform: uppercase;">Grand Total</span>
                            <span class="text-gold" style="font-size: 2rem; font-weight: 800;"><?php echo formatPrice($grand_total); ?></span>
                        </div>
                    </div>
                </aside>

                <aside class="cart-actions">
                    <div class="summary-card glass-card" style="padding: 2.5rem; border-color: var(--primary);">
                        <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem;">Shipment Path</h3>
                        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 2.5rem; line-height: 1.7;">Secure your transaction and begin the journey of your new instruments.</p>
                        
                        <a href="checkout.php" class="btn btn-primary btn-block btn-lg pulse-btn" style="margin-bottom: 1.5rem; box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2); justify-content: center; height: auto; min-height: 60px; font-size: 0.95rem;">
                            <span style="display: inline-block; text-align: center; font-weight: 700;">Proceed to Acquisition</span> <i class="fas fa-chevron-right" style="margin-left: 0.5rem; font-size: 0.8rem;"></i>
                        </a>
                        
                        <a href="shop.php" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left" style="margin-right: 0.5rem;"></i> Continue Exploration
                        </a>

                        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border-color); display: flex; justify-content: center; gap: 1.5rem; font-size: 1.5rem; color: var(--text-muted); opacity: 0.4;">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-amex"></i>
                            <i class="fab fa-cc-paypal"></i>
                        </div>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
