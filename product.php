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
</head>
<body>
    <header>
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/products.php">Products</a></li>
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
        <a href="<?php echo $base; ?>/products.php" style="color: #7F8C8D; text-decoration: none; margin-bottom: 1rem; display: inline-block;">← Back to Products</a>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; margin: 2rem 0;">
            <!-- Product Image -->
            <div>
                <div class="product-image" style="height: 400px; border: 1px solid #D6DDE3; border-radius: 8px; overflow: hidden;">
                    <?php if (!empty($product['image'])): ?>
                        <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #F7F9FA;">
                            <span style="color: #7F8C8D;">No Image Available</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Details -->
            <div>
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <?php if (!empty($product['brand'])): ?>
                    <p style="color: #7F8C8D; font-size: 1.1rem;">By <strong><?php echo htmlspecialchars($product['brand']); ?></strong></p>
                <?php endif; ?>
                <p style="color: #7F8C8D;">Category: <strong><?php echo htmlspecialchars($product['category_name']); ?></strong></p>

                <div style="margin: 1.5rem 0;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                        <span style="font-size: 1.2rem;">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <?php echo ($i < round($rating['avg_rating'])) ? '⭐' : '☆'; ?>
                            <?php endfor; ?>
                        </span>
                        <span style="color: #7F8C8D;"><?php echo (int)$rating['total_reviews']; ?> reviews</span>
                    </div>
                </div>

                <div style="font-size: 2rem; font-weight: 700; color: var(--accent); margin: 1.5rem 0;">
                    $<?php echo number_format($product['price'], 2); ?>
                </div>

                <!-- Stock Status -->
                <div style="margin: 1.5rem 0;">
                    <?php if ($product['product_type'] === 'physical'): ?>
                        <?php if ((int)$product['stock'] > 0): ?>
                            <p style="color: #1E8449; font-size: 1.05rem; font-weight: 600;">✓ In Stock (<?php echo (int)$product['stock']; ?> available)</p>
                        <?php else: ?>
                            <p style="color: #C0392B; font-size: 1.05rem; font-weight: 600;">✗ Out of Stock</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p style="color: #1E8449; font-size: 1.05rem; font-weight: 600;">✓ Digital Download - Instant Access</p>
                    <?php endif; ?>
                </div>

                <!-- Add to Cart -->
                <?php if (is_logged_in() && has_role('customer')): ?>
                    <form method="POST" action="<?php echo $base; ?>/add_to_cart.php" style="display: flex; gap: 1rem; margin: 2rem 0;">
                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                        <div>
                            <label for="quantity" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Quantity</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo ($product['product_type'] === 'physical') ? (int)$product['stock'] : 999; ?>" style="width: 80px; padding: 0.5rem; border: 1px solid #D6DDE3; border-radius: 4px;">
                        </div>
                        <button type="submit" class="btn btn-primary" style="align-self: flex-end; padding: 0.75rem 2rem;">🛒 Add to Cart</button>
                    </form>
                <?php elseif (!is_logged_in()): ?>
                    <p style="margin: 2rem 0;">
                        <a href="<?php echo $base; ?>/login.php" class="btn btn-primary">Login to Purchase</a>
                    </p>
                <?php endif; ?>

                <!-- Description -->
                <div class="card" style="margin-top: 2rem;">
                    <h3>Product Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="card" style="margin-top: 3rem;">
            <h2>Customer Reviews</h2>
            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to review!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div style="padding: 1.5rem; border-bottom: 1px solid #D6DDE3;">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <h4><?php echo htmlspecialchars($review['user_name']); ?></h4>
                                <div style="font-size: 1.1rem;">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php echo ($i < (int)$review['rating']) ? '⭐' : '☆'; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small style="color: #7F8C8D;"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></small>
                        </div>
                        <p style="margin-top: 0.75rem;"><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
