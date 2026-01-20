<?php
/**
 * Product Management Handler
 * Manages CRUD operations for products
 */

include 'db_connection.php';

class ProductManager {
    private $db;
    private $upload_dir = '/Music-Instrument-Shop-Online-Store/assets/images/products/';
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Get all products with optional filters
     */
    public function get_products($category_id = null, $search = null) {
        $query = "SELECT p.product_id, p.product_name, p.brand, p.description, p.price, p.stock, p.product_type, p.image, p.category_id, c.category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.category_id 
                  WHERE 1=1";
        
        if ($category_id) {
            $category_id = (int) $category_id;
            $query .= " AND p.category_id = $category_id";
        }
        
        if ($search) {
            $search = sanitize_input($search);
            $query .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%' OR p.brand LIKE '%$search%')";
        }
        
        $query .= " ORDER BY p.created_at DESC";
        
        $result = $this->db->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Get product by ID
     */
    public function get_product($product_id) {
        $product_id = (int) $product_id;
        $result = $this->db->query("SELECT p.product_id, p.product_name, p.brand, p.description, p.price, p.stock, p.product_type, p.image, p.category_id, c.category_name 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.category_id 
                                    WHERE p.product_id = $product_id");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    /**
     * Add new product
     */
    public function add_product($data) {
        // Validate required fields
        $required = ['product_name', 'price', 'stock', 'description', 'product_type'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                return ['success' => false, 'message' => "Field '$field' is required"];
            }
        }
        
        // Sanitize inputs
        $product_name = sanitize_input($data['product_name']);
        $brand = isset($data['brand']) ? sanitize_input($data['brand']) : null;
        $category_id = isset($data['category_id']) ? (int)$data['category_id'] : 'NULL';
        $price = (float)$data['price'];
        $stock = (int)$data['stock'];
        $description = sanitize_input($data['description']);
        $product_type = sanitize_input($data['product_type']);
        $image = isset($data['image']) ? sanitize_input($data['image']) : null;
        
        // Validate price and stock
        if ($price < 0) {
            return ['success' => false, 'message' => 'Price cannot be negative'];
        }
        
        if ($stock < 0) {
            return ['success' => false, 'message' => 'Stock cannot be negative'];
        }
        
        // Insert product
        $query = "INSERT INTO products (product_name, brand, category_id, price, stock, description, product_type, image) 
                  VALUES ('$product_name', " . ($brand ? "'$brand'" : "NULL") . ", " . ($category_id === 'NULL' ? "NULL" : $category_id) . ", $price, $stock, '$description', '$product_type', " . ($image ? "'$image'" : "NULL") . ")";
        
        if ($this->db->query($query)) {
            $product_id = $this->db->insert_id;
            return ['success' => true, 'message' => 'Product added successfully', 'product_id' => $product_id];
        } else {
            return ['success' => false, 'message' => 'Failed to add product: ' . $this->db->error];
        }
    }
    
    /**
     * Update product
     */
    public function update_product($product_id, $data) {
        $product_id = (int) $product_id;
        $updates = [];
        
        if (isset($data['product_name']) && $data['product_name'] !== '') {
            $product_name = sanitize_input($data['product_name']);
            $updates[] = "product_name = '$product_name'";
        }
        
        if (isset($data['brand'])) {
            $brand = sanitize_input($data['brand']);
            $updates[] = "brand = '$brand'";
        }
        
        if (isset($data['price'])) {
            $price = (float)$data['price'];
            if ($price < 0) {
                return ['success' => false, 'message' => 'Price cannot be negative'];
            }
            $updates[] = "price = $price";
        }
        
        if (isset($data['stock'])) {
            $stock = (int)$data['stock'];
            if ($stock < 0) {
                return ['success' => false, 'message' => 'Stock cannot be negative'];
            }
            $updates[] = "stock = $stock";
        }
        
        if (isset($data['description']) && $data['description'] !== '') {
            $description = sanitize_input($data['description']);
            $updates[] = "description = '$description'";
        }
        
        if (isset($data['category_id'])) {
            $category_id = (int)$data['category_id'];
            $updates[] = "category_id = $category_id";
        }
        
        if (isset($data['product_type'])) {
            $product_type = sanitize_input($data['product_type']);
            $updates[] = "product_type = '$product_type'";
        }
        
        if (isset($data['image']) && $data['image'] !== '') {
            $image = sanitize_input($data['image']);
            $updates[] = "image = '$image'";
        }
        
        if (empty($updates)) {
            return ['success' => false, 'message' => 'No fields to update'];
        }
        
        $query = "UPDATE products SET " . implode(", ", $updates) . " WHERE product_id = $product_id";
        
        if ($this->db->query($query)) {
            return ['success' => true, 'message' => 'Product updated successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to update product: ' . $this->db->error];
        }
    }
    
    /**
     * Delete product (hard delete)
     */
    public function delete_product($product_id) {
        $product_id = (int) $product_id;
        $query = "DELETE FROM products WHERE product_id = $product_id";
        
        if ($this->db->query($query)) {
            return ['success' => true, 'message' => 'Product deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Failed to delete product'];
        }
    }
    
    /**
     * Get all categories
     */
    public function get_categories() {
        $result = $this->db->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Get product reviews
     */
    public function get_product_reviews($product_id) {
        $product_id = (int) $product_id;
        $query = "SELECT r.review_id, r.rating, r.comment, r.review_date, u.full_name AS user_name 
                  FROM reviews r 
                  JOIN users u ON r.user_id = u.user_id 
                  WHERE r.product_id = $product_id 
                  ORDER BY r.review_date DESC";
        
        $result = $this->db->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Get average rating
     */
    public function get_average_rating($product_id) {
        $product_id = (int) $product_id;
        $result = $this->db->query("SELECT COALESCE(AVG(rating),0) as avg_rating, COUNT(*) as total_reviews 
                                    FROM reviews WHERE product_id = $product_id");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return ['avg_rating' => 0, 'total_reviews' => 0];
    }
}

?>
