<?php
require_once 'config/config.php';
require_once 'config/database.php';

requireLogin();

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$product_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// 1. Ownership & Item Retrieval
$own_sql = "SELECT oi.order_item_id, p.product_name, dp.download_limit, dp.file_path 
            FROM order_items oi 
            JOIN orders o ON oi.order_id = o.order_id 
            JOIN products p ON oi.product_id = p.product_id
            LEFT JOIN digital_products dp ON p.product_id = dp.product_id
            WHERE o.user_id = ? AND oi.product_id = ? 
            LIMIT 1";

$own_result = preparedQuery($conn, $own_sql, [$user_id, $product_id], "ii");

if (!$own_result || $own_result->num_rows === 0) {
    die("You do not have access to this download.");
}

$data = $own_result->fetch_assoc();
$order_item_id = $data['order_item_id'];
$download_limit = $data['download_limit'] ?? 3;
$file_path = $data['file_path'];

// 2. Download Count Tracking
$track_sql = "SELECT * FROM order_downloads WHERE order_item_id = ?";
$track_result = preparedQuery($conn, $track_sql, [$order_item_id], "i");

if (!$track_result || $track_result->num_rows === 0) {
    // Initialize record
    preparedQuery($conn, "INSERT INTO order_downloads (order_item_id, download_count) VALUES (?, 0)", [$order_item_id], "i");
    $current_count = 0;
} else {
    $track = $track_result->fetch_assoc();
    $current_count = $track['download_count'];
}

// 3. Limit Enforcement
if ($current_count >= $download_limit) {
    die("Download limit reached for this purchase. (Limit: $download_limit)");
}

// 4. File Preparation (Same logic as before, ensuring file existence)
if (empty($file_path)) {
    $file_path = "assets/downloads/demo_sheet_music.pdf";
    if (!file_exists($file_path)) {
        if (!is_dir('assets/downloads')) mkdir('assets/downloads', 0777, true);
        file_put_contents($file_path, "Demo Digital Product Content for Product: " . $data['product_name']);
    }
}

if (!file_exists($file_path)) {
    die("File not found on server.");
}

// 5. Success: Increment Count and Serve File
$new_count = $current_count + 1;
preparedQuery($conn, "UPDATE order_downloads SET download_count = ?, last_download = CURRENT_TIMESTAMP WHERE order_item_id = ?", [$new_count, $order_item_id], "ii");

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;
?>
