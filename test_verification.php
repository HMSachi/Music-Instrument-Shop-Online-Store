<?php
require 'config/database.php';
require 'config/config.php';

// Test 1: Can an unauthenticated user submit a review? (Handled by isLoggedIn in product.php)
$_SESSION['user_id'] = 3; // Using Sample Customer User 'John Doe' who purchased product 1 (Delivered).

$user_id = 3;
$purchased_product_id = 1; // Order 1 is Delivered
$unpurchased_product_id = 2; // Not in any order

// Helper function to test verification logic
function testReviewVerification($conn, $user_id, $product_id, $expected_result) {
    $purchase_sql = "SELECT oi.product_id FROM order_items oi 
                     JOIN orders o ON oi.order_id = o.order_id 
                     WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = 'Delivered'";
    
    $stmt = $conn->prepare($purchase_sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $can_review = ($result && $result->num_rows > 0);
    
    $pass = ($can_review === $expected_result);
    echo "Test for User $user_id, Product $product_id: " . ($pass ? "PASS" : "FAIL") . " (Expected: " . ($expected_result ? 'Yes' : 'No') . ", Got: " . ($can_review ? 'Yes' : 'No') . ")\n";
    return $pass;
}

testReviewVerification($conn, $user_id, $purchased_product_id, true);
testReviewVerification($conn, $user_id, $unpurchased_product_id, false);

// Let's create a pending order for product 3
$conn->query("INSERT INTO orders (user_id, total_amount, order_status) VALUES (3, 35000.00, 'Pending')");
$pending_order_id = $conn->insert_id;
$conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($pending_order_id, 3, 1, 35000.00)");

testReviewVerification($conn, $user_id, 3, false); // Product 3 is Pending, so should be false

// Clean up
$conn->query("DELETE FROM orders WHERE order_id = $pending_order_id");

echo "Verification tests completed.\n";
?>
