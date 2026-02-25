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

<section class="dashboard-section" style="padding: 6rem 0;">
    <div class="container">
        <div style="margin-bottom: 3rem;">
            <h1 class="text-gold">My Dashboard</h1>
            <p style="color: var(--text-muted);">Manage your orders and account details.</p>
        </div>
        
        <?php if ($order_placed): ?>
            <div class="alert alert-success animate-fade-in" style="margin-bottom: 2rem;">
                <i class="fas fa-check-circle"></i> Your order has been placed successfully! Welcome to the Melody Masters family.
            </div>
        <?php endif; ?>

        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success animate-fade-in" style="margin-bottom: 2rem;">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error animate-fade-in" style="margin-bottom: 2rem;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-grid animate-fade-in">
            <!-- Sidebar -->
            <aside class="dashboard-sidebar">
                <div class="glass-card card-shimmer text-center">
                    <div style="width: 100px; height: 100px; background: var(--gold-gradient); color: var(--bg-dark); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 800; margin: 0 auto 2rem; box-shadow: 0 0 30px rgba(212, 175, 55, 0.3);">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h3 style="color: var(--text-main); margin-bottom: 0.5rem;"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2.5rem;"><?php echo htmlspecialchars($user['email']); ?></p>
                    
                    <nav class="dashboard-nav">
                        <a href="dashboard.php" class="<?php echo !isset($_GET['tab']) || $_GET['tab'] == 'orders' ? 'active' : ''; ?>">
                            <i class="fas fa-shopping-bag"></i> My Orders
                        </a>
                        <a href="?tab=profile" class="<?php echo isset($_GET['tab']) && $_GET['tab'] == 'profile' ? 'active' : ''; ?>">
                            <i class="fas fa-user-circle"></i> Account Profile
                        </a>
                        <a href="<?php echo SITE_URL; ?>/logout.php" style="margin-top: 1rem; color: var(--error);">
                            <i class="fas fa-sign-out-alt"></i> Sign Out
                        </a>
                    </nav>
                </div>
            </aside>
            
            <!-- Content -->
            <div class="dashboard-content">
                <?php if (!isset($_GET['tab']) || $_GET['tab'] == 'orders'): ?>
                    <h2 style="margin-bottom: 2rem; color: var(--text-main);">Order History</h2>
                    
                    <?php if ($orders && $orders->num_rows > 0): ?>
                        <div class="orders-list">
                            <?php while ($order = $orders->fetch_assoc()): ?>
                                <div class="glass-card order-card card-shimmer" style="padding: 0; overflow: hidden;">
                                    <div class="order-card-header">
                                        <div>
                                            <span style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px;">Order</span>
                                            <h4 style="margin: 0; color: var(--primary);">#<?php echo $order['order_id']; ?></h4>
                                        </div>
                                        <div style="text-align: right;">
                                            <span style="color: var(--text-muted); font-size: 0.85rem; display: block; margin-bottom: 0.25rem;">
                                                <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                            </span>
                                            <span class="badge badge-<?php echo strtolower($order['order_status']); ?>">
                                                <?php echo $order['order_status']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="order-card-body">
                                        <?php
                                        $items_sql = "SELECT oi.*, p.product_name, p.product_type, p.image 
                                                       FROM order_items oi 
                                                       JOIN products p ON oi.product_id = p.product_id 
                                                       WHERE oi.order_id = ?";
                                        $items = preparedQuery($conn, $items_sql, [$order['order_id']], "i");
                                        ?>
                                        
                                        <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                                            <?php while ($item = $items->fetch_assoc()): ?>
                                                <div style="display: flex; align-items: center; gap: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                                    <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($item['image'] ? ltrim($item['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                         style="width: 70px; height: 70px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                                                    <div style="flex: 1;">
                                                        <h5 style="margin: 0; color: var(--text-main); font-size: 1.1rem;"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                                        <p style="margin: 0; color: var(--text-muted); font-size: 0.9rem;">
                                                            <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?>
                                                        </p>
                                                    </div>
                                                    
                                                    <div style="display: flex; gap: 0.75rem;">
                                                        <?php if ($item['product_type'] === 'digital'): ?>
                                                            <a href="<?php echo SITE_URL; ?>/download.php?id=<?php echo $item['product_id']; ?>" 
                                                               class="btn btn-sm btn-primary">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        <?php endif; ?>

                                                        <?php if ($order['order_status'] === 'Delivered'): ?>
                                                            <button class="btn btn-sm btn-secondary" onclick="toggleReview(<?php echo $item['product_id']; ?>)">
                                                                <i class="fas fa-star"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>

                                                <?php if ($order['order_status'] === 'Delivered'): ?>
                                                    <div id="review-form-<?php echo $item['product_id']; ?>" style="display: none; padding: 2.5rem; background: rgba(255,255,255,0.02); border-radius: var(--radius-md); margin-top: 1rem;">
                                                        <h4 style="margin-bottom: 2rem;">Post a Review for <?php echo htmlspecialchars($item['product_name']); ?></h4>
                                                        <form method="POST" action="">
                                                            <?php echo csrfInput(); ?>
                                                            <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                            
                                                            <div class="form-group">
                                                                <label class="form-label">Rating</label>
                                                                <div class="rating-input" style="display: flex; gap: 0.75rem; font-size: 1.5rem; color: #555;">
                                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                        <label style="cursor: pointer; transition: var(--transition);">
                                                                            <input type="radio" name="rating" value="<?php echo $i; ?>" required style="display: none;">
                                                                            <i class="fas fa-star" onclick="this.parentElement.parentElement.querySelectorAll('i').forEach((s, idx) => s.style.color = idx < <?php echo $i; ?> ? 'var(--primary)' : '#555')"></i>
                                                                        </label>
                                                                    <?php endfor; ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="form-group" style="margin-top: 1.5rem;">
                                                                <label class="form-label">Your Experience</label>
                                                                <textarea name="comment" class="form-control" rows="3" placeholder="How's your new instrument?"></textarea>
                                                            </div>
                                                            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                                                                <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                                                                <button type="button" class="btn btn-secondary" onclick="toggleReview(<?php echo $item['product_id']; ?>)">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endwhile; ?>
                                        </div>
                                        
                                        <div style="margin-top: 2.5rem; display: flex; justify-content: flex-end; align-items: center; gap: 1rem;">
                                            <span style="color: var(--text-muted);">Total Amount:</span>
                                            <span class="text-gold" style="font-size: 1.5rem; font-weight: 700;"><?php echo formatPrice($order['total_amount']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="glass-card text-center" style="padding: 6rem 3rem;">
                            <div style="font-size: 4rem; color: rgba(255,255,255,0.05); margin-bottom: 2rem;">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3 style="color: var(--text-main);">No orders found</h3>
                            <p style="color: var(--text-muted); margin-bottom: 2.5rem;">You haven't purchased any instruments yet.</p>
                            <a href="<?php echo SITE_URL; ?>/shop.php" class="btn btn-primary">
                                Explore Shop
                            </a>
                        </div>
                    <?php endif; ?>
                
                <?php elseif (isset($_GET['tab']) && $_GET['tab'] == 'profile'): ?>
                    <h2 style="margin-bottom: 2rem; color: var(--text-main);">Account Profile</h2>
                    
                    <div class="glass-card card-shimmer" style="padding: 3.5rem;">
                        <form method="POST" action="">
                            <?php echo csrfInput(); ?>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                                <div class="form-group">
                                    <label class="form-label" for="full_name">Full Name</label>
                                    <input type="text" id="full_name" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" for="email">Email Address</label>
                                    <input type="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="opacity: 0.6;">
                                </div>
                            </div>

                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                                <div class="form-group">
                                    <label class="form-label" for="phone">Phone Number</label>
                                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+44 123 456 7890">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Member Since</label>
                                    <input type="text" class="form-control" value="<?php echo date('F d, Y', strtotime($user['created_at'])); ?>" disabled style="opacity: 0.6;">
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 3rem;">
                                <label class="form-label" for="address">Default Shipping Address</label>
                                <textarea id="address" name="address" class="form-control" rows="4" placeholder="Street, City, Postal Code..."><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                            </div>

                            <button type="submit" name="update_profile" class="btn btn-primary">
                                <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Save Changes
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
