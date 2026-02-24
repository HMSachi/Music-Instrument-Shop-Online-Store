<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$message = '';
$error = '';

// Handle product actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security validation failed. Please try again.";
        redirect('admin/manage_products.php');
    }

    if (isset($_POST['add_product'])) {
        $product_name = sanitizeInput($_POST['product_name']);
        $brand = sanitizeInput($_POST['brand']);
        $category_id = (int)$_POST['category_id'];
        $description = sanitizeInput($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $product_type = $_POST['product_type'];
        $image = sanitizeInput($_POST['image']);
        
        $sql = "INSERT INTO products (product_name, brand, category_id, description, price, stock, product_type, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [$product_name, $brand, $category_id, $description, $price, $stock, $product_type, $image];
        $types = "ssisdiss";
        
        if (preparedQuery($conn, $sql, $params, $types)) {
            $message = 'Product added successfully!';
        } else {
            $error = 'Failed to add product.';
        }
    }
    
    if (isset($_POST['delete_product'])) {
        $product_id = (int)$_POST['product_id'];
        $sql = "DELETE FROM products WHERE product_id = ?";
        
        if (preparedQuery($conn, $sql, [$product_id], "i")) {
            $message = 'Product deleted successfully!';
        } else {
            $error = 'Failed to delete product';
        }
    }

    if (isset($_POST['edit_product'])) {
        $product_id = (int)$_POST['product_id'];
        $product_name = sanitizeInput($_POST['product_name']);
        $brand = sanitizeInput($_POST['brand']);
        $category_id = (int)$_POST['category_id'];
        $description = sanitizeInput($_POST['description']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $product_type = $_POST['product_type'];
        $image = sanitizeInput($_POST['image']);
        
        $sql = "UPDATE products SET 
                product_name = ?, 
                brand = ?, 
                category_id = ?, 
                description = ?, 
                price = ?, 
                stock = ?, 
                product_type = ?, 
                image = ? 
                WHERE product_id = ?";
        
        $params = [$product_name, $brand, $category_id, $description, $price, $stock, $product_type, $image, $product_id];
        $types = "ssisdissi";
        
        if (preparedQuery($conn, $sql, $params, $types)) {
            $message = 'Product updated successfully!';
        } else {
            $error = 'Failed to update product.';
        }
    }
}

// Get product for editing
$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_res = preparedQuery($conn, "SELECT * FROM products WHERE product_id = ?", [$edit_id], "i");
    if ($edit_res && $edit_res->num_rows > 0) {
        $edit_product = $edit_res->fetch_assoc();
    }
}

// Get all products
$products = preparedQuery($conn, "SELECT p.*, c.category_name FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.category_id 
                          ORDER BY p.created_at DESC");

// Get categories for dropdown
$categories = preparedQuery($conn, "SELECT * FROM categories WHERE parent_id IS NULL");

$page_title = 'Manage Products - Admin';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-box"></i> Manage Products</h1>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="admin-layout">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <nav class="admin-nav">
                    <a href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_products.php" class="active">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="manage_users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                    <a href="manage_orders.php">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                    <a href="<?php echo SITE_URL; ?>/index.php">
                        <i class="fas fa-home"></i> Back to Site
                    </a>
                </nav>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                <!-- Product Form (Add/Edit) -->
                <div class="admin-section-box">
                    <h2><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h2>
                    <form method="POST" action="" class="admin-form">
                        <?php echo csrfInput(); ?>
                        <?php if ($edit_product): ?>
                            <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="product_name">Product Name *</label>
                                <input type="text" id="product_name" name="product_name" value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_name']) : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="brand">Brand *</label>
                                <input type="text" id="brand" name="brand" value="<?php echo $edit_product ? htmlspecialchars($edit_product['brand']) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="category_id">Category *</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Select Category</option>
                                    <?php 
                                    $categories->data_seek(0);
                                    while ($cat = $categories->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $cat['category_id']; ?>" <?php echo ($edit_product && $edit_product['category_id'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['category_name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="product_type">Type *</label>
                                <select id="product_type" name="product_type" required>
                                    <option value="physical" <?php echo ($edit_product && $edit_product['product_type'] == 'physical') ? 'selected' : ''; ?>>Physical</option>
                                    <option value="digital" <?php echo ($edit_product && $edit_product['product_type'] == 'digital') ? 'selected' : ''; ?>>Digital</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Price *</label>
                                <input type="number" id="price" name="price" step="0.01" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Stock *</label>
                                <input type="number" id="stock" name="stock" value="<?php echo $edit_product ? $edit_product['stock'] : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" rows="3"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Image URL</label>
                            <input type="text" id="image" name="image" value="<?php echo $edit_product ? htmlspecialchars($edit_product['image']) : ''; ?>" placeholder="assets/images/product.jpg">
                        </div>
                        
                        <?php if ($edit_product): ?>
                            <button type="submit" name="edit_product" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="manage_products.php" class="btn btn-outline">Cancel</a>
                        <?php else: ?>
                            <button type="submit" name="add_product" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Product
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
                
                <!-- Products List -->
                <div class="admin-section-box">
                    <h2>All Products</h2>
                    
                    <?php if ($products && $products->num_rows > 0): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($product = $products->fetch_assoc()): 
                                    $is_low_stock = ($product['stock'] <= 5 && $product['product_type'] === 'physical');
                                ?>
                                    <tr class="<?php echo $is_low_stock ? 'row-warning' : ''; ?>">
                                        <td><?php echo $product['product_id']; ?></td>
                                        <td>
                                            <div class="admin-product-thumb">
                                                <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                     alt="thumb"
                                                     onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder.jpg';">
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($product['product_name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($product['brand']); ?></small>
                                        </td>
                                        <td><?php echo formatPrice($product['price']); ?></td>
                                        <td>
                                            <?php echo $product['stock']; ?>
                                            <?php if ($is_low_stock): ?>
                                                <br><span class="badge badge-danger">Low Stock</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $product['product_type'] === 'digital' ? 'info' : 'success'; ?>">
                                                <?php echo ucfirst($product['product_type']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="admin-actions">
                                                <a href="?edit=<?php echo $product['product_id']; ?>" class="btn-sm btn-outline">Edit</a>
                                                <form method="POST" style="display: inline;">
                                                    <?php echo csrfInput(); ?>
                                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                    <button type="submit" name="delete_product" class="btn-sm btn-danger" 
                                                            onclick="return confirm('Delete this product?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No products found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
