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

<section class="cart-section">
    <div class="container">
        <div class="section-header">
            <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
            <?php if (!empty($cart_items)): ?>
                <span class="cart-badge"><?php echo count($cart_items); ?> Items</span>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> animate-fade-in">
                <i class="fas fa-info-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart-v2">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h2>Your cart is currently empty</h2>
                <p>Looks like you haven't added any instruments to your collection yet. Explore our shop to find your perfect match!</p>
                <a href="shop.php" class="btn btn-primary btn-large">
                    <i class="fas fa-store"></i> Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="cart-grid animate-fade-in-up">
                <div class="cart-main">
                    <form method="POST" action="">
                        <?php echo csrfInput(); ?>
                        <div class="cart-table-wrapper">
                            <table class="cart-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cart_items as $item): ?>
                                        <tr>
                                            <td class="cart-product-cell">
                                                <div class="cart-product-info">
                                                    <div class="cart-product-image">
                                                        <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                             alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                             onerror="this.src='assets/images/placeholder.jpg';">
                                                    </div>
                                                    <div class="cart-product-details">
                                                        <h4><a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['product_name']); ?></a></h4>
                                                        <p>Brand: <?php echo htmlspecialchars($item['brand']); ?></p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="price-cell"><?php echo formatPrice($item['price']); ?></td>
                                            <td class="qty-cell">
                                                <div class="qty-control">
                                                    <input type="number" name="quantity[<?php echo $item['product_id']; ?>]" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           min="1" max="<?php echo $item['stock']; ?>" 
                                                           class="qty-input">
                                                </div>
                                            </td>
                                            <td class="subtotal-cell"><?php echo formatPrice($item['line_total']); ?></td>
                                            <td class="action-cell">
                                                <button type="submit" name="remove_item" value="1" 
                                                        onclick="this.form.product_id.value='<?php echo $item['product_id']; ?>'"
                                                        class="btn-icon-remove" title="Remove Item">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <input type="hidden" name="product_id" value="">
                        
                        <div class="cart-footer-actions">
                            <a href="shop.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                            <div class="bulk-actions">
                                <button type="submit" name="clear_cart" class="btn btn-outline btn-danger" 
                                        onclick="return confirm('Ar you sure you want to empty your cart?')">
                                    <i class="fas fa-trash-alt"></i> Clear Cart
                                </button>
                                <button type="submit" name="update_cart" class="btn btn-secondary">
                                    <i class="fas fa-sync-alt"></i> Update Quantities
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <aside class="cart-summary-sidebar">
                    <div class="summary-card">
                        <h3>Order Summary</h3>
                        
                        <div class="summary-details">
                            <div class="summary-line">
                                <span>Subtotal</span>
                                <span><?php echo formatPrice($subtotal); ?></span>
                            </div>
                            <div class="summary-line">
                                <span>Shipping</span>
                                <?php if ($shipping_cost == 0): ?>
                                    <span class="free-shipping">FREE</span>
                                <?php else: ?>
                                    <span><?php echo formatPrice($shipping_cost); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($subtotal > 0 && $subtotal < $shipping_threshold): ?>
                                <div class="shipping-hint">
                                    <i class="fas fa-truck"></i>
                                    Add <strong><?php echo formatPrice($shipping_threshold - $subtotal); ?></strong> more for FREE shipping!
                                </div>
                            <?php elseif ($subtotal >= $shipping_threshold): ?>
                                <div class="shipping-hint success">
                                    <i class="fas fa-check"></i> You've unlocked <strong>FREE shipping</strong>!
                                </div>
                            <?php endif; ?>
                            
                            <div class="summary-total-line">
                                <span>Total Amount</span>
                                <span class="grand-total"><?php echo formatPrice($grand_total); ?></span>
                            </div>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-primary btn-large btn-block">
                            <i class="fas fa-credit-card"></i> Proceed to Checkout
                        </a>
                        
                        <div class="payment-badges">
                            <i class="fab fa-cc-visa"></i>
                            <i class="fab fa-cc-mastercard"></i>
                            <i class="fab fa-cc-paypal"></i>
                            <i class="fab fa-cc-apple-pay"></i>
                        </div>
                    </div>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
