<?php
/**
 * Order Management Handler
 * Manages orders and shopping cart
 */

include 'db_connection.php';

class OrderManager {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get shopping cart from session
     */
    public function get_cart() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    }
    
    /**
     * Add item to cart
     */
    public function add_to_cart($product_id, $quantity = 1) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Check if product exists and is in stock
        $product = $this->db->query("SELECT id, stock, price, type FROM products WHERE id = $product_id AND is_active = TRUE");
        
        if (!$product || $product->num_rows === 0) {
            return ['success' => false, 'message' => 'Product not found'];
        }
        
        $product_data = $product->fetch_assoc();
        
        if ($product_data['stock'] < $quantity && $product_data['type'] === 'Physical') {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }
        
        // Add or update cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'product_id' => $product_id,
                'quantity' => $quantity
            ];
        }
        
        return ['success' => true, 'message' => 'Item added to cart'];
    }
    
    /**
     * Remove item from cart
     */
    public function remove_from_cart($product_id) {
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            return ['success' => true, 'message' => 'Item removed from cart'];
        }
        
        return ['success' => false, 'message' => 'Item not found in cart'];
    }
    
    /**
     * Update cart item quantity
     */
    public function update_cart_quantity($product_id, $quantity) {
        if ($quantity <= 0) {
            return $this->remove_from_cart($product_id);
        }
        
        if (isset($_SESSION['cart'][$product_id])) {
            // Check stock availability
            $product = $this->db->query("SELECT stock, type FROM products WHERE id = $product_id");
            $product_data = $product->fetch_assoc();
            
            if ($product_data['stock'] < $quantity && $product_data['type'] === 'Physical') {
                return ['success' => false, 'message' => 'Insufficient stock'];
            }
            
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            return ['success' => true, 'message' => 'Cart updated'];
        }
        
        return ['success' => false, 'message' => 'Item not found in cart'];
    }
    
    /**
     * Clear cart
     */
    public function clear_cart() {
        $_SESSION['cart'] = [];
        return ['success' => true, 'message' => 'Cart cleared'];
    }
    
    /**
     * Get cart details with product information
     */
    public function get_cart_details() {
        $cart = $this->get_cart();
        $cart_items = [];
        $total = 0;
        $has_physical = false;
        
        foreach ($cart as $product_id => $item) {
            $product = $this->db->query("SELECT id, name, price, type, image FROM products WHERE id = $product_id");
            
            if ($product && $product->num_rows > 0) {
                $product_data = $product->fetch_assoc();
                $item_total = $product_data['price'] * $item['quantity'];
                
                $cart_items[] = [
                    'product_id' => $product_id,
                    'name' => $product_data['name'],
                    'price' => $product_data['price'],
                    'type' => $product_data['type'],
                    'quantity' => $item['quantity'],
                    'item_total' => $item_total,
                    'image' => $product_data['image']
                ];
                
                $total += $item_total;
                
                if ($product_data['type'] === 'Physical') {
                    $has_physical = true;
                }
            }
        }
        
        // Calculate shipping
        $shipping = 0;
        if ($has_physical && $total <= 100) {
            $shipping = 15; // Standard shipping cost
        }
        
        return [
            'items' => $cart_items,
            'subtotal' => $total,
            'shipping' => $shipping,
            'total' => $total + $shipping,
            'item_count' => count($cart_items),
            'has_physical' => $has_physical
        ];
    }
    
    /**
     * Create order from cart
     */
    public function create_order($user_id, $delivery_address, $phone) {
        $cart_details = $this->get_cart_details();
        
        if (empty($cart_details['items'])) {
            return ['success' => false, 'message' => 'Cart is empty'];
        }
        
        // Validate inputs
        if (empty($delivery_address) || empty($phone)) {
            return ['success' => false, 'message' => 'Delivery address and phone are required'];
        }
        
        // Start transaction
        $this->db->begin_transaction();
        
        try {
            // Create order
            $delivery_address = sanitize_input($delivery_address);
            $phone = sanitize_input($phone);
            $total_price = $cart_details['total'];
            $shipping_cost = $cart_details['shipping'];
            
            $order_query = "INSERT INTO orders (user_id, total_price, shipping_cost, delivery_address, phone, status) 
                           VALUES ($user_id, $total_price, $shipping_cost, '$delivery_address', '$phone', 'Pending')";
            
            if (!$this->db->query($order_query)) {
                throw new Exception("Failed to create order");
            }
            
            $order_id = $this->db->insert_id;
            
            // Add order items and update stock
            foreach ($cart_details['items'] as $item) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $price = $item['price'];
                
                // Insert order item
                $item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                              VALUES ($order_id, $product_id, $quantity, $price)";
                
                if (!$this->db->query($item_query)) {
                    throw new Exception("Failed to add order item");
                }
                
                // Update stock for physical products only
                if ($item['type'] === 'Physical') {
                    $stock_query = "UPDATE products SET stock = stock - $quantity WHERE id = $product_id";
                    if (!$this->db->query($stock_query)) {
                        throw new Exception("Failed to update stock");
                    }
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            // Clear cart
            $this->clear_cart();
            
            return ['success' => true, 'message' => 'Order created successfully', 'order_id' => $order_id];
            
        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Get user orders
     */
    public function get_user_orders($user_id) {
        $result = $this->db->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY order_date DESC");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Get order details
     */
    public function get_order($order_id, $user_id = null) {
        $query = "SELECT * FROM orders WHERE id = $order_id";
        
        if ($user_id) {
            $query .= " AND user_id = $user_id";
        }
        
        $result = $this->db->query($query);
        
        if ($result && $result->num_rows > 0) {
            $order = $result->fetch_assoc();
            
            // Get order items
            $items_result = $this->db->query("SELECT oi.*, p.name, p.image FROM order_items oi 
                                              JOIN products p ON oi.product_id = p.id 
                                              WHERE oi.order_id = $order_id");
            
            $order['items'] = [];
            if ($items_result && $items_result->num_rows > 0) {
                $order['items'] = $items_result->fetch_all(MYSQLI_ASSOC);
            }
            
            return $order;
        }
        
        return null;
    }
    
    /**
     * Get all orders (for staff)
     */
    public function get_all_orders($status = null) {
        $query = "SELECT o.*, u.name as customer_name, u.email as customer_email FROM orders o 
                  JOIN users u ON o.user_id = u.id";
        
        if ($status) {
            $status = sanitize_input($status);
            $query .= " WHERE o.status = '$status'";
        }
        
        $query .= " ORDER BY o.order_date DESC";
        
        $result = $this->db->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Update order status
     */
    public function update_order_status($order_id, $status) {
        if (!in_array($status, ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'])) {
            return ['success' => false, 'message' => 'Invalid status'];
        }
        
        $query = "UPDATE orders SET status = '$status' WHERE id = $order_id";
        
        if ($this->db->query($query)) {
            return ['success' => true, 'message' => 'Order status updated'];
        } else {
            return ['success' => false, 'message' => 'Failed to update order status'];
        }
    }
}

?>
