<?php
require_once 'config/database.php';

try {
    $conn->query("ALTER TABLE orders ADD COLUMN shipping_address TEXT AFTER total_amount");
    echo "Added shipping_address column.\n";
} catch (Exception $e) {
    echo "shipping_address error or already exists.\n";
}

try {
    $conn->query("ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) AFTER shipping_address");
    echo "Added payment_method column.\n";
} catch (Exception $e) {
    echo "payment_method error or already exists.\n";
}

echo "Migration complete.\n";
?>
