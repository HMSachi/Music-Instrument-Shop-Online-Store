<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$message = '';
$error = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security validation failed. Please try again.";
        redirect('admin/manage_users.php');
    }

    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        
        // Prevent self-deletion
        if ($user_id === $_SESSION['user_id']) {
            $_SESSION['error'] = "You cannot delete your own account.";
        } else {
            $sql = "DELETE FROM users WHERE user_id = ?";
            if (preparedQuery($conn, $sql, [$user_id], "i")) {
                $message = 'User deleted successfully!';
            } else {
                $error = 'Failed to delete user';
            }
        }
    }
    
    if (isset($_POST['update_role'])) {
        $user_id = (int)$_POST['user_id'];
        $role = sanitizeInput($_POST['role']);
        
        // Validate role input
        $allowed_roles = ['customer', 'staff', 'admin'];
        if (!in_array($role, $allowed_roles)) {
            $error = 'Invalid role selected';
        } else {
            $sql = "UPDATE users SET role = ? WHERE user_id = ?";
            if (preparedQuery($conn, $sql, [$role, $user_id], "si")) {
                $message = 'User role updated successfully!';
            } else {
                $error = 'Failed to update role';
            }
        }
    }
}

// Get all users
$users = preparedQuery($conn, "SELECT * FROM users ORDER BY created_at DESC");

$page_title = 'Manage Users - Admin';
include '../includes/header.php';
?>

<section class="admin-section">
    <div class="container">
        <div class="admin-header">
            <h1><i class="fas fa-users"></i> Manage Users</h1>
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
                    <a href="manage_products.php">
                        <i class="fas fa-box"></i> Products
                    </a>
                    <a href="manage_users.php" class="active">
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
                <div class="admin-section-box">
                    <h2>All Users</h2>
                    
                    <?php if ($users && $users->num_rows > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Role</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $user['user_id']; ?></td>
                                            <td><strong><?php echo htmlspecialchars($user['full_name']); ?></strong></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <?php echo csrfInput(); ?>
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <select name="role" onchange="this.form.submit()" class="form-control-sm">
                                                        <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                        <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    </select>
                                                    <input type="hidden" name="update_role">
                                                </form>
                                            </td>
                                            <td><small style="color: var(--text-light);"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></small></td>
                                            <td>
                                                <div class="admin-actions">
                                                    <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <?php echo csrfInput(); ?>
                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Delete this user?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge badge-info" style="position: static;">You</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No users found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
