<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

$base = BASE_PATH;
$pm = new ProductManager($db);

if (!isset($_GET['id'])) {
    header('Location: ' . $base . '/products.php');
    exit();
}

$product = $pm->get_product((int)$_GET['id']);
if (!$product) {
    header('Location: ' . $base . '/products.php');
    exit();
}

$reviews = $pm->get_product_reviews($product['product_id']);
$rating = $pm->get_average_rating($product['product_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['product_name']); ?> - Melody Masters</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/product-detail.css">
</head>
<body>
    <header class="premium-header">
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/products.php" class="nav-link">Products</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('admin')): ?>
                        <li><a href="<?php echo $base; ?>/admin/dashboard.php" class="nav-link">Admin</a></li>
                    <?php elseif (has_role('staff')): ?>
                        <li><a href="<?php echo $base; ?>/staff/dashboard.php" class="nav-link">Staff</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base; ?>/customer/dashboard.php" class="nav-link">My Account</a></li>
                        <li><a href="<?php echo $base; ?>/cart.php" class="nav-link cart-link">🛒 Cart<?php if (isset($_SESSION['cart'])) echo ' <span class="cart-badge">' . count($_SESSION['cart']) . '</span>'; ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base; ?>/public/logout.php" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>/login.php" class="nav-link">Login</a></li>
                    <li><a href="<?php echo $base; ?>/signup.php" class="nav-link">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container">
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
            <a href="<?php echo $base; ?>/products.php" class="breadcrumb-link">← Back to Products</a>
        </div>

        <!-- Product Container -->
        <div class="product-detail-container">
            <!-- Product Gallery Section -->
            <div class="product-gallery">
                <div class="main-image-wrapper">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                             class="main-product-image">
                    <?php else: ?>
                        <div class="image-placeholder">
                            <span class="placeholder-icon">🎸</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Information Section -->
            <div class="product-info-section">
                <!-- Header -->
                <div class="product-header">
                    <div>
                        <p class="product-breadcrumb"><?php echo htmlspecialchars($product['category_name'] ?? 'Category'); ?></p>
                        <h1 class="product-title"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                    </div>
                    <?php if (!empty($product['brand'])): ?>
                        <div class="brand-badge">
                            <?php echo htmlspecialchars($product['brand']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Rating Section -->
                <div class="rating-section">
                    <div class="rating-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <span class="star <?php echo ($i < round($rating['avg_rating'])) ? 'filled' : 'empty'; ?>">★</span>
                        <?php endfor; ?>
                        <span class="rating-value"><?php echo number_format($rating['avg_rating'], 1); ?></span>
                    </div>
                    <span class="review-count">(<?php echo (int)$rating['total_reviews']; ?> reviews)</span>
                </div>

                <!-- Price Section -->
                <div class="price-section">
                    <span class="price">$<?php echo number_format($product['price'], 2); ?></span>
                </div>

                <!-- Stock Status -->
                <div class="stock-section">
                    <?php if ($product['product_type'] === 'physical'): ?>
                        <?php if ((int)$product['stock'] > 0): ?>
                            <div class="stock-available">
                                <span class="stock-indicator">●</span>
                                <span>In Stock - <?php echo (int)$product['stock']; ?> available</span>
                            </div>
                        <?php else: ?>
                            <div class="stock-unavailable">
                                <span class="stock-indicator">●</span>
                                <span>Out of Stock</span>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="stock-available">
                            <span class="stock-indicator">●</span>
                            <span>Digital Download - Instant Access</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Add to Cart Section -->
                <?php if (is_logged_in() && has_role('customer')): ?>
                    <div class="action-section">
                        <form method="POST" action="<?php echo $base; ?>/add_to_cart.php" class="cart-form">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            
                            <div class="quantity-selector">
                                <label for="quantity" class="quantity-label">Quantity:</label>
                                <div class="qty-control">
                                    <button type="button" class="qty-btn qty-minus" onclick="document.getElementById('quantity').stepDown()">−</button>
                                    <input type="number" id="quantity" name="quantity" value="1" min="1" 
                                           max="<?php echo ($product['product_type'] === 'physical') ? (int)$product['stock'] : 999; ?>" 
                                           class="qty-input">
                                    <button type="button" class="qty-btn qty-plus" onclick="document.getElementById('quantity').stepUp()">+</button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-large btn-primary">
                                <span class="btn-icon">🛒</span>
                                <span>Add to Cart</span>
                            </button>
                        </form>
                    </div>
                <?php elseif (!is_logged_in()): ?>
                    <div class="action-section">
                        <a href="<?php echo $base; ?>/login.php" class="btn btn-large btn-primary">
                            <span class="btn-icon">🔐</span>
                            <span>Login to Purchase</span>
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Quick Info -->
                <div class="quick-info">
                    <div class="info-item">
                        <span class="info-icon">📦</span>
                        <span>Free Shipping</span>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">🔄</span>
                        <span>Easy Returns</span>
                    </div>
                    <div class="info-item">
                        <span class="info-icon">✓</span>
                        <span>Quality Assured</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description Section -->
        <div class="description-section">
            <h2 class="section-title">Product Description</h2>
            <div class="description-content">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="reviews-section">
            <h2 class="section-title">Customer Reviews</h2>
            
            <?php if (empty($reviews)): ?>
                <div class="empty-reviews">
                    <span class="empty-icon">💬</span>
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
            <?php else: ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div>
                                    <h4 class="review-author"><?php echo htmlspecialchars($review['user_name']); ?></h4>
                                    <div class="review-rating">
                                        <?php for ($i = 0; $i < 5; $i++): ?>
                                            <span class="star <?php echo ($i < (int)$review['rating']) ? 'filled' : 'empty'; ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <span class="review-date"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></span>
                            </div>
                            <p class="review-comment"><?php echo htmlspecialchars($review['comment']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="premium-footer">
        <div class="footer-content">
            <p>&copy; 2026 Melody Masters. All rights reserved.</p>
            <p class="footer-tagline">Your Ultimate Music Store</p>
        </div>
    </footer>
</body>
</html>
