<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();
requireAdmin();

$message = '';
$error = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_user'])) {
        $user_id = (int)$_POST['user_id'];
        $sql = "DELETE FROM users WHERE user_id = $user_id";
        
        if ($conn->query($sql)) {
            $message = 'User deleted successfully!';
        } else {
            $error = 'Failed to delete user';
        }
    }
    
    if (isset($_POST['update_role'])) {
        $user_id = (int)$_POST['user_id'];
        $role = $conn->real_escape_string($_POST['role']);
        $sql = "UPDATE users SET role = '$role' WHERE user_id = $user_id";
        
        if ($conn->query($sql)) {
            $message = 'User role updated successfully!';
        } else {
            $error = 'Failed to update role';
        }
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

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
                        <table class="admin-table">
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
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                <select name="role" onchange="this.form.submit()" class="role-select">
                                                    <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                    <option value="staff" <?php echo $user['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                                <input type="hidden" name="update_role">
                                            </form>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <?php if ($user['user_id'] !== $_SESSION['user_id']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <button type="submit" name="delete_user" class="btn-sm btn-danger" 
                                                            onclick="return confirm('Delete this user?')">
                                                        Delete
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">You</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No users found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
