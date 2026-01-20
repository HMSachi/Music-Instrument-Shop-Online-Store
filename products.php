<?php
include 'includes/db_connection.php';
include 'includes/session.php';
include 'includes/product_manager.php';

$pm = new ProductManager($db);
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

$products = $pm->get_products(
    $category ? (int)$category : null,
    $search ? $search : null
);
$categories = $pm->get_categories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Melody Masters</title>
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="/products.php">Products</a></li>
                <?php if (is_logged_in()): ?>
                    <?php if (has_role('admin')): ?>
                        <li><a href="/admin/dashboard.php">Admin</a></li>
                    <?php elseif (has_role('staff')): ?>
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
        <h1>Browse Products</h1>
        
        <!-- Search and Filter -->
        <div class="card mb-3">
            <form method="GET" action="" style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                <div>
                    <label>Search Products</label>
                    <input type="text" name="search" placeholder="Search by name or description..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div>
                    <label>Category</label>
                    <select name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['category_id']; ?>" <?php echo ($category == $cat['category_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="products-grid">
            <?php 
            if (empty($products)): ?>
                <div style="grid-column: 1/-1;">
                    <p>No products found. Try adjusting your search or filters.</p>
                </div>
            <?php 
            else:
                foreach ($products as $product):
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
            <?php 
                endforeach;
            endif;
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
