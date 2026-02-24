<?php
require_once 'config/config.php';
require_once 'config/database.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$error = '';

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
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
    if (!isLoggedIn()) {
        setFlashMessage('error', 'You must be logged in to leave a review.');
    } else {
        $user_id = $_SESSION['user_id'];
        $rating = (int)$_POST['rating'];
        $comment = $conn->real_escape_string($_POST['comment']);
        
        // Purchase check
        $purchase_sql = "SELECT oi.product_id FROM order_items oi 
                        JOIN orders o ON oi.order_id = o.order_id 
                        WHERE o.user_id = $user_id AND oi.product_id = $product_id AND o.order_status = 'Delivered'";
        $purchase_result = $conn->query($purchase_sql);
        
        if ($purchase_result && $purchase_result->num_rows > 0) {
            $insert_review = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES ($product_id, $user_id, $rating, '$comment')";
            if ($conn->query($insert_review)) {
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
        WHERE p.product_id = $product_id";
$result = $conn->query($sql);

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
                      WHERE o.user_id = $user_id AND oi.product_id = $product_id AND o.order_status = 'Delivered'";
    $v_result = $conn->query($check_purchase);
    if ($v_result && $v_result->num_rows > 0) {
        $is_verified_buyer = true;
    }
}

// Fetch reviews
$reviews_sql = "SELECT r.*, u.full_name FROM reviews r 
                JOIN users u ON r.user_id = u.user_id 
                WHERE r.product_id = $product_id 
                ORDER BY r.review_date DESC";
$reviews = $conn->query($reviews_sql);

// Calculate average rating
$rating_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id = $product_id";
$rating_result = $conn->query($rating_sql);
$rating_data = $rating_result->fetch_assoc();
$avg_rating = round($rating_data['avg_rating'] ?? 0, 1);
$total_reviews = $rating_data['total_reviews'];

$page_title = $product['product_name'] . ' - Melody Masters';
include 'includes/header.php';
?>

<section class="product-detail-section">
    <div class="container">
        
        <div class="product-detail-grid">
            <!-- Product Image -->
            <div class="product-detail-image">
                <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                     alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                     onerror="this.src='assets/images/placeholder.jpg';">
            </div>
            
            <!-- Product Info -->
            <div class="product-detail-info">
                <p class="product-category">
                    <a href="shop.php?category=<?php echo $product['category_id']; ?>">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </a>
                </p>
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                
                <div class="product-rating">
                    <div class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?php echo $i <= $avg_rating ? '' : '-o'; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-text"><?php echo $avg_rating; ?> / 5 (<?php echo $total_reviews; ?> Reviews)</span>
                </div>
                
                <div class="product-meta">
                    <p><span>Brand:</span> <?php echo htmlspecialchars($product['brand']); ?></p>
                    <p><span>Category:</span> <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p><span>Availability:</span> 
                        <?php if ($product['stock'] > 0): ?>
                            <span class="in-stock">In Stock (<?php echo $product['stock']; ?> units)</span>
                        <?php else: ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="product-price-box">
                    <span class="price-label">Price</span>
                    <span class="price"><?php echo formatPrice($product['price']); ?></span>
                </div>
                
                <?php if ($product['stock'] > 0): ?>
                    <form method="POST" action="" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <div class="quantity-selector">
                            <label for="quantity">Quantity</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                        </div>
                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-large btn-block">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                    </form>
                <?php endif; ?>
                
                <div class="product-description">
                    <h3>About this product</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
            </div>
        </div>
        
        <div class="reviews-section">
            <div class="section-header">
                <h2>Customer Feedback</h2>
                <span class="reviews-total"><?php echo $total_reviews; ?> Verified Reviews</span>
            </div>
            
            <?php if ($is_verified_buyer): ?>
                <div class="review-form-box">
                    <h3>Write a Review</h3>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Rating</label>
                            <div class="rating-input">
                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comment">Your Comment</label>
                            <textarea name="comment" id="comment" rows="4" placeholder="Share your experience with this instrument..." required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            <?php elseif (isLoggedIn()): ?>
                <div class="info-alert">
                    <i class="fas fa-info-circle"></i> Only customers who have purchased this product can leave a review.
                </div>
            <?php else: ?>
                <div class="info-alert">
                    <i class="fas fa-user-lock"></i> Please <a href="login.php">login</a> to see if you can review this product.
                </div>
            <?php endif; ?>

            <div class="reviews-list">
                <?php if ($reviews && $reviews->num_rows > 0): ?>
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="avatar"><?php echo strtoupper(substr($review['full_name'], 0, 1)); ?></div>
                                    <div>
                                        <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                                        <span class="verified-badge"><i class="fas fa-check-circle"></i> Verified Purchase</span>
                                    </div>
                                </div>
                                <div class="review-meta">
                                    <div class="review-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="review-date"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></span>
                                </div>
                            </div>
                            <div class="review-body">
                                <p><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-reviews">
                        <i class="far fa-comments"></i>
                        <p>No reviews yet. Be the first to share your thoughts!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
