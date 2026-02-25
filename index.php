<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Check if connection was successful
if (!$conn) {
    $page_title = 'System Error - Melody Masters';
    include 'includes/header.php';
    echo '<div class="container" style="padding: 50px 20px; text-align: center;">';
    echo '<div class="alert alert-danger shadow-sm" style="background: #fff5f5; border: 1px solid #feb2b2; color: #9b2c2c; padding: 20px; border-radius: 8px;">';
    echo '<i class="fas fa-exclamation-triangle" style="font-size: 3rem; margin-bottom: 20px; color: #fc8181;"></i>';
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
    echo '<div class="alert alert-warning" style="background: #fffaf0; border: 1px solid #fbd38d; color: #9c4221; padding: 20px; border-radius: 8px;">';
    echo '<i class="fas fa-database" style="font-size: 3rem; margin-bottom: 20px; color: #ed8936;"></i>';
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

<!-- Hero Section -->
<section class="hero-full" style="background-image: url('https://images.unsplash.com/photo-1511379938547-c1f69419868d?q=80&w=2070&auto=format&fit=crop');">
    <div class="container">
        <div class="hero-content animate-fade-in">
            <h1 class="text-gold">Crafting the <br>Future of Sound</h1>
            <p>Experience the magic of world-class instruments. From hand-picked acoustic classics to cutting-edge digital workstations.</p>
            <div class="hero-actions">
                <a href="shop.php" class="btn btn-primary btn-lg pulse-btn">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
                <a href="#categories" class="btn btn-secondary btn-lg">
                    Explore Categories
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Category Section -->
<section id="categories" class="bg-gradient-dark" style="padding: 10rem 0 5rem;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 6rem;">
            <h2 style="font-size: 3rem; font-weight: 800; margin-bottom: 1rem;">Shop by <span class="text-gold">Category</span></h2>
            <div class="section-divider"></div>
        </div>
        
        <div class="grid grid-cols-4">
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
                elseif (strpos($cat_name, 'sheet') !== false) $icon = 'fa-file-lines';
            ?>
                <a href="shop.php?category=<?php echo $category['category_id']; ?>" class="glass-card card-shimmer text-center" style="padding: 4rem 2rem;">
                    <i class="fas <?php echo $icon; ?> category-icon-large"></i>
                    <h4 style="margin-top: 1.5rem; letter-spacing: 1px;"><?php echo htmlspecialchars($category['category_name']); ?></h4>
                    <p style="color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; font-weight: 600;">View Collection</p>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Instruments Section -->
<section class="featured-section" style="padding: 5rem 0 10rem;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 6rem;">
            <div>
                <h2 style="font-size: 3rem; font-weight: 800; margin: 0;">Featured <span class="text-gold">Instruments</span></h2>
                <p style="color: var(--text-muted); margin-top: 1rem; font-size: 1.1rem;">Precision-tuned excellence for the professional musician.</p>
            </div>
            <a href="shop.php" class="btn btn-secondary">View All Catalog</a>
        </div>

        <div class="grid grid-cols-4">
            <?php 
            if ($featured_products && $featured_products->num_rows > 0):
                $featured_products->data_seek(0);
                while ($product = $featured_products->fetch_assoc()): 
            ?>
                <div class="product-card-premium card-shimmer">
                    <a href="product.php?id=<?php echo $product['product_id']; ?>" class="image-wrap hover-zoom">
                        <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                             onerror="this.src='assets/images/placeholder.jpg';">
                    </a>
                    <div class="product-info" style="flex: 1; display: flex; flex-direction: column;">
                        <span style="color: var(--primary); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">
                            <?php echo htmlspecialchars($product['category_name']); ?>
                        </span>
                        <h4 style="font-size: 1.15rem; margin-bottom: 0.25rem;">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a>
                        </h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;"><?php echo htmlspecialchars($product['brand']); ?></p>
                        
                        <div class="item-footer" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                            <span class="price"><?php echo formatPrice($product['price']); ?></span>
                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm">Inspect</a>
                        </div>
                    </div>
                </div>
            <?php 
                endwhile; 
            else:
            ?>
                <p class="text-center" style="grid-column: 1 / -1;">No products found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Features / Trust Section -->
<section class="trust-section" style="padding: 8rem 0; background: rgba(255, 255, 255, 0.02); border-top: 1px solid var(--border-color);">
    <div class="container">
        <div class="grid grid-cols-4">
            <div class="text-center" style="display: flex; flex-direction: column; align-items: center;">
                <i class="fas fa-truck" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h4 style="font-size: 1.1rem; letter-spacing: 1px;">Global Logistics</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Insured worldwide delivery</p>
            </div>
            <div class="text-center" style="display: flex; flex-direction: column; align-items: center;">
                <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h4 style="font-size: 1.1rem; letter-spacing: 1px;">Secure Access</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Encrypted transactions</p>
            </div>
            <div class="text-center" style="display: flex; flex-direction: column; align-items: center;">
                <i class="fas fa-undo" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h4 style="font-size: 1.1rem; letter-spacing: 1px;">Satisfaction</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">30-day trial period</p>
            </div>
            <div class="text-center" style="display: flex; flex-direction: column; align-items: center;">
                <i class="fas fa-headset" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 1.5rem;"></i>
                <h4 style="font-size: 1.1rem; letter-spacing: 1px;">Artisan Support</h4>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Expert musician assistance</p>
            </div>
        </div>
    </div>
</section>


<?php include 'includes/footer.php'; ?>

