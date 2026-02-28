<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$message = '';
$error = '';

// Handle category actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security validation failed. Please try again.";
        redirect('admin/manage_categories.php');
    }

    if (isset($_POST['add_category'])) {
        $category_name = sanitizeInput($_POST['category_name']);
        $description = sanitizeInput($_POST['description']);
        
        $sql = "INSERT INTO categories (category_name, description) VALUES (?, ?)";
        
        if (preparedQuery($conn, $sql, [$category_name, $description], "ss")) {
            $message = 'Category added successfully!';
        } else {
            $error = 'Failed to add category.';
        }
    }
    
    if (isset($_POST['delete_category'])) {
        $category_id = (int)$_POST['category_id'];
        
        // Check if category has products
        $check_sql = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
        $check_res = preparedQuery($conn, $check_sql, [$category_id], "i");
        $count = $check_res->fetch_assoc()['count'];
        
        if ($count > 0) {
            $error = "Cannot delete category: It contains $count active product(s). Please reassign them first.";
        } else {
            $sql = "DELETE FROM categories WHERE category_id = ?";
            if (preparedQuery($conn, $sql, [$category_id], "i")) {
                $message = 'Category deleted successfully!';
            } else {
                $error = 'Failed to delete category';
            }
        }
    }

    if (isset($_POST['edit_category'])) {
        $category_id = (int)$_POST['category_id'];
        $category_name = sanitizeInput($_POST['category_name']);
        $description = sanitizeInput($_POST['description']);
        
        $sql = "UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?";
        
        if (preparedQuery($conn, $sql, [$category_name, $description, $category_id], "ssi")) {
            $message = 'Category updated successfully!';
        } else {
            $error = 'Failed to update category.';
        }
    }
}

// Get category for editing
$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_res = preparedQuery($conn, "SELECT * FROM categories WHERE category_id = ?", [$edit_id], "i");
    if ($edit_res && $edit_res->num_rows > 0) {
        $edit_category = $edit_res->fetch_assoc();
    }
}

// Get all categories with product counts
$categories_sql = "SELECT c.*, COUNT(p.product_id) as product_count 
                   FROM categories c 
                   LEFT JOIN products p ON c.category_id = p.category_id 
                   GROUP BY c.category_id 
                   ORDER BY c.category_name ASC";
$categories = preparedQuery($conn, $categories_sql);

$page_title = 'Manage Categories - Admin';
$body_class = 'admin-mode';
include '../includes/header.php';
?>

<!-- Admin Redesign Wrapper -->
<div class="admin-wrapper slide-up-fade">
    <!-- Floating Glass Sidebar -->
    <aside class="admin-sidebar-glass">
        <div class="admin-profile-badge">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
            </div>
            <h4>Site Administrator</h4>
            <div style="color: var(--admin-primary); font-size: 0.8rem; margin-top: 0.25rem;">Super Admin</div>
        </div>
        
        <nav class="admin-nav-menu">
            <a href="dashboard.php" class="admin-nav-item">
                <i class="fas fa-satellite-dish"></i> Command Center
            </a>
            <a href="manage_products.php" class="admin-nav-item">
                <i class="fas fa-guitar"></i> Instrument Vault
            </a>
            <a href="manage_users.php" class="admin-nav-item">
                <i class="fas fa-users-cog"></i> User Base
            </a>
            <a href="manage_categories.php" class="admin-nav-item active">
                <i class="fas fa-tags"></i> Category Logic
            </a>
            <a href="manage_orders.php" class="admin-nav-item">
                <i class="fas fa-file-invoice-dollar"></i> Global Ledgers
            </a>
            
            <div class="admin-nav-divider"></div>
            
            <a href="<?php echo SITE_URL; ?>/index.php" class="admin-nav-item" style="color: var(--admin-primary);">
                <i class="fas fa-external-link-alt"></i> Storefront
            </a>
        </nav>
    </aside>

    <!-- Main Content Panel -->
    <main class="admin-main-content">
        <div class="admin-page-header">
            <h1 class="admin-page-title">Category Logic</h1>
            <p class="admin-page-subtitle">Define and organize the structural hierarchy of your store.</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" style="margin-bottom: 2rem; border-radius: 12px; background: rgba(46, 213, 115, 0.1); border: 1px solid #2ecc71; color: #2ecc71;">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="margin-bottom: 2rem; border-radius: 12px; background: rgba(231, 76, 60, 0.1); border: 1px solid #e74c3c; color: #e74c3c;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="admin-data-grid">
            <!-- Category Form (Add/Edit) -->
            <div class="admin-panel slide-up-fade delay-1" style="height: fit-content;">
                <div class="admin-panel-header">
                    <h2><?php echo $edit_category ? 'Edit Category' : 'Create New Category'; ?></h2>
                    <?php if ($edit_category): ?>
                        <a href="manage_categories.php" class="admin-btn-outline" style="font-size: 0.75rem;">New Form</a>
                    <?php endif; ?>
                </div>
                
                <form method="POST" action="" class="admin-form">
                    <?php echo csrfInput(); ?>
                    <?php if ($edit_category): ?>
                        <input type="hidden" name="category_id" value="<?php echo $edit_category['category_id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="category_name">Category Nomenclature *</label>
                        <input type="text" id="category_name" name="category_name" value="<?php echo $edit_category ? htmlspecialchars($edit_category['category_name']) : ''; ?>" required placeholder="e.g. Grand Pianos">
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 2.5rem;">
                        <label for="description">Classification Rules (Optional)</label>
                        <textarea id="description" name="description" rows="5" placeholder="Internal notes or public description for this category..."><?php echo $edit_category ? htmlspecialchars($edit_category['description']) : ''; ?></textarea>
                    </div>
                    
                    <div>
                        <?php if ($edit_category): ?>
                            <button type="submit" name="edit_category" class="admin-btn-outline" style="background: var(--admin-primary); color: #000; width: 100%;">
                                <i class="fas fa-save" style="margin-right: 0.5rem;"></i> Update Category Matrix
                            </button>
                        <?php else: ?>
                            <button type="submit" name="add_category" class="admin-btn-outline" style="background: var(--admin-primary); color: #000; width: 100%;">
                                <i class="fas fa-plus" style="margin-right: 0.5rem;"></i> Establish Category
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <!-- Category List -->
            <div class="admin-panel slide-up-fade delay-2">
                <div class="admin-panel-header">
                    <h2>Active Classifications</h2>
                </div>
                
                <?php if ($categories && $categories->num_rows > 0): ?>
                    <div class="admin-table-wrapper">
                        <table class="admin-modern-table">
                            <thead>
                                <tr>
                                    <th>Ref ID</th>
                                    <th>Nomenclature</th>
                                    <th>Assigned Assets</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($category = $categories->fetch_assoc()): ?>
                                    <tr>
                                        <td class="order-id">#<?php echo $category['category_id']; ?></td>
                                        <td style="font-weight: 600; color: #fff;">
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </td>
                                        <td>
                                            <span class="admin-badge" style="background: rgba(52,152,219,0.15); color: #3498db;">
                                                <?php echo $category['product_count']; ?> Asset(s)
                                            </span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.75rem;">
                                                <a href="?edit=<?php echo $category['category_id']; ?>" style="color: #3498db; transition: color 0.3s ease; padding: 0.5rem; background: rgba(52,152,219,0.1); border-radius: 8px;">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form method="POST" style="display: inline;">
                                                    <?php echo csrfInput(); ?>
                                                    <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                                                    <button type="submit" name="delete_category" 
                                                            style="background: rgba(231,76,60,0.1); border: none; color: #e74c3c; cursor: pointer; transition: all 0.3s ease; padding: 0.5rem; border-radius: 8px;"
                                                            onclick="return confirm('WARNING: Are you sure you want to delete this category? This dictates store structure.')">
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
                    <div style="text-align: center; padding: 3rem 0; color: var(--admin-text-muted);">
                        <i class="fas fa-folder-open fa-3x" style="opacity: 0.5; margin-bottom: 1rem;"></i>
                        <p>No taxonomy defined. Create your first category to begin.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
