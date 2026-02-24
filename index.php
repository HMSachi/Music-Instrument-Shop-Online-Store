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
    <div class="container">
        <div class="hero-content">
            <h1>Experience the Magic of Music</h1>
            <p>Discover a world-class collection of premium instruments tailored for Every artist, from beginner to professional.</p>
            <div class="hero-btns">
                <a href="shop.php" class="btn btn-primary btn-large">
                    <i class="fas fa-shopping-bag"></i> Explore Shop
                </a>
                <a href="#categories" class="btn btn-secondary btn-large">
                    View Categories
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
                <a href="shop.php?category=<?php echo $category['category_id']; ?>" class="category-card">
                    <i class="fas <?php echo $icon; ?>"></i>
                    <h3><?php echo htmlspecialchars($category['category_name']); ?></h3>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<section class="promo-banner">
    <div class="container">
        <div class="promo-content">
            <h2>Special Offer: 20% Off All Accessories!</h2>
            <p>Upgrade your setup with our premium collection of strings, picks, and cases. Limited time only.</p>
            <a href="shop.php?category=6" class="btn btn-secondary btn-large">Claim Offer</a>
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
            <div class="feature-card">
                <i class="fas fa-shipping-fast"></i>
                <h3>Free Shipping</h3>
                <p>On orders over ₱5,000</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Store</h3>
                <p>100% safe transactions</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-award"></i>
                <h3>Premium Quality</h3>
                <p>Handpicked instruments</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-undo"></i>
                <h3>30-Day Returns</h3>
                <p>Money back guarantee</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

