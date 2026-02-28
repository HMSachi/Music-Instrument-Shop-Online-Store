<?php
require 'config/database.php';

$sql = "CREATE TABLE IF NOT EXISTS order_downloads (
    download_id INT AUTO_INCREMENT PRIMARY KEY,
    order_item_id INT NOT NULL,
    download_count INT DEFAULT 0,
    last_download TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_item_id) REFERENCES order_items(order_item_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($sql) === TRUE) {
    echo "Table order_downloads created successfully\n";
} else {
    echo "Error creating table: " . $conn->error . "\n";
}
?>
