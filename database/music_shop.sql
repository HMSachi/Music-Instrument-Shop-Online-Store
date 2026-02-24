-- Melody Masters Database Schema
-- Music Instrument Shop Online Store

-- Create Database
CREATE DATABASE IF NOT EXISTS melody_masters;
USE melody_masters;

-- 1. Users Table
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'staff', 'admin') DEFAULT 'customer',
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Categories Table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(category_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Products Table
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    product_name VARCHAR(150) NOT NULL,
    brand VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    product_type ENUM('physical', 'digital') DEFAULT 'physical',
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL,
    INDEX idx_category (category_id),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Digital Products Table
CREATE TABLE digital_products (
    digital_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    file_path VARCHAR(255),
    download_limit INT DEFAULT 3,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Orders Table
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_cost DECIMAL(10,2) DEFAULT 0.00,
    order_status VARCHAR(50) DEFAULT 'Pending',
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (order_status),
    INDEX idx_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Order Items Table
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Reviews Table
CREATE TABLE reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_product (product_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Sample Categories
INSERT INTO categories (category_name, parent_id) VALUES 
('Guitars', NULL),
('Keyboards', NULL),
('Drums', NULL),
('Accessories', NULL),
('Digital Sheet Music', NULL);

-- Insert Sample Admin User (password: admin123)
INSERT INTO users (full_name, email, password, role, phone, address) VALUES
('Admin User', 'admin@melodymaster.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '09123456789', 'Manila, Philippines');

-- Insert Sample Staff User (password: staff123)
INSERT INTO users (full_name, email, password, role, phone, address) VALUES
('Staff Member', 'staff@melodymaster.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'staff', '09987654321', 'Quezon City, Philippines');

-- Insert Sample Customer User (password: customer123)
INSERT INTO users (full_name, email, password, role, phone, address) VALUES
('John Doe', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '09111222333', 'Makati, Philippines');

-- Insert Sample Products
INSERT INTO products (category_id, product_name, brand, description, price, stock, product_type, image) VALUES
(1, 'Acoustic Guitar', 'Yamaha', 'Professional acoustic guitar with rich sound quality. Perfect for beginners and professionals alike.', 15000.00, 10, 'physical', 'assets/images/guitars2.jpg'),
(1, 'Electric Guitar', 'Fender', 'Classic electric guitar with amazing tone and playability.', 25000.00, 8, 'physical', 'assets/images/guitars1.png'),
(2, 'Digital Piano', 'Casio', '88-key digital piano with weighted keys and realistic sound.', 35000.00, 5, 'physical', 'assets/images/piano.jpg'),
(2, 'Keyboard Synthesizer', 'Roland', 'Professional synthesizer with hundreds of sounds and effects.', 45000.00, 3, 'physical', 'assets/images/keyboard.jpg'),
(3, 'Drum Set', 'Pearl', 'Complete 5-piece drum set ideal for beginners and intermediate players.', 25000.00, 3, 'physical', 'assets/images/drums.jpg'),
(3, 'Electronic Drum Kit', 'Alesis', 'Compact electronic drum kit with mesh heads and built-in sounds.', 30000.00, 4, 'physical', 'assets/images/e-drums.jpg'),
(4, 'Guitar Strings', 'Ernie Ball', 'Premium nickel wound guitar strings for excellent tone.', 500.00, 50, 'physical', 'assets/images/strings.jpg'),
(4, 'Guitar Capo', 'Kyser', 'Quick-change guitar capo for easy key changes.', 800.00, 30, 'physical', 'assets/images/capo.jpg'),
(4, 'Guitar Picks Set', 'Dunlop', 'Variety pack of guitar picks in different thickness.', 250.00, 100, 'physical', 'assets/images/picks.jpg'),
(4, 'Microphone', 'Shure', 'Professional dynamic microphone for vocals and instruments.', 8000.00, 15, 'physical', 'assets/images/microphone.jpg'),
(5, 'Classical Music Sheet', 'Various', 'Digital sheet music collection of classical pieces.', 500.00, 999, 'digital', 'assets/images/sheet-music.jpg'),
(5, 'Rock Songs Collection', 'Various', 'Popular rock songs sheet music bundle.', 750.00, 999, 'digital', 'assets/images/rock-sheets.jpg');

-- Insert Sample Reviews
INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(3, 1, 5, 'Amazing guitar! Great sound quality and value for money.'),
(3, 2, 4, 'Good electric guitar, but could use better pickups.'),
(3, 3, 5, 'Perfect for learning piano. Keys feel realistic!'),
(3, 5, 4, 'Great drum set for the price. Setup was easy.');

-- Insert Sample Order
INSERT INTO orders (user_id, total_amount, shipping_cost, order_status) VALUES
(3, 15500.00, 100.00, 'Delivered');

-- Insert Sample Order Items
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 15000.00),
(1, 7, 1, 500.00);

-- Display Table Information
SELECT 'Database created successfully!' as Status;
SELECT COUNT(*) as 'Total Categories' FROM categories;
SELECT COUNT(*) as 'Total Products' FROM products;
SELECT COUNT(*) as 'Total Users' FROM users;
