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
        <div style="margin-bottom: 4rem;">
            <h1 class="text-gold">Inventory Management</h1>
            <p style="color: var(--text-muted);">Configure and manage your premium instrument collection.</p>
        </div>
        
        <div class="admin-layout animate-fade-in">
            <!-- Sidebar -->
            <aside class="admin-sidebar">
                <div class="glass-card card-shimmer" style="padding: 2.5rem; position: sticky; top: 100px;">
                    <nav class="dashboard-nav">
                        <a href="dashboard.php">
                            <i class="fas fa-chart-line"></i> Analytics Overview
                        </a>
                        <a href="manage_products.php" class="active">
                            <i class="fas fa-guitar"></i> Instrument Inventory
                        </a>
                        <a href="manage_users.php">
                            <i class="fas fa-user-friends"></i> User Base
                        </a>
                        <a href="manage_orders.php">
                            <i class="fas fa-receipt"></i> Order Management
                        </a>
                    </nav>
                </div>
            </aside>
            
            <!-- Content -->
            <div class="admin-content">
                <!-- Product Form (Add/Edit) -->
                <div class="admin-section-box card-shimmer" style="margin-bottom: 3rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem;">
                        <h2 style="margin: 0;"><?php echo $edit_product ? 'Edit Instrument' : 'Catalog New Instrument'; ?></h2>
                        <?php if ($edit_product): ?>
                            <a href="manage_products.php" class="btn btn-sm btn-secondary">New Product</a>
                        <?php endif; ?>
                    </div>
                    
                    <form method="POST" action="" class="admin-form">
                        <?php echo csrfInput(); ?>
                        <?php if ($edit_product): ?>
                            <input type="hidden" name="product_id" value="<?php echo $edit_product['product_id']; ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="product_name">Product Nomenclature *</label>
                                <input type="text" id="product_name" name="product_name" value="<?php echo $edit_product ? htmlspecialchars($edit_product['product_name']) : ''; ?>" required placeholder="e.g. Steinway Model D">
                            </div>
                            
                            <div class="form-group">
                                <label for="brand">Brand / Manufacturer *</label>
                                <input type="text" id="brand" name="brand" value="<?php echo $edit_product ? htmlspecialchars($edit_product['brand']) : ''; ?>" required placeholder="e.g. Steinway & Sons">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="category_id">Market Category *</label>
                                <select id="category_id" name="category_id" required>
                                    <option value="">Select Classification</option>
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
                                <label for="product_type">Fulfillment Model *</label>
                                <select id="product_type" name="product_type" required>
                                    <option value="physical" <?php echo ($edit_product && $edit_product['product_type'] == 'physical') ? 'selected' : ''; ?>>Physical Asset</option>
                                    <option value="digital" <?php echo ($edit_product && $edit_product['product_type'] == 'digital') ? 'selected' : ''; ?>>Digital Distribution</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="price">Listing Price (GBP) *</label>
                                <input type="number" id="price" name="price" step="0.01" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required placeholder="0.00">
                            </div>
                            
                            <div class="form-group">
                                <label for="stock">Inventory Level *</label>
                                <input type="number" id="stock" name="stock" value="<?php echo $edit_product ? $edit_product['stock'] : ''; ?>" required placeholder="Available units">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Product Narrative</label>
                            <textarea id="description" name="description" rows="4" placeholder="Describe the sonic characteristics and craftsmanship..."><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="image">Visual Asset Path</label>
                            <div style="display: flex; gap: 2rem; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <input type="text" id="image" name="image" value="<?php echo $edit_product ? htmlspecialchars($edit_product['image']) : ''; ?>" placeholder="assets/images/product.jpg" oninput="updateImagePreview(this.value)">
                                    <div class="asset-suggestion-list">
                                        <span class="asset-tag" onclick="document.getElementById('image').value='assets/images/Digital Piano 88 Keys.jpg'; updateImagePreview('assets/images/Digital Piano 88 Keys.jpg')">Piano</span>
                                        <span class="asset-tag" onclick="document.getElementById('image').value='assets/images/Drum Kit 5-Piece.jpg'; updateImagePreview('assets/images/Drum Kit 5-Piece.jpg')">Drums</span>
                                        <span class="asset-tag" onclick="document.getElementById('image').value='assets/images/Sepina.jpg'; updateImagePreview('assets/images/Sepina.jpg')">Sepina</span>
                                        <span class="asset-tag" onclick="document.getElementById('image').value='assets/images/Theory Course (Digital).jpg'; updateImagePreview('assets/images/Theory Course (Digital).jpg')">Theory</span>
                                    </div>
                                </div>
                                <div class="image-preview-container" style="margin: 0;">
                                    <img id="image-preview" src="<?php echo SITE_URL; ?>/<?php echo $edit_product ? ($edit_product['image'] ?: 'assets/images/placeholder.jpg') : 'assets/images/placeholder.jpg'; ?>" alt="Preview">
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 3rem; pt: 2rem; border-top: 1px solid var(--border-color); display: flex; gap: 1rem;">
                            <?php if ($edit_product): ?>
                                <button type="submit" name="edit_product" class="btn btn-primary">
                                    <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Update Instrument
                                </button>
                                <a href="manage_products.php" class="btn btn-secondary">Discard Changes</a>
                            <?php else: ?>
                                <button type="submit" name="add_product" class="btn btn-primary">
                                    <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Commit to Catalog
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <!-- Products List -->
                <div class="admin-section-box">
                    <h2 style="margin-bottom: 2.5rem;">Inventory Repository</h2>
                    
                    <?php if ($products && $products->num_rows > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Visual</th>
                                        <th>Instrument</th>
                                        <th>Price</th>
                                        <th>Level</th>
                                        <th>Model</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($product = $products->fetch_assoc()): 
                                        $is_low_stock = ($product['stock'] <= 5 && $product['product_type'] === 'physical');
                                    ?>
                                        <tr class="<?php echo $is_low_stock ? 'row-warning' : ''; ?>">
                                            <td style="font-family: monospace; font-weight: 700; color: var(--primary);">#<?php echo $product['product_id']; ?></td>
                                            <td>
                                                <div class="admin-product-thumb">
                                                    <img src="<?php echo rtrim(SITE_URL, '/') . '/' . ($product['image'] ? ltrim($product['image'], '/') : 'assets/images/placeholder.jpg'); ?>" 
                                                         alt=""
                                                         onerror="this.src='<?php echo SITE_URL; ?>/assets/images/placeholder.jpg';">
                                                </div>
                                            </td>
                                            <td>
                                                <div style="color: var(--text-main); font-weight: 700;"><?php echo htmlspecialchars($product['product_name']); ?></div>
                                                <div style="color: var(--text-muted); font-size: 0.8rem;"><?php echo htmlspecialchars($product['brand']); ?> • <?php echo htmlspecialchars($product['category_name']); ?></div>
                                            </td>
                                            <td style="color: var(--text-main); font-weight: 600;"><?php echo formatPrice($product['price']); ?></td>
                                            <td>
                                                <div style="font-weight: 800; color: <?php echo $is_low_stock ? '#e74c3c' : 'var(--text-main)'; ?>;">
                                                    <?php echo $product['stock']; ?>
                                                </div>
                                                <?php if ($is_low_stock): ?>
                                                    <span class="badge badge-cancelled" style="font-size: 0.65rem; padding: 0.1rem 0.4rem;">CRITICAL</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?php echo $product['product_type'] === 'digital' ? 'info' : 'success'; ?>">
                                                    <?php echo ucfirst($product['product_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="admin-actions">
                                                    <a href="?edit=<?php echo $product['product_id']; ?>" class="btn btn-sm btn-secondary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" style="display: inline;">
                                                        <?php echo csrfInput(); ?>
                                                        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                                        <button type="submit" name="delete_product" class="btn btn-sm btn-danger" 
                                                                onclick="return confirm('Secure deletion: Are you sure you want to remove this instrument?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 5rem 0;">
                            <i class="fas fa-box-open" style="font-size: 3rem; color: rgba(255,255,255,0.05); margin-bottom: 2rem; display: block;"></i>
                            <p style="color: var(--text-muted);">The inventory is currently empty.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
