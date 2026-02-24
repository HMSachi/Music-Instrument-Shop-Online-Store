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
                <div class="user-profile">
                    <i class="fas fa-user-circle"></i>
                    <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p><?php echo htmlspecialchars($user['email']); ?></p>
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
                                <div class="order-card">
                                    <div class="order-header">
                                        <div>
                                            <h4>Order #<?php echo $order['order_id']; ?></h4>
                                            <p class="order-date">
                                                <i class="fas fa-calendar"></i>
                                                <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                            </p>
                                        </div>
                                        <span class="order-status status-<?php echo strtolower($order['order_status']); ?>">
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
                                    
                                    <div class="order-items">
                                        <?php while ($item = $items->fetch_assoc()): ?>
                                            <div class="order-item">
                                                <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                     alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                     onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder.jpg';">
                                                <div class="item-info">
                                                    <h5><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                                    <p>Qty: <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?></p>
                                                    
                                                    <div class="item-actions">
                                                        <?php if ($item['product_type'] === 'digital'): 
                                                            $limit = $item['download_limit'] ?? 3;
                                                            $count = $item['download_count'] ?? 0;
                                                            $remaining = max(0, $limit - $count);
                                                            $is_disabled = ($remaining <= 0);
                                                        ?>
                                                            <div class="download-container">
                                                                <p class="download-counter <?php echo $is_disabled ? 'limit-reached' : ''; ?>">
                                                                    <small>Downloads: <?php echo $count; ?>/<?php echo $limit; ?></small>
                                                                </p>
                                                                <a href="<?php echo SITE_URL; ?>/download.php?id=<?php echo $item['product_id']; ?>" 
                                                                   class="btn-sm <?php echo $is_disabled ? 'btn-disabled' : 'btn-secondary'; ?> mt-1">
                                                                    <i class="fas fa-download"></i> <?php echo $is_disabled ? 'Limit Reached' : 'Download'; ?>
                                                                </a>
                                                            </div>
                                                        <?php endif; ?>

                                                        <?php if ($order['order_status'] === 'Delivered'): ?>
                                                            <button class="btn-sm btn-outline mt-1" onclick="toggleReview(<?php echo $item['product_id']; ?>)">
                                                                <i class="fas fa-star"></i> Write Review
                                                            </button>

                                                            <!-- Review Form (Hidden) -->
                                                            <div id="review-form-<?php echo $item['product_id']; ?>" class="review-form-inline" style="display: none;">
                                                                <form method="POST" action="">
                                                                    <?php echo csrfInput(); ?>
                                                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                                    <div class="form-group">
                                                                        <label>Rating</label>
                                                                        <select name="rating" required>
                                                                            <option value="5">5 - Excellent</option>
                                                                            <option value="4">4 - Very Good</option>
                                                                            <option value="3">3 - Good</option>
                                                                            <option value="2">2 - Fair</option>
                                                                            <option value="1">1 - Poor</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Your Review</label>
                                                                        <textarea name="comment" rows="3" placeholder="Share your experience..."></textarea>
                                                                    </div>
                                                                    <button type="submit" name="submit_review" class="btn-sm btn-primary">Submit</button>
                                                                    <button type="button" class="btn-sm btn-link" onclick="toggleReview(<?php echo $item['product_id']; ?>)">Cancel</button>
                                                                </form>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                    
                                    <div class="order-footer">
                                        <div class="order-total">
                                            <strong>Total:</strong> <?php echo formatPrice($order['total_amount']); ?>
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
                <div class="content-section" id="profile">
                    <h2>Profile Information</h2>
                    
                    <form method="POST" action="" class="profile-update-form">
                        <?php echo csrfInput(); ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">Full Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email (Cannot be changed)</label>
                                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="created_at">Member Since</label>
                                <input type="text" id="created_at" value="<?php echo date('M d, Y', strtotime($user['created_at'])); ?>" disabled>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Shipping Address</label>
                            <textarea id="address" name="address" rows="4"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
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
