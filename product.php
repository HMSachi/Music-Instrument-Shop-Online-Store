<?php
require_once 'config/config.php';
require_once 'config/database.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Security validation failed.');
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
        exit();
    }

    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    setFlashMessage('success', 'Product added to cart!');
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit();
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Security validation failed.');
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
        exit();
    }

    if (!isLoggedIn()) {
        setFlashMessage('error', 'You must be logged in to leave a review.');
    } else {
        $user_id = $_SESSION['user_id'];
        $rating = (int)$_POST['rating'];
        $comment = sanitizeInput($_POST['comment']);
        
        // Purchase check using preparedQuery
        $purchase_sql = "SELECT oi.product_id FROM order_items oi 
                        JOIN orders o ON oi.order_id = o.order_id 
                        WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = 'Delivered'";
        $purchase_result = preparedQuery($conn, $purchase_sql, [$user_id, $product_id], "ii");
        
        if ($purchase_result && $purchase_result->num_rows > 0) {
            $insert_review = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
            if (preparedQuery($conn, $insert_review, [$product_id, $user_id, $rating, $comment], "iiis")) {
                setFlashMessage('success', 'Thank you! Your review has been posted.');
            } else {
                setFlashMessage('error', 'Failed to submit review. Please try again.');
            }
        } else {
            setFlashMessage('error', 'Only verified buyers can review this product.');
        }
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit();
}

// Fetch product details
$sql = "SELECT p.*, c.category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.product_id = ?";
$result = preparedQuery($conn, $sql, [$product_id], "i");

if (!$result || $result->num_rows === 0) {
    redirect('shop.php');
}

$product = $result->fetch_assoc();

// Check if current user is a verified buyer
$is_verified_buyer = false;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $check_purchase = "SELECT oi.product_id FROM order_items oi 
                      JOIN orders o ON oi.order_id = o.order_id 
                      WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = 'Delivered'";
    $v_result = preparedQuery($conn, $check_purchase, [$user_id, $product_id], "ii");
    if ($v_result && $v_result->num_rows > 0) {
        $is_verified_buyer = true;
    }
}

// Fetch reviews
$reviews_sql = "SELECT r.*, u.full_name FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.product_id = ? 
                ORDER BY r.review_date DESC";
$reviews = preparedQuery($conn, $reviews_sql, [$product_id], "i");

// Calculate average rating
$rating_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = ?";
$rating_result = preparedQuery($conn, $rating_sql, [$product_id], "i");
$rating_data = $rating_result->fetch_assoc();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total_reviews'];

$page_title = $product['product_name'] . ' - Melody Masters';
include 'includes/header.php';
?>

<section class="product-detail-section" style="padding: 8rem 0;">
    <div class="container">
        <div class="product-detail-grid animate-fade-in">
            <!-- Product Image -->
            <div class="product-detail-image">
                <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                     onerror="this.src='assets/images/placeholder.jpg';">
            </div>
            
            <!-- Product Info -->
            <div class="product-detail-info">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <span style="background: rgba(212, 175, 55, 0.1); color: var(--primary); padding: 0.4rem 1rem; border-radius: var(--radius-full); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </span>
                    <span style="color: var(--text-muted); font-size: 0.85rem;">Brand: <?php echo htmlspecialchars($product['brand']); ?></span>
                </div>
                
                <h1 class="text-gold" style="margin-bottom: 1.5rem;"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                
                <div class="product-rating" style="margin-bottom: 2.5rem;">
                    <div class="review-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star <?php echo $i <= $avg_rating ? 'active' : ''; ?>"></i>
                        <?php endfor; ?>
                        <span style="color: var(--text-muted); font-size: 0.9rem; margin-left: 0.5rem;">
                            <?php echo $avg_rating; ?> / 5 (<?php echo $total_reviews; ?> reviews)
                        </span>
                    </div>
                </div>
                
                <div class="product-price-box">
                    <span class="price-label">Investment</span>
                    <span class="price"><?php echo formatPrice($product['price']); ?></span>
                </div>
                
                <div class="stock-status" style="margin-bottom: 2.5rem;">
                    <?php if ($product['stock'] > 0): ?>
                        <span style="color: var(--success); display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                            <i class="fas fa-check-circle"></i> In Stock & Ready to Ship (<?php echo $product['stock']; ?> available)
                        </span>
                    <?php else: ?>
                        <span style="color: var(--error); display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                            <i class="fas fa-times-circle"></i> Currently Out of Stock
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['stock'] > 0): ?>
                    <form method="POST" action="" class="glass-card" style="padding: 2.5rem; margin-bottom: 3rem; background: rgba(255,255,255,0.02);">
                        <?php echo csrfInput(); ?>
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        
                        <div style="display: flex; gap: 1.5rem; align-items: flex-end;">
                            <div class="form-group" style="margin: 0;">
                                <label class="form-label" for="quantity">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" style="width: 100px; text-align: center;" value="1" min="1" max="<?php echo $product['stock']; ?>">
                            </div>
                            <button type="submit" name="add_to_cart" class="btn btn-primary" style="flex: 1; height: 52px;">
                                <i class="fas fa-shopping-bag"></i> Add to Purchase
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
                
                <div class="product-description">
                    <h3 style="color: var(--text-main); margin-bottom: 1.5rem; font-size: 1.25rem;">The Experience</h3>
                    <p style="color: var(--text-muted); line-height: 1.8; font-size: 1.05rem;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="reviews-section animate-fade-in" style="margin-top: 8rem;">
            <div style="margin-bottom: 4rem; text-align: center;">
                <h2 class="text-gold" style="font-size: 2.5rem; margin-bottom: 1rem;">Customer Reviews</h2>
                <div class="review-summary-large" style="display: flex; justify-content: center; align-items: center; gap: 2rem;">
                    <div style="font-size: 3.5rem; font-weight: 800; color: var(--text-main);"><?php echo $avg_rating; ?></div>
                    <div style="text-align: left;">
                        <div class="review-stars" style="font-size: 1.25rem; color: var(--primary); margin-bottom: 0.25rem;">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $avg_rating ? 'active' : ''; ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <span style="color: var(--text-muted);"><?php echo $total_reviews; ?> reviews in total</span>
                    </div>
                </div>
            </div>
            
            <?php if ($is_verified_buyer): ?>
                <div class="glass-card" style="margin-bottom: 5rem; padding: 4rem;">
                    <h3 style="margin-bottom: 2rem; text-align: center;">Share Your Experience</h3>
                    <form method="POST" action="" style="max-width: 600px; margin: 0 auto;">
                        <?php echo csrfInput(); ?>
                        <div class="form-group" style="text-align: center;">
                            <label class="form-label" style="display: block; margin-bottom: 1rem;">Your Rating</label>
                            <div class="rating-input" style="display: flex; gap: 1rem; justify-content: center; font-size: 2rem; color: #555;">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <label style="cursor: pointer;">
                                        <input type="radio" name="rating" value="<?php echo $i; ?>" style="display: none;" required>
                                        <i class="fas fa-star" onclick="
                                            let stars = this.parentElement.parentElement.querySelectorAll('i');
                                            stars.forEach((s, idx) => s.style.color = idx < <?php echo $i; ?> ? 'var(--primary)' : '#555');
                                        "></i>
                                    </label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 2.5rem;">
                            <label class="form-label" for="comment">Your Review</label>
                            <textarea name="comment" id="comment" class="form-control" rows="4" placeholder="How's your new instrument sounding?" required></textarea>
                        </div>
                        <div style="text-align: center; margin-top: 2.5rem;">
                            <button type="submit" name="submit_review" class="btn btn-primary btn-lg">Post My Review</button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="reviews-list" style="max-width: 900px; margin: 0 auto;">
                <?php if ($reviews && $reviews->num_rows > 0): ?>
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <div class="review-card animate-fade-in">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="avatar"><?php echo strtoupper(substr($review['full_name'], 0, 1)); ?></div>
                                    <div>
                                        <div style="color: var(--text-main); font-weight: 700;"><?php echo htmlspecialchars($review['full_name']); ?></div>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Buyer</span>
                                    </div>
                                </div>
                                <div style="text-align: right;">
                                    <div class="review-stars" style="color: var(--primary); margin-bottom: 0.5rem;">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span style="color: var(--text-muted); font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></span>
                                </div>
                            </div>
                            <div class="review-body">
                                <p style="color: var(--text-muted); line-height: 1.7;"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="glass-card text-center" style="padding: 5rem 3rem;">
                        <i class="far fa-comments" style="font-size: 4rem; color: rgba(255,255,255,0.05); margin-bottom: 2rem; display: block;"></i>
                        <h3 style="color: var(--text-main); margin-bottom: 1rem;">No Reviews Yet</h3>
                        <p style="color: var(--text-muted);">Be the first to share your experience with this instrument.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
