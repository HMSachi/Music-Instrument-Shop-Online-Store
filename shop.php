<?php
require_once 'config/config.php';
require_once 'config/database.php';

$message = '';

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('error', 'Security validation failed.');
        header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
        exit();
    }

    $product_id = (int)$_POST['product_id'];
    $quantity = 1; 
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    
    setFlashMessage('success', 'Product added to cart!');
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING']);
    exit();
}

// Get filters and sanitize
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$brand = isset($_GET['brand']) ? sanitizeInput($_GET['brand']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (float)$_GET['min_price'] : null;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;

// Build dynamic query with prepared statements
$where = [];
$params = [];
$types = "";

if ($category_id > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}
if (!empty($search)) {
    $search_param = "%$search%";
    $where[] = "(p.product_name LIKE ? OR p.description LIKE ? OR p.brand LIKE ?)";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}
if (!empty($brand)) {
    $where[] = "p.brand = ?";
    $params[] = $brand;
    $types .= "s";
}
if ($min_price !== null) {
    $where[] = "p.price >= ?";
    $params[] = $min_price;
    $types .= "d";
}
if ($max_price !== null) {
    $where[] = "p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$products_sql = "SELECT p.*, c.category_name FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 $where_clause
                 ORDER BY p.created_at DESC";

$products = preparedQuery($conn, $products_sql, $params, $types);

// Get all categories for filter
$categories_sql = "SELECT * FROM categories WHERE parent_id IS NULL";
$categories = preparedQuery($conn, $categories_sql);

// Get all unique brands for filter
$brands_sql = "SELECT DISTINCT brand FROM products WHERE brand != '' ORDER BY brand ASC";
$brands_result = preparedQuery($conn, $brands_sql);

$page_title = 'Shop - Melody Masters';
include 'includes/header.php';
?>

<section class="shop-section">
    <div class="container">
        <div class="shop-header">
            <h1><i class="fas fa-store"></i> Our Products</h1>
        </div>
        

        <div class="shop-layout animate-fade-in-up">
            <!-- Sidebar -->
            <aside class="shop-sidebar">
                <div class="sidebar-section">
                    <h3>Search</h3>
                    <form method="GET" action="" class="search-form">
                        <input type="text" name="search" placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <div class="sidebar-section">
                    <h3>Price Range</h3>
                    <form method="GET" action="" class="price-filter-form">
                        <div class="price-inputs">
                            <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price; ?>">
                            <span>-</span>
                            <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price; ?>">
                        </div>
                        <?php if ($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                        <?php if ($brand): ?><input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand); ?>"><?php endif; ?>
                        <button type="submit" class="btn btn-sm btn-secondary btn-block mt-2">Apply</button>
                    </form>
                </div>
                
                <div class="sidebar-section">
                    <h3>Categories</h3>
                    <ul class="category-list">
                        <li>
                            <a href="shop.php" class="<?php echo $category_id === 0 ? 'active' : ''; ?>">
                                All Products
                            </a>
                        </li>
                        <?php while ($category = $categories->fetch_assoc()): ?>
                            <li>
                                <a href="shop.php?category=<?php echo $category['category_id']; ?>&brand=<?php echo urlencode($brand); ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" 
                                   class="<?php echo $category_id === (int)$category['category_id'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="sidebar-section">
                    <h3>Brands</h3>
                    <ul class="category-list">
                        <li>
                            <a href="shop.php?category=<?php echo $category_id; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" 
                               class="<?php echo empty($brand) ? 'active' : ''; ?>">
                                All Brands
                            </a>
                        </li>
                        <?php while ($b = $brands_result->fetch_assoc()): ?>
                            <li>
                                <a href="shop.php?brand=<?php echo urlencode($b['brand']); ?>&category=<?php echo $category_id; ?>&min_price=<?php echo $min_price; ?>&max_price=<?php echo $max_price; ?>" 
                                   class="<?php echo $brand === $b['brand'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($b['brand']); ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
                
                <a href="shop.php" class="btn btn-sm btn-outline btn-block">Clear All Filters</a>
            </aside>
            
            <!-- Products Grid -->
            <div class="shop-content">
                <?php if ($products && $products->num_rows > 0): ?>
                    <div class="products-count">
                        <p>Showing <?php echo $products->num_rows; ?> products</p>
                    </div>
                    
                    <div class="products-grid">
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <a href="product.php?id=<?php echo $product['product_id']; ?>">
                                        <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                             alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                             onerror="this.src='assets/images/placeholder.jpg';">
                                    </a>
                                    <?php if ($product['stock'] < 1): ?>
                                        <span class="badge badge-danger">Out of Stock</span>
                                    <?php elseif ($product['product_type'] === 'digital'): ?>
                                        <span class="badge badge-info">Digital</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <p class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                                    <h3><a href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a></h3>
                                    <p class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></p>
                                    <div class="product-footer">
                                        <span class="product-price"><?php echo formatPrice($product['price']); ?></span>
                                        <div class="product-actions">
                                            <?php if ($product['stock'] > 0): ?>
                                                <form method="POST" action="" style="display:inline;">
                                                    <?php echo csrfInput(); ?>
                                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                    <button type="submit" name="add_to_cart" class="btn btn-sm btn-primary" title="Add to Cart">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-secondary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <p>No products found matching your criteria</p>
                        <a href="shop.php" class="btn btn-primary mt-3">Reset Filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
