<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

$base = defined('BASE_PATH') ? BASE_PATH : '/Music-Instrument-Shop-Online-Store';
$pm = new ProductManager($db);
$products = $pm->get_products();
$featured = array_slice($products, 0, 8);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters | Premium Music Instruments Store</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/index.css">
</head>
<body>
    <!-- Premium Header -->
    <header class="premium-header">
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/products_store.php" class="nav-link">🛍️ Shop</a></li>
                <li><a href="<?php echo $base; ?>/products.php" class="nav-link">📚 Browse</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('admin')): ?>
                        <li><a href="<?php echo $base; ?>/admin/dashboard.php" class="nav-link">⚙️ Admin</a></li>
                    <?php elseif (has_role('staff')): ?>
                        <li><a href="<?php echo $base; ?>/staff/dashboard.php" class="nav-link">📋 Staff</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base; ?>/customer/dashboard.php" class="nav-link">👤 Account</a></li>
                        <li><a href="<?php echo $base; ?>/cart.php" class="nav-link cart-link">
                            🛒 Cart
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="cart-badge"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base; ?>/public/logout.php" class="nav-link">🚪 Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>/login.php" class="nav-link">🔐 Login</a></li>
                    <li><a href="<?php echo $base; ?>/signup.php" class="nav-link">✨ Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Hero Section with Parallax -->
        <section class="hero-premium">
            <div class="hero-background"></div>
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">Unleash Your Musical Potential</h1>
                    <p class="hero-subtitle">Discover world-class instruments, professional studio gear, and digital assets</p>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <span class="stat-number">1000+</span>
                            <span class="stat-label">Premium Products</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">5000+</span>
                            <span class="stat-label">Happy Musicians</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">24/7</span>
                            <span class="stat-label">Support</span>
                        </div>
                    </div>
                    <div class="hero-buttons">
                        <a class="btn btn-primary btn-hero" href="<?php echo $base; ?>/products_store.php">
                            <span class="btn-icon">🛍️</span>
                            <span>Start Shopping</span>
                        </a>
                        <?php if (!is_logged_in()): ?>
                            <a class="btn btn-secondary btn-hero" href="<?php echo $base; ?>/signup.php">
                                <span class="btn-icon">✨</span>
                                <span>Join Community</span>
                            </a>
                        <?php else: ?>
                            <a class="btn btn-secondary btn-hero" href="<?php echo $base; ?>/customer/dashboard.php">
                                <span class="btn-icon">📊</span>
                                <span>My Dashboard</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-visual">
                    <div class="hero-circle"></div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Why Choose Melody Masters?</h2>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🎸</div>
                        <h3>Premium Quality</h3>
                        <p>Handpicked instruments from world-renowned brands</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🚚</div>
                        <h3>Fast Shipping</h3>
                        <p>Free worldwide shipping on orders over $100</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🔒</div>
                        <h3>Secure Payment</h3>
                        <p>256-bit SSL encryption for your safety</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💰</div>
                        <h3>Best Prices</h3>
                        <p>Competitive pricing with regular exclusive deals</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">⭐</div>
                        <h3>Expert Reviews</h3>
                        <p>Detailed product reviews from music professionals</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📞</div>
                        <h3>Support Team</h3>
                        <p>Expert assistance to help you choose the perfect gear</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Premium Categories Showcase -->
        <section class="categories-showcase">
            <div class="container">
                <h2 class="section-title">Shop by Category</h2>
                <div class="categories-grid">
                    <div class="category-card">
                        <div class="category-image">🎸</div>
                        <h3>Guitars</h3>
                        <p>Acoustic & Electric</p>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link">Explore →</a>
                    </div>
                    <div class="category-card">
                        <div class="category-image">🎹</div>
                        <h3>Keyboards</h3>
                        <p>Pianos & Synths</p>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link">Explore →</a>
                    </div>
                    <div class="category-card">
                        <div class="category-image">🥁</div>
                        <h3>Drums</h3>
                        <p>Kits & Percussion</p>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link">Explore →</a>
                    </div>
                    <div class="category-card">
                        <div class="category-image">🎤</div>
                        <h3>Microphones</h3>
                        <p>Studio & Live</p>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link">Explore →</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products Section -->
        <section class="featured-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">Featured Collection</h2>
                    <p class="section-subtitle">Handpicked instruments curated for excellence</p>
                </div>
                
                <div class="products-carousel">
                    <?php if (empty($featured)): ?>
                        <div class="empty-state">
                            <p>🎵 No products available yet. Check back soon!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($featured as $product): ?>
                            <div class="product-card-premium">
                                <div class="product-badge">
                                    <?php if ($product['product_type'] === 'digital'): ?>
                                        <span class="badge-digital">Digital</span>
                                    <?php elseif ((int)$product['stock'] > 0): ?>
                                        <span class="badge-instock">In Stock</span>
                                    <?php else: ?>
                                        <span class="badge-outstock">Sold Out</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-image-container">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                             class="product-image-main">
                                    <?php else: ?>
                                        <div class="product-image-placeholder">
                                            <span>🎵</span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-overlay">
                                        <a href="<?php echo $base; ?>/product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-overlay">Quick View</a>
                                    </div>
                                </div>
                                <div class="product-details">
                                    <div class="product-category-tag"><?php echo htmlspecialchars($product['category_name']); ?></div>
                                    <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    
                                    <?php if (!empty($product['brand'])): ?>
                                        <p class="product-brand">by <?php echo htmlspecialchars($product['brand']); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="product-rating">
                                        <span class="stars">⭐⭐⭐⭐⭐</span>
                                        <span class="rating-count">(24)</span>
                                    </div>
                                    
                                    <div class="product-price-section">
                                        <span class="product-price">$<?php echo number_format($product['price'], 2); ?></span>
                                    </div>
                                    
                                    <p class="product-stock-status">
                                        <?php if ($product['product_type'] === 'physical'): ?>
                                            <?php if ((int)$product['stock'] > 0): ?>
                                                <span class="status-available">✓ In stock (<?php echo (int)$product['stock']; ?>)</span>
                                            <?php else: ?>
                                                <span class="status-unavailable">✗ Out of stock</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="status-available">✓ Digital download</span>
                                        <?php endif; ?>
                                    </p>
                                    
                                    <a class="btn btn-primary btn-card" href="<?php echo $base; ?>/product.php?id=<?php echo $product['product_id']; ?>">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-content">
                    <h2>Ready to Elevate Your Music?</h2>
                    <p>Join thousands of musicians who trust Melody Masters for their gear</p>
                    <a href="<?php echo $base; ?>/products_store.php" class="btn btn-primary btn-large">Browse All Products</a>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="newsletter-section">
            <div class="container">
                <div class="newsletter-content">
                    <h3>Stay Updated</h3>
                    <p>Get exclusive deals and latest products delivered to your inbox</p>
                    <form class="newsletter-form" onsubmit="return false;">
                        <input type="email" placeholder="Enter your email" required>
                        <button type="submit" class="btn btn-primary">Subscribe</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Premium Footer -->
    <footer class="premium-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h4>About Us</h4>
                    <p>Melody Masters is your trusted source for premium musical instruments and professional audio equipment.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo $base; ?>/products_store.php">Shop</a></li>
                        <li><a href="<?php echo $base; ?>/products.php">Browse</a></li>
                        <li><a href="#">About</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="#">FAQs</a></li>
                        <li><a href="#">Shipping Info</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Warranty</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#" class="social-link">Facebook</a>
                        <a href="#" class="social-link">Instagram</a>
                        <a href="#" class="social-link">YouTube</a>
                        <a href="#" class="social-link">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Melody Masters. All rights reserved.</p>
                <p>Your Premier Source for Musical Excellence</p>
            </div>
        </div>
    </footer>
</body>
</html>
