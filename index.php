<?php
session_start();
require_once 'config/db_connect.php';

// Fetch products
$products = $pdo->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Masters - Premium Music Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --bg: #0f172a;
            --card-bg: #1e293b;
            --text: #f8fafc;
            --text-muted: #94a3b8;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text); margin: 0; }
        
        /* Navbar */
        .navbar {
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(10px);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo { font-size: 1.5rem; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 0.5rem; }
        
        .nav-links { display: flex; gap: 2rem; align-items: center; }
        .nav-links a { color: var(--text-muted); text-decoration: none; font-weight: 500; transition: color 0.2s; }
        .nav-links a:hover { color: white; }

        .btn-login { background: var(--primary); color: white; padding: 0.5rem 1.25rem; border-radius: 0.5rem; font-weight: 600; }

        /* Hero Section */
        .hero { padding: 4rem 2rem; text-align: center; background: radial-gradient(circle at top, rgba(99, 102, 241, 0.15), transparent); }
        .hero h1 { font-size: 3.5rem; margin-bottom: 1rem; }
        .hero p { color: var(--text-muted); font-size: 1.25rem; max-width: 600px; margin: 0 auto 2rem; }

        /* Product Grid */
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .section-title { font-size: 1.5rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; }
        
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; }
        
        .product-card {
            background: var(--card-bg);
            border-radius: 1rem;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3); }
        
        .product-img { width: 100%; height: 200px; background: #334155; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: rgba(255,255,255,0.1); }
        
        .product-info { padding: 1.5rem; }
        .product-cat { font-size: 0.75rem; color: var(--primary); text-transform: uppercase; font-weight: 700; margin-bottom: 0.5rem; }
        .product-name { font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; }
        .product-price { font-size: 1.25rem; font-weight: 700; color: white; }
        
        .btn-add-cart { width: 100%; margin-top: 1rem; padding: 0.75rem; border-radius: 0.5rem; border: none; background: rgba(255,255,255,0.05); color: white; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .btn-add-cart:hover { background: var(--primary); }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="logo"><i class="fas fa-music"></i> Melody Masters</a>
        <div class="nav-links">
            <a href="#">Shop</a>
            <a href="#">Categories</a>
            <a href="#">About</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span style="color: white">Hello, <strong><?php echo $_SESSION['full_name']; ?></strong></span>
                <?php if ($_SESSION['role'] == 'admin'): ?>
                    <a href="admin/dashboard.php" style="color: var(--primary)">Admin Panel</a>
                <?php endif; ?>
                <a href="public/logout.php">Logout</a>
            <?php else: ?>
                <a href="public/login.php" class="btn-login">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero">
        <h1>Unleash Your Musical Potential</h1>
        <p>Discover a curated collection of premium instruments for every musician, from beginners to professionals.</p>
    </div>

    <div class="container">
        <h2 class="section-title"><i class="fas fa-star" style="color: #f59e0b"></i> Featured Instruments</h2>
        
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
            <div class="product-card">
                <div class="product-img">
                    <i class="fas fa-guitar"></i>
                </div>
                <div class="product-info">
                    <div class="product-cat"><?php echo $p['category_name']; ?></div>
                    <div class="product-name"><?php echo $p['product_name']; ?></div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="product-price">£<?php echo number_format($p['price'], 2); ?></div>
                        <div style="font-size: 0.875rem; color: var(--text-muted)"><?php echo $p['stock']; ?> in stock</div>
                    </div>
                    <button class="btn-add-cart">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($products)): ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 4rem; color: var(--text-muted);">
                    <i class="fas fa-box-open" style="font-size: 3rem; margin-bottom: 1rem; display: block;"></i>
                    No products available yet. Admin can add them from the dashboard.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
