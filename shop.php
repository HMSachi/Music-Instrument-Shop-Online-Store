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
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$limit = 9;
$offset = ($page - 1) * $limit;

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

// Count total items for pagination
$count_sql = "SELECT COUNT(*) as total FROM products p " . $where_clause;
$count_result = preparedQuery($conn, $count_sql, $params, $types);
$total_products = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Sorting logic
$order_by = "ORDER BY p.created_at DESC";
if ($sort === 'price_asc') {
    $order_by = "ORDER BY p.price ASC";
} elseif ($sort === 'price_desc') {
    $order_by = "ORDER BY p.price DESC";
} elseif ($sort === 'name_asc') {
    $order_by = "ORDER BY p.product_name ASC";
}

// Ensure params handle LIMIT appropriately by just concatenating because they are integers safely casted above
$products_sql = "SELECT p.*, c.category_name FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.category_id 
                 $where_clause
                 $order_by 
                 LIMIT $limit OFFSET $offset";

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
        <div class="shop-header" style="margin-bottom: 4rem; text-align: center;">
            <h1 class="text-gold">Our Instruments</h1>
            <p style="color: var(--text-muted);">Discover precision-crafted gear for every musician.</p>
        </div>

        <div class="shop-layout animate-fade-in">
            <!-- Sidebar -->
            <aside class="shop-sidebar">
                <div class="sidebar-section glass-card" style="padding: 1.5rem; margin-bottom: 2rem;">
                    <h3>Search</h3>
                    <form method="GET" action="" class="sidebar-search">
                        <div style="position: relative;">
                            <input type="text" name="search" class="form-control" placeholder="Instrument Nomenclature..." 
                                   value="<?php echo htmlspecialchars($search); ?>" style="padding-right: 3rem;">
                            <button type="submit" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--primary); cursor: pointer; font-size: 1.1rem;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="sidebar-section glass-card" style="padding: 1.5rem; margin-bottom: 2rem;">
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

                <div class="sidebar-section glass-card" style="padding: 1.5rem; margin-bottom: 2rem;">
                    <h3>Price Range</h3>
                    <form method="GET" action="" class="price-filter-form">
                        <div class="price-inputs" style="display: flex; gap: 0.5rem; margin-bottom: 1rem;">
                            <input type="number" name="min_price" class="form-control" placeholder="Min" value="<?php echo $min_price; ?>">
                            <input type="number" name="max_price" class="form-control" placeholder="Max" value="<?php echo $max_price; ?>">
                        </div>
                        <?php if ($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                        <?php if ($brand): ?><input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand); ?>"><?php endif; ?>
                        <button type="submit" class="btn btn-primary btn-block btn-sm">Filter</button>
                    </form>
                </div>

                <div class="sidebar-section glass-card" style="padding: 1.5rem; margin-bottom: 2rem;">
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
                
                <a href="shop.php" class="btn btn-secondary btn-block">Clear All Filters</a>
            </aside>
            
            <!-- Products Grid -->
            <div class="shop-content">
                <?php if ($products && $products->num_rows > 0): ?>
                    <div class="products-count" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; color: var(--text-muted);">
                        <p>Showing <?php echo $products->num_rows; ?> of <?php echo $total_products; ?> instruments</p>
                        
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <label for="sortSelector" style="font-size: 0.9rem;">Sort by</label>
                            <form method="GET" action="" style="margin: 0;">
                                <?php if ($category_id): ?><input type="hidden" name="category" value="<?php echo $category_id; ?>"><?php endif; ?>
                                <?php if ($search): ?><input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>"><?php endif; ?>
                                <?php if ($brand): ?><input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand); ?>"><?php endif; ?>
                                <?php if ($min_price): ?><input type="hidden" name="min_price" value="<?php echo $min_price; ?>"><?php endif; ?>
                                <?php if ($max_price): ?><input type="hidden" name="max_price" value="<?php echo $max_price; ?>"><?php endif; ?>
                                
                                <select name="sort" id="sortSelector"onchange="this.form.submit()" class="form-control" style="background: rgba(255,255,255,0.03); border-color: var(--border-color); color: var(--text-main); font-size: 0.9rem; padding: 0.4rem 2rem 0.4rem 1rem;">
                                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                                    <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                                    <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                                    <option value="name_asc" <?php echo $sort === 'name_asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3" style="gap: 1.5rem;">
                        <?php while ($product = $products->fetch_assoc()): ?>
                            <div class="product-card-premium card-shimmer" style="display: flex; flex-direction: column;">
                                <a href="product.php?id=<?php echo $product['product_id']; ?>" class="image-wrap hover-zoom" style="height: 200px;">
                                    <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($product['product_name']); ?>"
                                         onerror="this.src='assets/images/placeholder.jpg';">
                                </a>
                                <div class="product-info" style="flex: 1; display: flex; flex-direction: column; padding: 1.5rem;">
                                    <span style="color: var(--primary); font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem; display: block;">
                                        <?php echo htmlspecialchars($product['category_name']); ?>
                                    </span>
                                    <h4 style="font-size: 1.15rem; margin-bottom: 0.25rem;">
                                        <a href="product.php?id=<?php echo $product['product_id']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></a>
                                    </h4>
                                    <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem;"><?php echo htmlspecialchars($product['brand']); ?></p>
                                    
                                    <div class="item-footer" style="border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: auto;">
                                        <span class="price"><?php echo formatPrice($product['price']); ?></span>
                                        <div style="display: flex; gap: 0.4rem; align-items: center;">
                                            <?php if ($product['stock'] > 0): ?>
                                                <form method="POST" action="" style="margin: 0;">
                                                    <?php echo csrfInput(); ?>
                                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                    <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm" title="Add to Cart">
                                                        <i class="fas fa-plus"></i> Add
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-secondary btn-sm" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if ($total_pages > 1): ?>
                        <?php
                        // Build query string ensuring persistence when clicking a page link
                        $qp = $_GET;
                        unset($qp['page']);
                        $query_string = http_build_query($qp);
                        $query_string = !empty($query_string) ? '&' . $query_string : '';
                        ?>
                        <div class="pagination" style="margin-top: 4rem; display: flex; justify-content: center; gap: 0.5rem;">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo ($page - 1) . $query_string; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem;"><i class="fas fa-chevron-left"></i></a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i . $query_string; ?>" class="btn <?php echo $page === $i ? 'btn-primary' : 'btn-secondary'; ?>" style="padding: 0.5rem 1rem; min-width: 40px; text-align: center;">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo ($page + 1) . $query_string; ?>" class="btn btn-secondary" style="padding: 0.5rem 1rem;"><i class="fas fa-chevron-right"></i></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="glass-card text-center" style="padding: 4rem;">
                        <i class="fas fa-box-open" style="font-size: 4rem; color: var(--border-color); margin-bottom: 2rem;"></i>
                        <h3>No instruments found</h3>
                        <p style="color: var(--text-muted); margin-bottom: 2rem;">Try adjusting your filters or search keywords.</p>
                        <a href="shop.php" class="btn btn-primary">Reset All Filters</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
