<?php
include 'includes/db_connection.php';
include 'includes/session.php';
include 'includes/product_manager.php';
include 'includes/order_manager.php';

$pm = new ProductManager($db);
$om = new OrderManager($db);
$message = '';

if (!isset($_GET['id'])) {
    header("Location: /products.php");
    exit();
}

$product_id = (int) $_GET['id'];
$product = $pm->get_product($product_id);

if (!$product) {
    header("Location: /products.php");
    exit();
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    require_customer();
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $result = $om->add_to_cart($product['product_id'], $quantity);
    $message = $result['message'];
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
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="/products.php">Products</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('Admin')): ?>
                        <li><a href="/admin/dashboard.php">Admin</a></li>
                    <?php elseif (has_role('Staff')): ?>
                        <li><a href="/staff/dashboard.php">Staff</a></li>
                    <?php else: ?>
                        <li><a href="/customer/dashboard.php">My Account</a></li>
                        <li><a href="/cart.php">🛒 Cart</a></li>
                    <?php endif; ?>
                    <li><a href="/public/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo (strpos($message, 'added') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <!-- Product Image -->
            <div>
                <div class="product-image" style="height: 400px;">
                    <?php if (!empty($product['image'])): ?>
                        <img src="/Music-Instrument-Shop-Online-Store/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                    <?php else: ?>
                        <span>No Image Available</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Details -->
            <div>
                <h1><?php echo htmlspecialchars($product['product_name']); ?></h1>
                <p class="text-muted"><?php echo htmlspecialchars($product['category_name']); ?></p>
                
                <div style="margin: 1.5rem 0;">
                    <div class="product-rating" style="margin-bottom: 1rem;">
                        <div class="stars" style="font-size: 1.5rem;">
                            <?php 
                            $rating_val = round($rating['avg_rating']);
                            for ($i = 0; $i < 5; $i++): 
                                echo ($i < $rating_val) ? '⭐' : '☆';
                            endfor;
                            ?>
                        </div>
                        <p class="rating-count"><?php echo $rating['total_reviews']; ?> customer reviews</p>
                    </div>
                </div>

                <div class="product-price" style="font-size: 2rem; margin: 1rem 0;">£<?php echo number_format($product['price'], 2); ?></div>

                <!-- Stock Status -->
                <?php if ($product['product_type'] === 'physical'): ?>
                    <p class="product-stock">
                        <?php if ($product['stock'] > 0): ?>
                            <span class="stock-available">✓ <?php echo $product['stock']; ?> in stock</span>
                        <?php else: ?>
                            <span class="stock-unavailable">Out of Stock</span>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <p class="product-stock"><span class="stock-available">✓ Digital Product - Instant Download</span></p>
                <?php endif; ?>

                <!-- Add to Cart Form -->
                <?php if (is_logged_in() && has_role('customer')): ?>
                    <form method="POST" action="" style="margin: 2rem 0;">
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo ($product['product_type'] === 'physical') ? $product['stock'] : 999; ?>" required>
                        </div>
                        <button type="submit" name="add_to_cart" class="btn btn-primary" style="width: 100%; padding: 1rem;">
                            🛒 Add to Cart
                        </button>
                    </form>
                <?php elseif (!is_logged_in()): ?>
                    <p style="margin: 2rem 0;">
                        <a href="/login.php" class="btn btn-primary">Login to Purchase</a>
                    </p>
                <?php endif; ?>

                <!-- Description -->
                <div class="card">
                    <h3>Product Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="card">
            <h2>Customer Reviews</h2>
            
            <?php if (empty($reviews)): ?>
                <p>No reviews yet. Be the first to review this product!</p>
            <?php else: ?>
                <?php foreach ($reviews as $review): ?>
                    <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <h4><?php echo htmlspecialchars($review['user_name']); ?></h4>
                                <div class="stars">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <?php echo ($i < $review['rating']) ? '⭐' : '☆'; ?>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($review['review_date'])); ?></small>
                        </div>
                        <p style="margin-top: 1rem;"><?php echo htmlspecialchars($review['comment']); ?></p>
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
