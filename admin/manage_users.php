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

<!-- Admin Redesign Wrapper -->
<div class="admin-wrapper slide-up-fade">
    <!-- Floating Glass Sidebar -->
    <aside class="admin-sidebar-glass">
        <div class="admin-profile-badge">
            <div class="admin-avatar">
                <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)); ?>
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
            <a href="manage_users.php" class="admin-nav-item active">
                <i class="fas fa-users-cog"></i> User Base
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
            <h1 class="admin-page-title">User Registry</h1>
            <p class="admin-page-subtitle">Monitor and manage access levels for the Melody Masters community.</p>
        </div>
                <div class="admin-section-box card-shimmer">
                    <h2 style="margin-bottom: 2.5rem;">Verified Membership</h2>
                    
                    <?php if ($users && $users->num_rows > 0): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Member</th>
                                        <th>Contact</th>
                                        <th>Permissions</th>
                                        <th>Registered</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td style="font-family: monospace; font-weight: 700; color: var(--primary);">#<?php echo $user['user_id']; ?></td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 1rem;">
                                                    <div style="width: 35px; height: 35px; background: rgba(255,255,255,0.05); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; border: 1px solid var(--border-color);">
                                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                                    </div>
                                                    <div style="color: var(--text-main); font-weight: 700;"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="color: var(--text-muted); font-size: 0.9rem;"><?php echo htmlspecialchars($user['email']); ?></div>
                                                <div style="color: rgba(255,255,255,0.3); font-size: 0.75rem;"><?php echo htmlspecialchars($user['phone'] ?? 'No phone recorded'); ?></div>
                                            </td>
                                            <td>
                                                <form method="POST" style="display: inline;">
                                                    <?php echo csrfInput(); ?>
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <select name="role" onchange="this.form.submit()" class="form-control-sm" style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-color); color: var(--text-main); border-radius: var(--radius-sm); padding: 0.25rem 0.5rem; outline: none; transition: var(--transition);">
                                                        <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                        <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff Access</option>
                                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                                    </select>
                                                    <input type="hidden" name="update_role">
                                                </form>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <div style="color: var(--text-muted); font-size: 0.85rem;"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                                            </td>
                                            <td>
                                                <div class="admin-actions">
                                                    <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                                                        <form method="POST" style="display: inline;">
                                                            <?php echo csrfInput(); ?>
                                                            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                            <button type="submit" name="delete_user" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Secure deletion: Revoke all access for this user?')">
                                                                <i class="fas fa-user-minus"></i>
                                                            </button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge badge-info" style="font-size: 0.65rem;">ACTIVE SESSION</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 5rem 0;">
                            <i class="fas fa-users-slash" style="font-size: 3rem; color: rgba(255,255,255,0.05); margin-bottom: 2rem; display: block;"></i>
                            <p style="color: var(--text-muted);">No users identified in the repository.</p>
                        </div>
                    <?php endif; ?>
                </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>
