-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 11:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `melody_masters`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `parent_id`) VALUES
(1, 'Guitars', NULL),
(2, 'Keyboards', NULL),
(3, 'Drums and Percussion', NULL),
(4, 'Accessories', NULL),
(5, 'Digital Sheet Music', NULL),
(16, 'Wind Instruments', NULL),
(17, 'String Instruments', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `digital_products`
--

CREATE TABLE `digital_products` (
  `digital_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `download_limit` int(11) DEFAULT 3
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `digital_products`
--

INSERT INTO `digital_products` (`digital_id`, `product_id`, `file_path`, `download_limit`) VALUES
(1, 6, '/downloads/theory_course.zip', 3);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT NULL,
  `order_status` varchar(50) DEFAULT 'Pending',
  `tracking_number` varchar(100) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `shipping_address`, `payment_method`, `shipping_cost`, `order_status`, `tracking_number`, `order_date`) VALUES
(1, 9, 2999.97, NULL, NULL, 0.00, 'Processing', NULL, '2026-02-24 13:18:42'),
(2, 13, 378.99, NULL, NULL, 0.00, 'Processing', NULL, '2026-02-28 06:34:32'),
(4, 14, 499.98, 'hi', 'cod', 0.00, 'Processing', NULL, '2026-02-28 07:41:22');

-- --------------------------------------------------------

--
-- Table structure for table `order_downloads`
--

CREATE TABLE `order_downloads` (
  `order_download_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `download_count` int(11) DEFAULT 0,
  `last_download` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_downloads`
--

INSERT INTO `order_downloads` (`order_download_id`, `order_item_id`, `download_count`, `last_download`) VALUES
(1, 3, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 599.99),
(2, 1, 3, 1, 349.99),
(3, 1, 6, 1, 49.99),
(4, 1, 7, 2, 1000.00),
(5, 2, 5, 1, 79.99),
(6, 2, 9, 1, 159.00),
(7, 2, 10, 1, 140.00),
(9, 4, 1, 1, 199.99),
(10, 4, 4, 1, 299.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `product_name` varchar(150) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `product_type` enum('physical','digital') DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `brand`, `description`, `price`, `stock`, `product_type`, `image`, `created_at`) VALUES
(1, 1, 'Fender Acoustic Guitar', 'Fender', 'High-quality acoustic guitar perfect for all levels', 199.99, 14, 'physical', 'assets/images/guitars1.png', '2026-01-20 13:55:40'),
(2, 1, 'Electric Guitar - Stratocaster', 'Fender', 'Classic electric guitar with versatile sound', 599.99, 7, 'physical', 'assets/images/guitars2.jpg', '2026-01-20 13:55:40'),
(3, 2, 'Digital Piano 88 Keys', 'Yamaha', 'Professional digital piano with weighted keys', 349.99, 11, 'physical', 'assets/images/Digital Piano 88 Keys.jpg', '2026-01-20 13:55:40'),
(4, 3, 'Drum Kit 5-Piece Professional', 'Pearl', 'Complete drum kit with cymbals and stands', 299.99, 5, 'physical', 'assets/images/Drum Kit 5-Piece.jpg', '2026-01-20 13:55:40'),
(5, 4, 'Digital Piano 88 Keys (Special Edition)', 'Audio-Technica', 'Condenser microphone with shock mount and pop filter', 79.99, 19, 'physical', 'assets/images/Digital Piano 88 Keys1.webp', '2026-01-20 13:55:40'),
(6, 5, 'Theory Course (Digital)', 'Melody Masters', 'Comprehensive digital music theory course with PDFs and audio', 49.99, 999, 'digital', 'assets/images/Theory Course (Digital).jpg', '2026-01-20 13:55:40'),
(7, 4, 'Sepina Premium Keyboard', 'ap', 'serpin', 1000.00, -1, 'physical', 'assets/images/Sepina.jpg', '2026-01-20 15:45:20'),
(8, 4, 'Drum Sticks', 'Melody Master', '', 1200.00, 4, 'physical', 'assets/images/Drum Sticks.jpg', '2026-02-24 15:56:27'),
(9, 4, 'Guitar Picks', 'Melody Master', '', 159.00, 29, 'physical', 'assets/images/Guitar Picks.jpg', '2026-02-24 15:58:30'),
(10, 5, 'Guitar Tabs', 'Melody Master', '', 140.00, 9, 'physical', 'assets/images/Guitar Tabs.webp', '2026-02-24 16:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('customer','staff','admin') DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `email`, `password`, `role`, `phone`, `address`, `created_at`) VALUES
(9, 'sachini', 's@gmail.com', '$2y$10$PHgSCMY63XoLuHWu4jSJwunN32h2flDynDXKGWk808q/8pBxhW7He', 'customer', '+94717299419', 'kk', '2026-02-24 10:50:26'),
(10, 'Admin User', 'admin@melodymaster.com', '$2y$10$jldWdu3j4Mj1113VkrXd.en6NuV4AbzlB3O/DLRfQaJZcLSOo7lXe', 'admin', '09123456789', 'Manila, Philippines', '2026-02-24 15:51:17'),
(11, 'Staff Member', 'staff@melodymaster.com', '$2y$10$mBCKkfYAWnsAYDV5TZw4Vub3DCNE30DjayhsrXlIS1r13IR78NeWW', 'staff', '09987654321', 'Quezon City, Philippines', '2026-02-24 15:51:17'),
(12, 'John Doe', 'customer@example.com', '$2y$10$pXhOarGcDS0OecFEE/pN.er0wJhAx5VWRAuUQuqX1pHoykBcDZjSi', 'customer', '09111222333', 'Makati, Philippines', '2026-02-24 15:51:17'),
(13, 'Sachini', 'sach@gmail.com', '$2y$10$GQE6iiUwcijRwUUfWH8hXeoWBan82XAsaPFhvQpjVpmdIG1K5vBO6', 'admin', '0717299419', '', '2026-02-28 06:30:29'),
(14, 'Sachini', 'sa@gmail.com', '$2y$10$Taxt3uMHqtlU7vct.D3g7.2WJPUnLWtUYu8QRj0xWgx0YRtHrppce', 'customer', '+94717299419', '', '2026-02-28 07:36:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `digital_products`
--
ALTER TABLE `digital_products`
  ADD PRIMARY KEY (`digital_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_downloads`
--
ALTER TABLE `order_downloads`
  ADD PRIMARY KEY (`order_download_id`),
  ADD KEY `order_item_id` (`order_item_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `digital_products`
--
ALTER TABLE `digital_products`
  MODIFY `digital_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_downloads`
--
ALTER TABLE `order_downloads`
  MODIFY `order_download_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `digital_products`
--
ALTER TABLE `digital_products`
  ADD CONSTRAINT `digital_products_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_downloads`
--
ALTER TABLE `order_downloads`
  ADD CONSTRAINT `order_downloads_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`order_item_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
