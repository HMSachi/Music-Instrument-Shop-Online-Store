<?php
require 'config/database.php';

$sql = "ALTER TABLE orders ADD COLUMN tracking_number VARCHAR(100) DEFAULT NULL AFTER shipping_cost;";

if ($conn->query($sql) === TRUE) {
    echo "Column tracking_number added to orders table successfully.\n";
} else {
    // If it already exists, output that logic
    echo "Query result: " . $conn->error . "\n";
}
?>
