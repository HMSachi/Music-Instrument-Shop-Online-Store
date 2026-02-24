<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if connection was successful
if (!$conn) {
    $page_title = 'System Error - Melody Masters';
    include 'includes/header.php';
    echo '<div class="container" style="padding: 50px 20px; text-align: center;">';
    echo '<div class="alert alert-danger shadow-sm" style="background: #fee2e2; border: 1px solid #ef4444; color: #b91c1c; padding: 20px; border-radius: 8px;">';
    echo '<i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px; color: #ef4444;"></i>';
    echo '<h2 style="margin-bottom: 10px;">Database Connection Failed</h2>';
    echo '<p>We are having trouble connecting to the database. Please make sure XAMPP MySQL is running.</p>';
    echo '<a href="QUICK_START.md" class="btn btn-primary" style="margin-top: 15px;">Check Setup Guide</a>';
    echo '</div></div>';
    include 'includes/footer.php';
    exit;
}

// Fetch featured products
$featured_sql = "SELECT p.*, c.category_name FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 ORDER BY p.created_at DESC LIMIT 8";
$featured_products = preparedQuery($conn, $featured_sql);

// Fetch categories
$categories_sql = "SELECT * FROM categories WHERE parent_id IS NULL";
$categories = preparedQuery($conn, $categories_sql);

// If queries failed (e.g. missing tables)
if ($featured_products === false || $categories === false) {
    $page_title = 'Setup Required - Melody Masters';
    include 'includes/header.php';
    echo '<div class="container" style="padding: 50px 20px; text-align: center;">';
    echo '<div class="alert alert-warning" style="background: #fffbeb; border: 1px solid #f59e0b; color: #92400e; padding: 20px; border-radius: 8px;">';
    echo '<i class="fas fa-database" style="font-size: 3rem; margin-bottom: 20px; color: #f59e0b;"></i>';
    echo '<h2 style="margin-bottom: 10px;">Database Not Initialized</h2>';
    echo '<p>It looks like the database tables are missing. Please import <code>database/music_shop.sql</code> using phpMyAdmin.</p>';
    echo '<a href="QUICK_START.md" class="btn btn-warning" style="margin-top: 15px; color: white;">View Setup Instructions</a>';
    echo '</div></div>';
    include 'includes/footer.php';
    exit;
}

$page_title = 'Home - Melody Masters';
include 'includes/header.php';
?>

<section class="hero-section">
    <div class="hero-slider">
        <div class="hero-slide active" style="background-image: url('assets/images/main_img.jpg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/main_img1.jpg');"></div>
        <div class="hero-slide" style="background-image: url('assets/images/main_img2.jpg');"></div>
    </div>
    <div class="hero-overlay"></div>
    <div class="container">
        <div class="hero-content animate-fade-in-up">
            <span class="badge mb-3 animate-float" style="position: static; display: inline-block; margin-bottom: 1rem; background: rgba(255, 161, 255, 0.15); color: var(--primary-dark); border: 1px solid var(--primary-light);">Premium Collection 2026</span>
            <h1>Crafting the <br>Future of <span>Sound</span></h1>
            <p>Experience the magic of world-class instruments. From hand-picked acoustic classics to cutting-edge digital workstations.</p>
            <div class="hero-btns">
                <a href="shop.php" class="btn btn-primary btn-large animate-pulse-glow">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
                <a href="#categories" class="btn btn-secondary btn-large">
                    Explore Categories
                </a>
            </div>
        </div>
    </div>
</section>

<section id="categories" class="categories-section">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
            <?php 
            $categories->data_seek(0);
            while ($category = $categories->fetch_assoc()): 
                $icon = 'fa-music';
                $cat_name = strtolower($category['category_name']);
                if (strpos($cat_name, 'guitar') !== false) $icon = 'fa-guitar';
                elseif (strpos($cat_name, 'keyboard') !== false || strpos($cat_name, 'piano') !== false) $icon = 'fa-keyboard';
                elseif (strpos($cat_name, 'drum') !== false) $icon = 'fa-drum';
                elseif (strpos($cat_name, 'wind') !== false) $icon = 'fa-wind';
                elseif (strpos($cat_name, 'string') !== false) $icon = 'fa-violin';
                elseif (strpos($cat_name, 'accessor') !== false) $icon = 'fa-headphones';
                elseif (strpos($cat_name, 'digital') !== false || strpos($cat_name, 'sheet') !== false) $icon = 'fa-file-audio';
            ?>
                <a href="shop.php?category=<?php echo $category['category_id']; ?>" class="category-card glass-card animate-fade-in-up" style="text-decoration: none; transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);">
                    <div class="category-icon" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 2rem; background: hsla(var(--p-h), 83%, 53%, 0.1); width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin: 0 auto 1.5rem;">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--heading); margin: 0;"><?php echo htmlspecialchars($category['category_name']); ?></h3>
                    <p style="font-size: 0.85rem; color: var(--text-light); margin-top: 0.5rem;">Explore Collection</p>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="promo-banner">
    <div class="container">
        <div class="promo-card animate-fade-in-up">
            <div class="promo-text">
                <span class="badge badge-accent">Limited Time Only</span>
                <h2>Elevate Your Performance</h2>
                <p>Enjoy up to <strong>20% off</strong> on all premium accessories and studio gear. Precision-crafted for those who demand excellence.</p>
                <div class="promo-actions">
                    <a href="shop.php?category=6" class="btn btn-primary btn-large">Claim 20% Discount</a>
                </div>
            </div>
            <div class="promo-visual">
                <i class="fas fa-music animate-float"></i>
            </div>
        </div>
    </div>
</section>

<section class="featured-section">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="products-grid">
            <?php 
            if ($featured_products && $featured_products->num_rows > 0):
                $featured_products->data_seek(0);
                while ($product = $featured_products->fetch_assoc()): 
            ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg';">
                        <?php if ($product['stock'] < 1): ?>
                            <span class="badge badge-danger">Out of Stock</span>
                        <?php elseif ($product['product_type'] === 'digital'): ?>
                            <span class="badge badge-info">Digital</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                        <div class="product-footer">
                            <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-primary">
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile; 
            else:
            ?>
                <p class="no-products">No featured products found.</p>
            <?php endif; ?>
        </div>
        <div class="text-center">
            <a href="shop.php" class="btn btn-secondary">View Catalog</a>
        </div>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card glass-card interactive-card animate-fade-in-up" style="animation-delay: 0.1s; padding: 4rem 3rem;">
                <i class="fas fa-shipping-fast" style="font-size: 2.5rem; color: #FFA1FF; margin-bottom: 2rem; transition: all 0.3s ease; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(255, 161, 255, 0.05); border-radius: 50%;"></i>
                <h3 style="font-weight: 800; margin-bottom: 1rem;">Global Logistics</h3>
                <p style="color: var(--text-light); font-size: 1.05rem;">Free Shipping over £100</p>
            </div>
            <div class="feature-card glass-card interactive-card animate-fade-in-up" style="animation-delay: 0.2s; padding: 4rem 3rem;">
                <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #FFA1FF; margin-bottom: 2rem; transition: all 0.3s ease; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(255, 161, 255, 0.05); border-radius: 50%;"></i>
                <h3 style="font-weight: 800; margin-bottom: 1rem;">Secure Vault</h3>
                <p style="color: var(--text-light); font-size: 1.05rem;">End-to-End Encryption</p>
            </div>
            <div class="feature-card glass-card interactive-card animate-fade-in-up" style="animation-delay: 0.3s; padding: 4rem 3rem;">
                <i class="fas fa-award" style="font-size: 2.5rem; color: #FFA1FF; margin-bottom: 2rem; transition: all 0.3s ease; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(255, 161, 255, 0.05); border-radius: 50%;"></i>
                <h3 style="font-weight: 800; margin-bottom: 1rem;">Prime Quality</h3>
                <p style="color: var(--text-light); font-size: 1.05rem;">Luthier Tested</p>
            </div>
            <div class="feature-card glass-card interactive-card animate-fade-in-up" style="animation-delay: 0.4s; padding: 4rem 3rem;">
                <i class="fas fa-undo" style="font-size: 2.5rem; color: #FFA1FF; margin-bottom: 2rem; transition: all 0.3s ease; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; background: rgba(255, 161, 255, 0.05); border-radius: 50%;"></i>
                <h3 style="font-weight: 800; margin-bottom: 1rem;">Purity Seal</h3>
                <p style="color: var(--text-light); font-size: 1.05rem;">Lifetime Integrity</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

