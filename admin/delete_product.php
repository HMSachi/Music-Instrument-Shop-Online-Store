<?php
require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/product_manager.php';

require_admin();

$base = BASE_PATH;
$pm = new ProductManager($db);

if (!isset($_GET['id'])) {
    header('Location: ' . $base . '/admin/dashboard.php');
    exit();
}

$product_id = (int) $_GET['id'];
$result = $pm->delete_product($product_id);

if ($result['success']) {
    header('Location: ' . $base . '/admin/dashboard.php?msg=deleted');
} else {
    header('Location: ' . $base . '/admin/dashboard.php?error=' . urlencode($result['message']));
}
exit();
?>
