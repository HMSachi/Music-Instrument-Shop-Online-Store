<?php
include '../includes/db_connection.php';
include '../includes/session.php';
include '../includes/product_manager.php';

require_admin();

$pm = new ProductManager($db);

if (!isset($_GET['id'])) {
    header("Location: /admin/dashboard.php");
    exit();
}

$product = $pm->get_product($_GET['id']);

if (!$product) {
    header("Location: /admin/dashboard.php");
    exit();
}

$categories = $pm->get_categories();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = $pm->update_product($_GET['id'], $_POST);
    $message = $result['message'];
    if ($result['success']) {
        $product = $pm->get_product($_GET['id']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Melody Masters</title>
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
        <h1>Edit Product</h1>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="card" style="max-width: 700px;">
            <form method="POST" action="">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select id="category_id" name="category_id" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Price (£)</label>
                        <input type="number" id="price" name="price" value="<?php echo $product['price']; ?>" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" value="<?php echo $product['stock']; ?>" min="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="image">Image Filename</label>
                    <input type="text" id="image" name="image" value="<?php echo htmlspecialchars($product['image']); ?>" placeholder="e.g., guitar.jpg">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-success">Update Product</button>
                    <a href="/admin/dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
