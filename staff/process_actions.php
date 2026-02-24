<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Only staff and admins allowed
requireLogin();
requireStaff();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Security validation failed. Please try again.";
        redirect('staff/dashboard.php');
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'update_stock') {
        $product_id = intval($_POST['product_id']);
        $new_stock = intval($_POST['stock']);

        $sql = "UPDATE products SET stock = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $new_stock, $product_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Stock updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating stock.";
        }
    } 
    elseif ($action === 'update_order') {
        $order_id = intval($_POST['order_id']);
        $status = $_POST['status'] ?? '';
        $tracking = $_POST['tracking_number'] ?? '';

        $sql = "UPDATE orders SET order_status = ?, tracking_number = ? WHERE order_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $tracking, $order_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Order #$order_id updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating order.";
        }
    }

    redirect('staff/dashboard.php');
} else {
    redirect('staff/dashboard.php');
}
?>
