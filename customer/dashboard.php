<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Check if connection was successful
if (!$conn) {
    header("Location: " . SITE_URL . "/index.php");
    exit;
}

requireLogin();

$user_id = $_SESSION['user_id'];

// Get user details
$user_sql = "SELECT * FROM users WHERE user_id = ?";
$user_result = preparedQuery($conn, $user_sql, [$user_id], "i");
$user = $user_result->fetch_assoc();

// Get user orders
$orders_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$orders = preparedQuery($conn, $orders_sql, [$user_id], "i");

// Check for order success message
$order_placed = isset($_GET['order_placed']) ? true : false;

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_msg = "Security validation failed. Please try again.";
    } else {
        $full_name = sanitizeInput($_POST['full_name']);
        $phone = sanitizeInput($_POST['phone']);
        $address = sanitizeInput($_POST['address']);
        
        $update_sql = "UPDATE users SET full_name = ?, phone = ?, address = ? WHERE user_id = ?";
        if (preparedQuery($conn, $update_sql, [$full_name, $phone, $address, $user_id], "sssi")) {
            $success_msg = "Profile updated successfully!";
            // Refresh user data
            $user_result = preparedQuery($conn, $user_sql, [$user_id], "i");
            $user = $user_result->fetch_assoc();
        } else {
            $error_msg = "Failed to update profile.";
        }
    }
}

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_msg = "Security validation failed. Please try again.";
    } else {
        $product_id = (int)$_POST['product_id'];
        $rating = (int)$_POST['rating'];
        $comment = sanitizeInput($_POST['comment']);
        
        // Verify purchase and delivery status using preparedQuery
        $verify_sql = "SELECT o.order_status FROM orders o 
                       JOIN order_items oi ON o.order_id = oi.order_id 
                       WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = 'Delivered'
                       LIMIT 1";
        $verify_res = preparedQuery($conn, $verify_sql, [$user_id, $product_id], "ii");
        
        if ($verify_res && $verify_res->num_rows > 0) {
            // Check if already reviewed
            $check_rev = "SELECT review_id FROM reviews WHERE user_id = ? AND product_id = ?";
            $check_res = preparedQuery($conn, $check_rev, [$user_id, $product_id], "ii");
            
            if ($check_res && $check_res->num_rows == 0) {
                $rev_sql = "INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)";
                if (preparedQuery($conn, $rev_sql, [$user_id, $product_id, $rating, $comment], "iiis")) {
                    $success_msg = "Thank you for your review!";
                } else {
                    $error_msg = "Failed to submit review.";
                }
            } else {
                $error_msg = "You have already reviewed this product.";
            }
        } else {
            $error_msg = "You can only review instruments you've purchased and received.";
        }
    }
}

$page_title = 'My Dashboard - Melody Masters';
include '../includes/header.php';
?>

<section class="dashboard-section">
    <div class="container">
        <h1><i class="fas fa-tachometer-alt"></i> My Dashboard</h1>
        
        <?php if ($order_placed): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Your order has been placed successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-grid">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar">
                <div class="user-profile animate-fade-in-up">
                    <div class="avatar" style="width: 80px; height: 80px; font-size: 2rem; margin: 0 auto 1.5rem;">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p style="color: var(--text-light);"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                
                <nav class="dashboard-nav">
                    <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="<?php echo !isset($_GET['tab']) || $_GET['tab'] == 'orders' ? 'active' : ''; ?>"><i class="fas fa-box"></i> My Orders</a>
                    <a href="?tab=profile" class="<?php echo isset($_GET['tab']) && $_GET['tab'] == 'profile' ? 'active' : ''; ?>"><i class="fas fa-user"></i> Profile</a>
                    <a href="<?php echo SITE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </aside>
            
            <!-- Content -->
            <div class="dashboard-content">
                <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'orders'): ?>
                <div class="content-section">
                    <h2>My Orders</h2>
                    
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <div class="orders-list">
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <div class="glass-card animate-fade-in-up" style="margin-bottom: 2rem; padding: 2.5rem; overflow: hidden;">
                                    <div class="order-header" style="border-bottom: 1px solid var(--border); padding-bottom: 1.5rem; margin-bottom: 2rem;">
                                        <div>
                                            <h4 style="font-size: 1.1rem; color: var(--heading);">Order #<?php echo $order['order_id']; ?></h4>
                                            <p class="order-date" style="margin-top: 0.5rem; color: var(--text-light);">
                                                <i class="fas fa-calendar" style="margin-right: 0.5rem;"></i>
                                                <?php echo date('F d, Y', strtotime($order['order_date'])); ?>
                                            </p>
                                        </div>
                                        <span class="badge badge-<?php echo strtolower($order['order_status']); ?>" style="position: static;">
                                            <?php echo $order['order_status']; ?>
                                        </span>
                                    </div>
                                    
                                    <?php
                                    // Get order items with digital info and counts
                                    $items_sql = "SELECT oi.*, p.product_name, p.product_type, p.image, 
                                                         dp.download_limit, od.download_count 
                                                   FROM order_items oi 
                                                   JOIN products p ON oi.product_id = p.product_id 
                                                   LEFT JOIN digital_products dp ON p.product_id = dp.product_id
                                                   LEFT JOIN order_downloads od ON oi.order_item_id = od.order_item_id
                                                   WHERE oi.order_id = ?";
                                    $items = preparedQuery($conn, $items_sql, [$order['order_id']], "i");
                                    ?>
                                    
                                    <div class="order-items-grid" style="display: grid; gap: 1.5rem;">
                                        <?php while ($item = $items->fetch_assoc()): ?>
                                            <div class="order-item-row" style="display: flex; gap: 1.5rem; align-items: center; padding: 1rem; background: var(--bg-main); border-radius: var(--radius);">
                                                <div class="admin-product-thumb" style="width: 60px; height: 60px; flex-shrink: 0;">
                                                    <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                         onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder.jpg';">
                                                </div>
                                                <div class="item-info" style="flex-grow: 1;">
                                                    <h5 style="margin-bottom: 0.25rem; font-weight: 700;"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                                    <p style="font-size: 0.85rem; color: var(--text-light);">Qty: <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?></p>
                                                </div>
                                                
                                                <div class="item-actions" style="display: flex; gap: 1rem;">
                                                    <?php if ($item['product_type'] === 'digital'): 
                                                        $limit = $item['download_limit'] ?? 3;
                                                        $count = $item['download_count'] ?? 0;
                                                        $remaining = max(0, $limit - $count);
                                                        $is_disabled = ($remaining <= 0);
                                                    ?>
                                                        <a href="<?php echo SITE_URL; ?>/download.php?id=<?php echo $item['product_id']; ?>" 
                                                            class="btn btn-sm <?php echo $is_disabled ? 'btn-disabled' : 'btn-secondary'; ?>">
                                                            <i class="fas fa-download"></i> <?php echo $is_disabled ? 'Limit Reached' : 'Download'; ?>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php if ($order['order_status'] === 'Delivered'): ?>
                                                        <button class="btn btn-sm btn-outline" onclick="toggleReview(<?php echo $item['product_id']; ?>)">
                                                            <i class="fas fa-star"></i> Review
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <?php if ($order['order_status'] === 'Delivered'): ?>
                                                <!-- Review Form (Hidden) -->
                                                <div id="review-form-<?php echo $item['product_id']; ?>" class="review-form-inline glass-card" style="display: none; padding: 2rem; margin-top: 1rem;">
                                                    <h4 style="margin-bottom: 1.5rem;">Share Your Experience</h4>
                                                    <form method="POST" action="">
                                                        <?php echo csrfInput(); ?>
                                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                        <div class="form-group">
                                                            <label>Rating</label>
                                                            <div class="rating-input" style="display: flex; gap: 0.5rem; flex-direction: row-reverse; justify-content: flex-end;">
                                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star-<?php echo $item['product_id']; ?>-<?php echo $i; ?>" required style="display: none;">
                                                                    <label for="star-<?php echo $item['product_id']; ?>-<?php echo $i; ?>" style="cursor: pointer; font-size: 1.5rem; color: #ddd;"><i class="fas fa-star"></i></label>
                                                                <?php endfor; ?>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Your Review</label>
                                                            <textarea name="comment" rows="3" placeholder="How's your new instrument sounding?" style="width: 100%;"></textarea>
                                                        </div>
                                                        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                                                            <button type="submit" name="submit_review" class="btn btn-sm btn-primary">Post Review</button>
                                                            <button type="button" class="btn btn-sm btn-outline" onclick="toggleReview(<?php echo $item['product_id']; ?>)">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        <?php endwhile; ?>
                                    </div>
                                    
                                    <div class="order-footer" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                                        <div class="order-total" style="font-size: 1.25rem; font-weight: 800; color: var(--heading);">
                                            Total: <?php echo formatPrice($order['total_amount']); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <p>You haven't placed any orders yet</p>
                            <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-primary">
                                Start Shopping
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php elseif (isset($_GET['tab']) && $_GET['tab'] == 'profile'): ?>
                <div class="content-section animate-fade-in-up" id="profile">
                    <h2>Profile Information</h2>
                    
                    <form method="POST" action="" class="profile-update-form glass-card" style="padding: 3rem;">
                        <?php echo csrfInput(); ?>
                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="background: hsla(var(--p-h), 83%, 53%, 0.05); cursor: not-allowed;">
                            </div>
                        </div>

                        <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+44 123 456 7890">
                            </div>
                            <div class="form-group">
                                <label for="created_at">Member Since</label>
                                <input type="text" id="created_at" value="<?php echo date('F d, Y', strtotime($user['created_at'])); ?>" disabled style="background: hsla(var(--p-h), 83%, 53%, 0.05); cursor: not-allowed;">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 3rem;">
                            <label for="address">Primary Shipping Address</label>
                            <textarea id="address" name="address" rows="4" placeholder="Street, City, Postcode, Country"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary btn-large">
                            <i class="fas fa-save"></i> Update Profile Details
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
function toggleReview(productId) {
    const form = document.getElementById('review-form-' + productId);
    if (form.style.display === 'none') {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        form.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
