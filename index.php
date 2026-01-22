<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

$base = defined('BASE_PATH') ? BASE_PATH : '/Music-Instrument-Shop-Online-Store';
$pm = new ProductManager($db);
$products = $pm->get_products();
$featured = array_slice($products, 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters | Music Store</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/products_store.php">🛒 Shop</a></li>
                <li><a href="<?php echo $base; ?>/products.php">Browse</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('admin')): ?>
                        <li><a href="<?php echo $base; ?>/admin/dashboard.php">Admin</a></li>
                    <?php elseif (has_role('staff')): ?>
                        <li><a href="<?php echo $base; ?>/staff/dashboard.php">Staff</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo $base; ?>/customer/dashboard.php">My Account</a></li>
                        <li><a href="<?php echo $base; ?>/cart.php">Cart<?php if (isset($_SESSION['cart'])) echo ' (' . count($_SESSION['cart']) . ')'; ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base; ?>/public/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base; ?>/login.php">Login</a></li>
                    <li><a href="<?php echo $base; ?>/signup.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container">
        <section class="hero">
            <div class="hero-content">
                <h1>Unleash Your Inner Artist</h1>
                <p>Discover world-class instruments, studio gear, and digital assets designed for professionals.</p>
                <div class="hero-buttons">
                    <a class="btn btn-primary btn-lg" href="<?php echo $base; ?>/products_store.php">Shop Now</a>
                    <?php if (!is_logged_in()): ?>
                        <a class="btn btn-outline btn-lg" href="<?php echo $base; ?>/signup.php">Join the Community</a>
                    <?php else: ?>
                        <a class="btn btn-outline btn-lg" href="<?php echo $base; ?>/customer/dashboard.php">My Dashboard</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="promo-section container">
            <div class="promo-content">
                <h2>Premium Guitars Collection</h2>
                <p>Experience the finest craftsmanship with our exclusive range of acoustic and electric guitars. Perfect for studio recordings and live performances.</p>
                <a href="<?php echo $base; ?>/products_store.php?category=guitars" class="btn btn-primary">Explore Guitars</a>
            </div>
            <div class="promo-image">
                <img src="<?php echo $base; ?>/assets/images/Guitar.jpg" alt="Premium Guitar">
            </div>
        </section>

        <section>
            <h2>Featured Products</h2>
            <div class="products-grid">
                <?php if (empty($featured)): ?>
                    <p>No products available yet.</p>
                <?php else: ?>
                    <?php foreach ($featured as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <?php if (!empty($product['image'])): ?>
                                    <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <?php else: ?>
                                    <span>No Image</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                <p class="product-stock">
                                    <?php if ($product['product_type'] === 'physical'): ?>
                                        <?php if ($product['stock'] > 0): ?>
                                            <span class="stock-available">In stock: <?php echo (int)$product['stock']; ?></span>
                                        <?php else: ?>
                                            <span class="stock-unavailable">Out of stock</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="stock-available">Digital download</span>
                                    <?php endif; ?>
                                </p>
                                <a class="btn btn-secondary btn-small" href="<?php echo $base; ?>/product.php?id=<?php echo $product['product_id']; ?>">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
        <p>Instruments, accessories, and digital music tools.</p>
    </footer>
</body>
</html>
