<?php
require_once __DIR__ . '/includes/session.php';

$base = BASE_PATH;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - Melody Masters</title>
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/style.css">
</head>
<body>
    <header>
        <nav class="container">
            <a href="<?php echo $base; ?>/index.php" class="logo">Melody Masters</a>
            <ul class="nav-links">
                <li><a href="<?php echo $base; ?>/products_store.php">🛒 Shop</a></li>
                <li><a href="<?php echo $base; ?>/login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div class="card" style="text-align: center; padding: 3rem; margin: 3rem 0;">
            <h1 style="color: #C0392B; font-size: 2.5rem;">403 - Access Denied</h1>
            <p style="font-size: 1.1rem; margin: 1rem 0;">You don't have permission to access this resource.</p>
            <p style="color: #7F8C8D; margin-bottom: 2rem;">Your account role doesn't have the required permissions.</p>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="<?php echo $base; ?>/products_store.php" class="btn btn-primary">Continue Shopping</a>
                <a href="<?php echo $base; ?>/index.php" class="btn btn-secondary">Go Home</a>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Melody Masters. All rights reserved.</p>
    </footer>
</body>
</html>
