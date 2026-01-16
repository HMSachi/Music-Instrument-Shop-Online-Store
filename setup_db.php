<?php
require_once 'config/db_connect.php';

// Use the database name from db_connect.php
$host = 'localhost';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// We need to connect without a DB first to create it
$dsn = "mysql:host=$host;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo_setup = new PDO($dsn, $user, $pass, $options);
    
    // Create Database (using the name from db_connect.php)
    $pdo_setup->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo_setup->exec("USE `$db`");
    
    echo "Database `$db` created or already exists.<br>";

    // 1. Users Table
    $pdo_setup->exec("CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(100),
        email VARCHAR(100) UNIQUE,
        password VARCHAR(255),
        role ENUM('customer', 'staff', 'admin') DEFAULT 'customer',
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Categories Table
    $pdo_setup->exec("CREATE TABLE IF NOT EXISTS categories (
        category_id INT AUTO_INCREMENT PRIMARY KEY,
        category_name VARCHAR(100),
        parent_id INT DEFAULT NULL,
        FOREIGN KEY (parent_id) REFERENCES categories(category_id)
    )");

    // 3. Products Table
    $pdo_setup->exec("CREATE TABLE IF NOT EXISTS products (
        product_id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT,
        product_name VARCHAR(150),
        brand VARCHAR(100),
        description TEXT,
        price DECIMAL(10,2),
        stock INT,
        product_type ENUM('physical', 'digital'),
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(category_id)
    )");

    // 4. Digital Products Table
    $pdo_setup->exec("CREATE TABLE IF NOT EXISTS digital_products (
        digital_id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT,
        file_path VARCHAR(255),
        download_limit INT DEFAULT 3,
        FOREIGN KEY (product_id) REFERENCES products(product_id)
    )");

    // 5. Orders Table
    $pdo_setup->exec("CREATE TABLE IF NOT EXISTS orders (
        order_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total_amount DECIMAL(10,2),
        shipping_cost DECIMAL(10,2),
        order_status VARCHAR(50) DEFAULT 'Pending',
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )");

    // 6. Order Items Table
    $pdo_setup->exec("CREATE TABLE IF NOT EXISTS order_items (
        order_item_id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT,
        price DECIMAL(10,2),
        FOREIGN KEY (order_id) REFERENCES orders(order_id),
        FOREIGN KEY (product_id) REFERENCES products(product_id)
    )");

    // 7. Reviews Table
    $pdo_setup->exec("CREATE TABLE IF NOT EXISTS reviews (
        review_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        rating INT CHECK (rating BETWEEN 1 AND 5),
        comment TEXT,
        review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id),
        FOREIGN KEY (product_id) REFERENCES products(product_id)
    )");

    echo "All tables created successfully.<br>";

    // Seed initial categories if empty
    $count = $pdo_setup->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count == 0) {
        $categories = ['Guitars', 'Keyboards', 'Drums', 'Accessories', 'Digital Sheet Music'];
        $stmt = $pdo_setup->prepare("INSERT INTO categories (category_name) VALUES (?)");
        foreach ($categories as $cat) {
            $stmt->execute([$cat]);
        }
        echo "Default categories seeded.<br>";
    }

    echo "<strong>Setup complete!</strong> You can now use the application.";

} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
