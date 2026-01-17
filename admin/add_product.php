<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit();
}

$message = '';
$error = '';

// Fetch categories for the dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $type = $_POST['product_type'];
    $desc = $_POST['description'];
    
    // Handle Image Upload
    $image_name = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $target_dir = "../assets/images/products/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
        $image_name = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $image_name;
        
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
            // Success
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (empty($error)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (product_name, category_id, brand, price, stock, product_type, description, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $category_id, $brand, $price, $stock, $type, $desc, $image_name]);
            header('Location: inventory.php?success=1');
            exit();
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Melody Masters</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #6366f1;
            --bg: #0f172a;
            --card-bg: #1e293b;
            --sidebar-bg: #020617;
            --text: #f8fafc;
            --text-muted: #94a3b8;
            --danger: #ef4444;
            --success: #10b981;
        }

        body { font-family: 'Inter', sans-serif; background-color: var(--bg); color: var(--text); margin: 0; display: flex; }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); height: 100vh; position: fixed; border-right: 1px solid rgba(255, 255, 255, 0.1); display: flex; flex-direction: column; }
        .sidebar-header { padding: 2rem; font-size: 1.5rem; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 0.75rem; }
        .sidebar-nav { flex: 1; padding: 1rem; }
        .nav-item { display: flex; align-items: center; gap: 1rem; padding: 0.875rem 1rem; color: var(--text-muted); text-decoration: none; border-radius: 0.5rem; transition: all 0.2s; margin-bottom: 0.5rem; }
        .nav-item:hover, .nav-item.active { background-color: rgba(99, 102, 241, 0.1); color: var(--text); }
        .nav-item.active { background-color: var(--primary); color: white; }

        .main-content { margin-left: 260px; flex: 1; padding: 2rem; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }

        .card { background-color: var(--card-bg); border-radius: 1rem; border: 1px solid rgba(255, 255, 255, 0.1); padding: 2rem; max-width: 800px; margin: 0 auto; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-size: 0.875rem; color: var(--text-muted); }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white; box-sizing: border-box; font-family: inherit; }
        .form-group input:focus { outline: none; border-color: var(--primary); }
        
        .btn { padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; font-family: inherit; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
        
        .alert { padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; }
        .alert-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header"><i class="fas fa-music"></i><span>Melody Masters</span></div>
        <div class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-chart-pie"></i><span>Dashboard</span></a>
            <a href="inventory.php" class="nav-item active"><i class="fas fa-boxes"></i><span>Inventory</span></a>
            <a href="orders.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span>Orders</span></a>
            <a href="users.php" class="nav-item"><i class="fas fa-users"></i><span>Users</span></a>
            <a href="../public/logout.php" class="nav-item" style="margin-top: auto;"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Add New Product</h1>
            <a href="inventory.php" class="btn" style="color: var(--text-muted)"><i class="fas fa-arrow-left"></i> Back to Inventory</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" required placeholder="e.g. Fender Stratocaster">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" placeholder="e.g. Fender">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Price (£)</label>
                        <input type="number" step="0.01" name="price" required placeholder="0.00">
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" required placeholder="0">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Product Type</label>
                        <select name="product_type" required>
                            <option value="physical">Physical Product</option>
                            <option value="digital">Digital Download</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Image</label>
                        <input type="file" name="product_image" accept="image/*">
                    </div>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Enter product details..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                    <button type="reset" class="btn" style="background: rgba(255,255,255,0.05); color: white;">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
