<?php
include '../includes/db_connection.php';
include '../includes/session.php';
include '../includes/auth.php';

require_admin();

$auth = new AuthHandler($db);
$users = $auth->get_all_users();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'update_role') {
            $result = $auth->update_user_role($_POST['user_id'], $_POST['role']);
            $message = $result['message'];
            $users = $auth->get_all_users();
        } elseif ($_POST['action'] === 'deactivate') {
            $result = $auth->deactivate_user($_POST['user_id']);
            $message = $result['message'];
            $users = $auth->get_all_users();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Melody Masters</title>
    <link rel="stylesheet" href="/Music-Instrument-Shop-Online-Store/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="/index.php" class="logo">🎵 Melody Masters</a>
            <ul class="nav-links">
                <li><a href="/admin/dashboard.php">Dashboard</a></li>
                <li><a href="/admin/users.php">Users</a></li>
                <li><a href="/public/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>User Management</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="update_role">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="role" onchange="this.form.submit();" style="padding: 0.5rem;">
                                        <option value="Customer" <?php echo ($user['role'] === 'Customer') ? 'selected' : ''; ?>>Customer</option>
                                        <option value="Staff" <?php echo ($user['role'] === 'Staff') ? 'selected' : ''; ?>>Staff</option>
                                        <option value="Admin" <?php echo ($user['role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </form>
                            </td>
                            <td><?php echo ($user['is_active']) ? '✓ Active' : '✗ Inactive'; ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <form method="POST" action="" style="display: inline;">
                                        <input type="hidden" name="action" value="deactivate">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" class="btn btn-warning btn-small" onclick="return confirm('Deactivate this user?');">Deactivate</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 2rem;">
            <a href="/admin/dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
