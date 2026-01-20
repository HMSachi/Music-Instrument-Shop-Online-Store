<?php
require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/product_manager.php';

$base = BASE_PATH;
$pm = new ProductManager($db);

$search = $_GET['search'] ?? '';
$category_id = $_GET['category'] ?? '';

$products = $pm->get_products(
    $category_id ? (int)$category_id : null,
    $search ? $search : null
);
$categories = $pm->get_categories();

// Group products by category for display
$products_by_category = [];
foreach ($products as $p) {
    $cat = $p['category_name'] ?? 'Uncategorized';
    $cat_id = $p['category_id'] ?? 0;
    if (!isset($products_by_category[$cat_id])) {
        $products_by_category[$cat_id] = ['name' => $cat, 'products' => []];
    }
    $products_by_category[$cat_id]['products'][] = $p;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Melody Masters</title>
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
        <h1>Browse Products</h1>
        
        <!-- Search & Filter -->
        <div class="card" style="margin-bottom: 2rem;">
            <form method="GET" action="" style="display: grid; grid-template-columns: 1fr 1fr 120px; gap: 1rem; align-items: end;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" placeholder="Product name..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="category">Category</label>
                    <select id="category" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category_id == $cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>

        <!-- Products by Category -->
        <?php if (empty($products)): ?>
            <div class="card">
                <p>No products found. Try adjusting your filters.</p>
            </div>
        <?php else: ?>
            <?php foreach ($products_by_category as $cat_id => $category_data): ?>
                <div style="margin-bottom: 3rem;">
                    <h2><?php echo htmlspecialchars($category_data['name']); ?></h2>
                    <div class="products-grid">
                        <?php foreach ($category_data['products'] as $product): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo $base; ?>/assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                    <?php else: ?>
                                        <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #F7F9FA;">
                                            <span style="color: #7F8C8D;">No Image</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                    <?php if (!empty($product['brand'])): ?>
                                        <p style="color: #7F8C8D; font-size: 0.9rem;">By <?php echo htmlspecialchars($product['brand']); ?></p>
                                    <?php endif; ?>
                                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                    <p class="product-stock">
                                        <?php if ($product['product_type'] === 'physical'): ?>
                                            <?php if ((int)$product['stock'] > 0): ?>
                                                <span class="stock-available">✓ In Stock (<?php echo (int)$product['stock']; ?>)</span>
                                            <?php else: ?>
                                                <span class="stock-unavailable">Out of Stock</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="stock-available">✓ Digital Download</span>
                                        <?php endif; ?>
                                    </p>
                                    <a class="btn btn-secondary btn-small" href="<?php echo $base; ?>/product.php?id=<?php echo $product['product_id']; ?>">View Details</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
        <p>Your source for quality music instruments and accessories.</p>
    </footer>
</body>
</html>
