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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="modern-home">
    <!-- Modern Navigation Header -->
    <header class="modern-header" id="mainHeader">
        <nav class="nav-container">
            <div class="nav-brand">
                <i class="fas fa-music brand-icon"></i>
                <a href="<?php echo $base; ?>/index.php" class="brand-name">Melody Masters</a>
            </div>
            
            <button class="mobile-toggle" id="mobileToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?php echo $base; ?>/index.php" class="nav-link active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="<?php echo $base; ?>/products_store.php" class="nav-link"><i class="fas fa-store"></i> Shop</a></li>
                <li><a href="<?php echo $base; ?>/products.php" class="nav-link"><i class="fas fa-th-large"></i> Browse</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('admin')): ?>
                        <li><a href="<?php echo $base; ?>/admin/dashboard.php" class="nav-link"><i class="fas fa-cog"></i> Admin</a></li>
                    <?php elseif (has_role('staff')): ?>
                        <li><a href="<?php echo $base; ?>/staff/dashboard.php" class="nav-link"><i class="fas fa-clipboard"></i> Staff</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base; ?>/customer/dashboard.php" class="nav-link"><i class="fas fa-user-circle"></i> Account</a></li>
                        <li><a href="<?php echo $base; ?>/cart.php" class="nav-link cart-link">
                            <i class="fas fa-shopping-cart"></i> Cart
                            <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <span class="cart-badge"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base; ?>/public/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>/login.php" class="nav-link"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="<?php echo $base; ?>/signup.php" class="btn-signup"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>


    <main>
        <!-- Hero Section with Modern Design -->
        <section class="hero-modern" id="heroSection">
            <div class="hero-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
            
            <div class="hero-container">
                <div class="hero-content-modern">
                    <span class="hero-badge" data-aos="fade-up">
                        <i class="fas fa-star"></i> #1 Music Store 2026
                    </span>
                    <h1 class="hero-title-modern" data-aos="fade-up" data-aos-delay="100">
                        Unleash Your <span class="highlight">Musical</span> Genius
                    </h1>
                    <p class="hero-description" data-aos="fade-up" data-aos-delay="200">
                        Discover premium instruments, professional studio gear, and digital assets. 
                        Transform your passion into performance with world-class equipment.
                    </p>
                    
                    <div class="hero-features" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-pill">
                            <i class="fas fa-shipping-fast"></i>
                            <span>Free Shipping</span>
                        </div>
                        <div class="feature-pill">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Payment</span>
                        </div>
                        <div class="feature-pill">
                            <i class="fas fa-headset"></i>
                            <span>24/7 Support</span>
                        </div>
                    </div>
                    
                    <div class="hero-actions" data-aos="fade-up" data-aos-delay="400">
                        <a class="btn-hero btn-hero-primary" href="<?php echo $base; ?>/products_store.php">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Start Shopping</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                        <?php if (!is_logged_in()): ?>
                            <a class="btn-hero btn-hero-secondary" href="<?php echo $base; ?>/signup.php">
                                <i class="fas fa-user-plus"></i>
                                <span>Join Now</span>
                            </a>
                        <?php else: ?>
                            <a class="btn-hero btn-hero-secondary" href="<?php echo $base; ?>/customer/dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="hero-stats" data-aos="fade-up" data-aos-delay="500">
                        <div class="stat-box">
                            <div class="stat-icon"><i class="fas fa-box"></i></div>
                            <div class="stat-info">
                                <h3 class="stat-number">1000+</h3>
                                <p class="stat-label">Premium Products</p>
                            </div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-icon"><i class="fas fa-users"></i></div>
                            <div class="stat-info">
                                <h3 class="stat-number">5000+</h3>
                                <p class="stat-label">Happy Musicians</p>
                            </div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-icon"><i class="fas fa-star"></i></div>
                            <div class="stat-info">
                                <h3 class="stat-number">4.9/5</h3>
                                <p class="stat-label">Customer Rating</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="hero-visual-modern" data-aos="fade-left" data-aos-delay="300">
                    <div class="visual-card card-1">
                        <i class="fas fa-guitar"></i>
                        <span>Guitars</span>
                    </div>
                    <div class="visual-card card-2">
                        <i class="fas fa-drum"></i>
                        <span>Drums</span>
                    </div>
                    <div class="visual-card card-3">
                        <i class="fas fa-piano"></i>
                        <span>Keyboards</span>
                    </div>
                    <div class="visual-circle"></div>
                </div>
            </div>
            
            <div class="scroll-indicator">
                <div class="mouse-scroll">
                    <div class="mouse-wheel"></div>
                </div>
                <p>Scroll to explore</p>
            </div>
        </section>


        <!-- Why Choose Us Section -->
        <section class="benefits-section" id="benefitsSection">
            <div class="container-wide">
                <div class="section-header-modern" data-aos="fade-up">
                    <span class="section-badge">Why Choose Us</span>
                    <h2 class="section-title-modern">Experience the <span class="highlight">Melody Masters</span> Difference</h2>
                    <p class="section-subtitle-modern">Premium quality, exceptional service, unbeatable value</p>
                </div>
                
                <div class="benefits-grid">
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="100">
                        <div class="benefit-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3>Premium Quality</h3>
                        <p>Handpicked instruments from world-renowned brands, ensuring top-tier craftsmanship</p>
                        <div class="benefit-hover-effect"></div>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="200">
                        <div class="benefit-icon">
                            <i class="fas fa-truck-fast"></i>
                        </div>
                        <h3>Lightning Fast Shipping</h3>
                        <p>Free worldwide delivery on orders over $100 with express options available</p>
                        <div class="benefit-hover-effect"></div>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="300">
                        <div class="benefit-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3>Secure Transactions</h3>
                        <p>256-bit SSL encryption and PCI compliance for complete payment security</p>
                        <div class="benefit-hover-effect"></div>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="400">
                        <div class="benefit-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3>Best Price Guarantee</h3>
                        <p>Competitive pricing with exclusive deals and price-match guarantee</p>
                        <div class="benefit-hover-effect"></div>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="500">
                        <div class="benefit-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Expert Reviews</h3>
                        <p>Detailed product insights from professional musicians and sound engineers</p>
                        <div class="benefit-hover-effect"></div>
                    </div>
                    
                    <div class="benefit-card" data-aos="zoom-in" data-aos-delay="600">
                        <div class="benefit-icon">
                            <i class="fas fa-headphones-alt"></i>
                        </div>
                        <h3>24/7 Expert Support</h3>
                        <p>Round-the-clock assistance to help you find the perfect musical gear</p>
                        <div class="benefit-hover-effect"></div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Categories Showcase Section -->
        <section class="categories-modern" id="categoriesSection">
            <div class="container-wide">
                <div class="section-header-modern" data-aos="fade-up">
                    <span class="section-badge">Explore</span>
                    <h2 class="section-title-modern">Shop by <span class="highlight">Category</span></h2>
                    <p class="section-subtitle-modern">Find the perfect instrument for your musical journey</p>
                </div>
                
                <div class="categories-grid-modern">
                    <div class="category-modern" data-aos="flip-left" data-aos-delay="100">
                        <div class="category-overlay"></div>
                        <div class="category-icon-modern">
                            <i class="fas fa-guitar"></i>
                        </div>
                        <div class="category-content-modern">
                            <h3>Guitars</h3>
                            <p>Acoustic & Electric</p>
                            <span class="category-count">250+ Products</span>
                        </div>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link-modern">
                            <span>Explore</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="category-modern" data-aos="flip-left" data-aos-delay="200">
                        <div class="category-overlay"></div>
                        <div class="category-icon-modern">
                            <i class="fas fa-piano"></i>
                        </div>
                        <div class="category-content-modern">
                            <h3>Keyboards</h3>
                            <p>Pianos & Synthesizers</p>
                            <span class="category-count">180+ Products</span>
                        </div>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link-modern">
                            <span>Explore</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="category-modern" data-aos="flip-left" data-aos-delay="300">
                        <div class="category-overlay"></div>
                        <div class="category-icon-modern">
                            <i class="fas fa-drum"></i>
                        </div>
                        <div class="category-content-modern">
                            <h3>Drums</h3>
                            <p>Kits & Percussion</p>
                            <span class="category-count">120+ Products</span>
                        </div>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link-modern">
                            <span>Explore</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <div class="category-modern" data-aos="flip-left" data-aos-delay="400">
                        <div class="category-overlay"></div>
                        <div class="category-icon-modern">
                            <i class="fas fa-microphone"></i>
                        </div>
                        <div class="category-content-modern">
                            <h3>Microphones</h3>
                            <p>Studio & Live Performance</p>
                            <span class="category-count">90+ Products</span>
                        </div>
                        <a href="<?php echo $base; ?>/products_store.php" class="category-link-modern">
                            <span>Explore</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>


        <!-- Featured Products Section -->
        <section class="products-modern" id="productsSection">
            <div class="container-wide">
                <div class="section-header-modern" data-aos="fade-up">
                    <span class="section-badge">Featured Collection</span>
                    <h2 class="section-title-modern">Handpicked <span class="highlight">Premium</span> Products</h2>
                    <p class="section-subtitle-modern">Curated instruments for excellence and performance</p>
                </div>
                
                <div class="products-grid-modern">
                    <?php if (empty($featured)): ?>
                        <div class="empty-state-modern">
                            <i class="fas fa-music"></i>
                            <h3>No products available yet</h3>
                            <p>Check back soon for amazing deals!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($featured as $index => $product): ?>
                            <div class="product-modern" data-aos="fade-up" data-aos-delay="<?php echo ($index % 4) * 100; ?>">
                                <div class="product-badges-modern">
                                    <?php if ($product['product_type'] === 'digital'): ?>
                                        <span class="badge-modern badge-digital">
                                            <i class="fas fa-download"></i> Digital
                                        </span>
                                    <?php elseif ((int)$product['stock'] > 0): ?>
                                        <span class="badge-modern badge-stock">
                                            <i class="fas fa-check-circle"></i> In Stock
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-modern badge-out">
                                            <i class="fas fa-times-circle"></i> Sold Out
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-image-modern">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" 
                                             alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <?php else: ?>
                                        <div class="product-placeholder-modern">
                                            <i class="fas fa-music"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-overlay-modern">
                                        <a href="<?php echo $base; ?>/product.php?id=<?php echo $product['product_id']; ?>" class="btn-quick-view">
                                            <i class="fas fa-eye"></i>
                                            <span>Quick View</span>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="product-info-modern">
                                    <div class="product-category-modern">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </div>
                                    
                                    <h3 class="product-title-modern">
                                        <a href="<?php echo $base; ?>/product.php?id=<?php echo $product['product_id']; ?>">
                                            <?php echo htmlspecialchars($product['product_name']); ?>
                                        </a>
                                    </h3>
                                    
                                    <?php if (!empty($product['brand'])): ?>
                                        <p class="product-brand-modern">
                                            <i class="fas fa-copyright"></i>
                                            <?php echo htmlspecialchars($product['brand']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="product-rating-modern">
                                        <div class="stars-modern">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <span class="rating-count-modern">(24 reviews)</span>
                                    </div>
                                    
                                    <div class="product-bottom-modern">
                                        <div class="product-price-modern">
                                            <span class="price-label">Price</span>
                                            <span class="price-amount">${<?php echo number_format($product['price'], 2); ?></span>
                                        </div>
                                        
                                        <a class="btn-add-modern" href="<?php echo $base; ?>/product.php?id=<?php echo $product['product_id']; ?>">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>View</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="section-cta" data-aos="fade-up">
                    <a href="<?php echo $base; ?>/products_store.php" class="btn-view-all">
                        <span>View All Products</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>


        <!-- Testimonials Section -->
        <section class="testimonials-section" id="testimonialsSection">
            <div class="container-wide">
                <div class="section-header-modern" data-aos="fade-up">
                    <span class="section-badge">Testimonials</span>
                    <h2 class="section-title-modern">What Our <span class="highlight">Musicians</span> Say</h2>
                    <p class="section-subtitle-modern">Real feedback from real artists</p>
                </div>
                
                <div class="testimonials-grid">
                    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="100">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Melody Masters has the best selection of professional guitars. The quality is outstanding and the customer service is exceptional!"</p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>Sarah Johnson</h4>
                                <p>Professional Guitarist</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="200">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"Fast shipping, great prices, and authentic products. I've been buying all my studio equipment here for years. Highly recommended!"</p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>Michael Chen</h4>
                                <p>Music Producer</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card" data-aos="fade-up" data-aos-delay="300">
                        <div class="testimonial-stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="testimonial-text">"The variety of instruments and the expert advice I received made my purchasing decision so easy. Love my new keyboard!"</p>
                        <div class="testimonial-author">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4>Emma Williams</h4>
                                <p>Pianist</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="cta-modern" id="ctaSection">
            <div class="cta-shapes">
                <div class="cta-shape cta-shape-1"></div>
                <div class="cta-shape cta-shape-2"></div>
            </div>
            <div class="container-wide">
                <div class="cta-content-modern" data-aos="zoom-in">
                    <div class="cta-icon-modern">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h2>Ready to Start Your Musical Journey?</h2>
                    <p>Join thousands of musicians who trust Melody Masters for premium instruments and exceptional service</p>
                    <div class="cta-buttons-modern">
                        <a href="<?php echo $base; ?>/products_store.php" class="btn-cta btn-cta-primary">
                            <i class="fas fa-shopping-bag"></i>
                            <span>Browse Products</span>
                        </a>
                        <?php if (!is_logged_in()): ?>
                            <a href="<?php echo $base; ?>/signup.php" class="btn-cta btn-cta-secondary">
                                <i class="fas fa-user-plus"></i>
                                <span>Create Account</span>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="cta-trust-badges">
                        <div class="trust-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>Secure Shopping</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-undo"></i>
                            <span>30-Day Returns</span>
                        </div>
                        <div class="trust-badge">
                            <i class="fas fa-certificate"></i>
                            <span>Authenticity Guaranteed</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="newsletter-modern" id="newsletterSection">
            <div class="container-wide">
                <div class="newsletter-container" data-aos="fade-up">
                    <div class="newsletter-left">
                        <div class="newsletter-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="newsletter-text">
                            <h3>Stay in the Loop</h3>
                            <p>Subscribe to get exclusive deals, product updates, and music tips delivered to your inbox</p>
                        </div>
                    </div>
                    <div class="newsletter-right">
                        <form class="newsletter-form-modern" id="newsletterForm">
                            <div class="form-group-modern">
                                <i class="fas fa-envelope form-icon"></i>
                                <input type="email" placeholder="Enter your email address" required>
                                <button type="submit" class="btn-subscribe-modern">
                                    <span>Subscribe</span>
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <p class="newsletter-privacy">
                                <i class="fas fa-lock"></i>
                                We respect your privacy. Unsubscribe anytime.
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>


    <!-- Modern Footer -->
    <footer class="footer-modern">
        <div class="footer-top">
            <div class="container-wide">
                <div class="footer-grid-modern">
                    <div class="footer-column">
                        <div class="footer-brand">
                            <i class="fas fa-music"></i>
                            <h3>Melody Masters</h3>
                        </div>
                        <p class="footer-description">Your premier destination for professional musical instruments and audio equipment. Quality, service, and passion for music.</p>
                        <div class="footer-social">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                    
                    <div class="footer-column">
                        <h4 class="footer-heading">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo $base; ?>/index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                            <li><a href="<?php echo $base; ?>/products_store.php"><i class="fas fa-chevron-right"></i> Shop</a></li>
                            <li><a href="<?php echo $base; ?>/products.php"><i class="fas fa-chevron-right"></i> Browse Products</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right"></i> Contact</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4 class="footer-heading">Customer Service</h4>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-chevron-right"></i> Help Center</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right"></i> Shipping Information</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right"></i> Returns & Exchanges</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right"></i> Warranty Policy</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right"></i> Track Order</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-column">
                        <h4 class="footer-heading">Contact Info</h4>
                        <ul class="footer-contact">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>123 Music Street<br>Harmony City, MC 12345</span>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <span>+1 (555) 123-4567</span>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <span>info@melodymasters.com</span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Mon - Fri: 9AM - 6PM</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container-wide">
                <div class="footer-bottom-content">
                    <p class="copyright">
                        <i class="fas fa-copyright"></i>
                        2026 Melody Masters. All rights reserved.
                    </p>
                    <div class="footer-payment">
                        <span>We Accept:</span>
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-amex"></i>
                        <i class="fab fa-cc-paypal"></i>
                    </div>
                    <div class="footer-links-bottom">
                        <a href="#">Privacy Policy</a>
                        <span>•</span>
                        <a href="#">Terms of Service</a>
                        <span>•</span>
                        <a href="#">Cookie Policy</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- JavaScript -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS (Animate On Scroll)
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });
        
        // Mobile Menu Toggle
        const mobileToggle = document.getElementById('mobileToggle');
        const navMenu = document.getElementById('navMenu');
        
        mobileToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            mobileToggle.classList.toggle('active');
        });
        
        // Header Scroll Effect
        const header = document.getElementById('mainHeader');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
        
        // Back to Top Button
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        });
        
        backToTop.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Newsletter Form
        const newsletterForm = document.getElementById('newsletterForm');
        if (newsletterForm) {
            newsletterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('Thank you for subscribing! You\'ll receive exclusive deals soon.');
                newsletterForm.reset();
            });
        }
        
        // Smooth Scroll for Anchor Links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
