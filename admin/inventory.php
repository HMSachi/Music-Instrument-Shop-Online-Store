<?php
session_start();
require_once '../config/db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../public/login.php');
    exit();
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$id]);
    header('Location: inventory.php?deleted=1');
    exit();
}

// Handle Add/Edit Product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $brand = $_POST['brand'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $type = $_POST['product_type'];
    $desc = $_POST['description'];

    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
        // Edit
        $id = $_POST['product_id'];
        $stmt = $pdo->prepare("UPDATE products SET product_name=?, category_id=?, brand=?, price=?, stock=?, product_type=?, description=? WHERE product_id=?");
        $stmt->execute([$name, $category_id, $brand, $price, $stock, $type, $desc, $id]);
        $msg = "updated=1";
    } else {
        // Add
        $stmt = $pdo->prepare("INSERT INTO products (product_name, category_id, brand, price, stock, product_type, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category_id, $brand, $price, $stock, $type, $desc]);
        $msg = "success=1";
    }
    header("Location: inventory.php?$msg");
    exit();
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Fetch products
$products = $pdo->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Melody Masters</title>
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

        .btn { padding: 0.625rem 1.25rem; border-radius: 0.5rem; font-weight: 600; cursor: pointer; border: none; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background-color: var(--primary); color: white; }
        .btn-danger { background-color: var(--danger); color: white; }
        .btn-icon { padding: 0.4rem; border-radius: 0.4rem; background: rgba(255,255,255,0.05); color: white; }
        .btn-icon:hover { background: rgba(255,255,255,0.1); }

        .card { background-color: var(--card-bg); border-radius: 1rem; border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 2rem; }
        .card-header { padding: 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); font-weight: 600; }

        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 1rem 1.5rem; background-color: rgba(255, 255, 255, 0.02); font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); }
        td { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05); }

        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: var(--card-bg); padding: 2rem; border-radius: 1rem; width: 100%; max-width: 500px; border: 1px solid rgba(255,255,255,0.1); }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-size: 0.875rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: white; box-sizing: border-box; }
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
            <h1>Inventory Management</h1>
            <button class="btn btn-primary" onclick="openModal()">
                <i class="fas fa-plus"></i> Add Product
            </button>
        </div>

        <div class="card">
            <div class="card-header">Product List</div>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td><strong><?php echo $p['product_name']; ?></strong><br><small style="color: var(--text-muted)"><?php echo $p['brand']; ?></small></td>
                        <td><?php echo $p['category_name']; ?></td>
                        <td>£<?php echo number_format($p['price'], 2); ?></td>
                        <td><?php echo $p['stock']; ?></td>
                        <td><span style="text-transform: capitalize"><?php echo $p['product_type']; ?></span></td>
                        <td>
                            <button class="btn-icon" onclick='editProduct(<?php echo json_encode($p); ?>)' title="Edit"><i class="fas fa-edit"></i></button>
                            <a href="inventory.php?delete=<?php echo $p['product_id']; ?>" class="btn-icon" style="color: var(--danger)" onclick="return confirm('Are you sure?')" title="Delete"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle" style="margin-top: 0">Add New Product</h2>
            <form method="POST">
                <input type="hidden" name="product_id" id="product_id">
                <div class="form-group">
                    <label>Product Name</label>
                    <input type="text" name="product_name" id="product_name" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id" id="category_id">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Brand</label>
                        <input type="text" name="brand" id="brand">
                    </div>
                    <div class="form-group">
                        <label>Price (£)</label>
                        <input type="number" step="0.01" name="price" id="price" required>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Stock</label>
                        <input type="number" name="stock" id="stock" required>
                    </div>
                    <div class="form-group">
                        <label>Product Type</label>
                        <select name="product_type" id="product_type">
                            <option value="physical">Physical</option>
                            <option value="digital">Digital</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="description" rows="3"></textarea>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem;">
                    <button type="button" class="btn" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('productModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = 'Add New Product';
            document.getElementById('product_id').value = '';
            document.querySelector('form').reset();
        }

        function closeModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        function editProduct(p) {
            document.getElementById('productModal').style.display = 'flex';
            document.getElementById('modalTitle').innerText = 'Edit Product';
            document.getElementById('product_id').value = p.product_id;
            document.getElementById('product_name').value = p.product_name;
            document.getElementById('category_id').value = p.category_id;
            document.getElementById('brand').value = p.brand;
            document.getElementById('price').value = p.price;
            document.getElementById('stock').value = p.stock;
            document.getElementById('product_type').value = p.product_type;
            document.getElementById('description').value = p.description;
        }
    </script>
</body>
</html>
