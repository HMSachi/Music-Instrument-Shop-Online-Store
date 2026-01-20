<?php
include 'includes/db_connection.php';
include 'includes/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters - Music Instrument Shop</title>
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="/products.php">Products</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('Admin')): ?>
                        <li><a href="/admin/dashboard.php">Admin Dashboard</a></li>
                    <?php elseif (has_role('Staff')): ?>
                        <li><a href="/staff/dashboard.php">Staff Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="/customer/dashboard.php">My Account</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo (has_role('Customer')) ? '/cart.php' : '#'; ?>">
                        <span class="cart-icon">
                            🛒 Cart
                            <?php if (has_role('Customer') && isset($_SESSION['cart'])): ?>
                                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </span>
                    </a></li>
                    <li><a href="/public/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/login.php">Login</a></li>
                    <li><a href="/signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container">
        <div class="hero">
            <h1>Welcome to Melody Masters</h1>
            <p>Discover the finest musical instruments from around the world</p>
            <?php if (!is_logged_in()): ?>
                <a href="/signup.php" class="btn btn-primary">Get Started</a>
            <?php endif; ?>
        </div>

        <!-- Featured Products -->
        <section>
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php
                include 'includes/product_manager.php';
                $pm = new ProductManager($db);
                $products = $pm->get_products();
                $featured = array_slice($products, 0, 6);
                
                foreach ($featured as $product):
                    $rating = $pm->get_average_rating($product['product_id']);
                ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if (!empty($product['image'])): ?>
                                <img src="/Music-Instrument-Shop-Online-Store/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                            <div class="product-price">£<?php echo number_format($product['price'], 2); ?></div>
                            
                            <?php if ($product['product_type'] === 'physical'): ?>
                                <p class="product-stock">
                                    <?php if ($product['stock'] > 0): ?>
                                        <span class="stock-available">✓ <?php echo $product['stock']; ?> in stock</span>
                                    <?php else: ?>
                                        <span class="stock-unavailable">Out of Stock</span>
                                    <?php endif; ?>
                                </p>
                            <?php else: ?>
                                <p class="product-stock"><span class="stock-available">✓ Digital - Available</span></p>
                            <?php endif; ?>
                            
                            <div class="product-rating">
                                <div class="stars">
                                    <?php 
                                    $rating_val = round($rating['avg_rating']);
                                    for ($i = 0; $i < 5; $i++): 
                                        echo ($i < $rating_val) ? '⭐' : '☆';
                                    endfor;
                                    ?>
                                </div>
                                <p class="rating-count"><?php echo $rating['total_reviews']; ?> reviews</p>
                            </div>
                            
                            <a href="/product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-secondary btn-small">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div style="text-align: center; margin: 3rem 0;">
            <a href="/products.php" class="btn btn-primary">View All Products</a>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 Melody Masters Instrument Shop. All rights reserved.</p>
        <p>Quality instruments for musicians at all levels</p>
    </footer>
</body>
</html>
