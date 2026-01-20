<?php
require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/product_manager.php';

require_admin();

$base = BASE_PATH;
$pm = new ProductManager($db);
$products = $pm->get_products();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Melody Masters</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/admin/dashboard.php">Admin</a></li>
                <li><a href="<?php echo $base; ?>/public/logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h1>Admin Dashboard</h1>
        <p>Manage products, categories, and inventory.</p>

        <div style="margin: 2rem 0;">
            <a class="btn btn-primary" href="<?php echo $base; ?>/admin/add_product.php">+ Add Product</a>
        </div>

        <div class="card">
            <h2>Products</h2>
            <?php if (empty($products)): ?>
                <p>No products yet.</p>
            <?php else: ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #F7F9FA; border-bottom: 2px solid #D6DDE3;">
                            <th style="padding: 1rem; text-align: left;">ID</th>
                            <th style="padding: 1rem; text-align: left;">Product Name</th>
                            <th style="padding: 1rem; text-align: left;">Brand</th>
                            <th style="padding: 1rem; text-align: left;">Price</th>
                            <th style="padding: 1rem; text-align: left;">Stock</th>
                            <th style="padding: 1rem; text-align: left;">Type</th>
                            <th style="padding: 1rem; text-align: left;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr style="border-bottom: 1px solid #D6DDE3;">
                                <td style="padding: 1rem;"><?php echo $p['product_id']; ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($p['product_name']); ?></td>
                                <td style="padding: 1rem;"><?php echo htmlspecialchars($p['brand']); ?></td>
                                <td style="padding: 1rem;">$<?php echo number_format($p['price'], 2); ?></td>
                                <td style="padding: 1rem;"><?php echo (int)$p['stock']; ?></td>
                                <td style="padding: 1rem;"><?php echo ucfirst($p['product_type']); ?></td>
                                <td style="padding: 1rem;">
                                    <a class="btn btn-secondary btn-small" href="<?php echo $base; ?>/admin/edit_product.php?id=<?php echo $p['product_id']; ?>">Edit</a>
                                    <a class="btn btn-small" style="background: #C0392B; color: #fff;" href="<?php echo $base; ?>/admin/delete_product.php?id=<?php echo $p['product_id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
