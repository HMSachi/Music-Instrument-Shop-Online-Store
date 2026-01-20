<?php
include '../includes/db_connection.php';
include '../includes/session.php';
include '../includes/product_manager.php';
include '../includes/order_manager.php';

require_admin();

$pm = new ProductManager($db);
$om = new OrderManager($db);
$message = '';
$action = '';

// Handle product operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'add_product') {
            $result = $pm->add_product($_POST);
            $message = $result['message'];
        } elseif ($action === 'update_product') {
            $result = $pm->update_product($_POST['product_id'], $_POST);
            $message = $result['message'];
        } elseif ($action === 'delete_product') {
            $result = $pm->delete_product($_POST['product_id']);
            $message = $result['message'];
        }
    }
}

$products = $pm->get_products();
$categories = $pm->get_categories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Melody Masters</title>
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
        <h1>Admin Dashboard</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Add Product Form -->
        <div class="card mb-3">
            <h2>Add New Product</h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="add_product">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (£)</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" min="0" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="type">Product Type</label>
                        <select id="type" name="type" required>
                            <option value="Physical">Physical</option>
                            <option value="Digital">Digital</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image Filename</label>
                        <input type="text" id="image" name="image" placeholder="e.g., guitar.jpg">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Add Product</button>
            </form>
        </div>

        <!-- Products Table -->
        <div class="card">
            <h2>Manage Products</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                            <td>£<?php echo number_format($product['price'], 2); ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            <td><?php echo $product['type']; ?></td>
                            <td>
                                <a href="/admin/edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-secondary btn-small">Edit</a>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_product">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn btn-warning btn-small" onclick="return confirm('Delete this product?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
