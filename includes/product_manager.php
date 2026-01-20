<?php
/**
 * Product Management
 * CRUD and read helpers for products and categories
 */

require_once __DIR__ . '/db_connection.php';

class ProductManager {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    // Fetch products with optional category filter and search
    public function get_products($category_id = null, $search = null) {
        $sql = "SELECT p.product_id, p.product_name, p.brand, p.description, p.price, p.stock, p.product_type, p.image, p.category_id, c.category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE 1=1";

        if ($category_id) {
            $category_id = (int) $category_id;
            $sql .= " AND p.category_id = $category_id";
        }

        if ($search) {
            $search = $this->db->real_escape_string($search);
            $sql .= " AND (p.product_name LIKE '%$search%' OR p.description LIKE '%$search%' OR p.brand LIKE '%$search%')";
        }

        $sql .= " ORDER BY p.created_at DESC";

        $result = $this->db->query($sql);
        return ($result && $result->num_rows) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Single product
    public function get_product($product_id) {
        $product_id = (int) $product_id;
        $sql = "SELECT p.product_id, p.product_name, p.brand, p.description, p.price, p.stock, p.product_type, p.image, p.category_id, c.category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                WHERE p.product_id = $product_id";
        $result = $this->db->query($sql);
        return ($result && $result->num_rows) ? $result->fetch_assoc() : null;
    }

    // Categories
    public function get_categories() {
        $result = $this->db->query('SELECT category_id, category_name, parent_id FROM categories ORDER BY category_name');
        return ($result && $result->num_rows) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Reviews for a product
    public function get_product_reviews($product_id) {
        $product_id = (int) $product_id;
        $sql = "SELECT r.review_id, r.rating, r.comment, r.review_date, u.full_name AS user_name
                FROM reviews r
                JOIN users u ON r.user_id = u.user_id
                WHERE r.product_id = $product_id
                ORDER BY r.review_date DESC";
        $result = $this->db->query($sql);
        return ($result && $result->num_rows) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Average rating and count
    public function get_average_rating($product_id) {
        $product_id = (int) $product_id;
        $result = $this->db->query("SELECT COALESCE(AVG(rating),0) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = $product_id");
        return ($result && $result->num_rows) ? $result->fetch_assoc() : ['avg_rating' => 0, 'total_reviews' => 0];
    }

    // Add product
    public function add_product($data) {
        $name = $this->db->real_escape_string($data['product_name'] ?? '');
        $brand = $this->db->real_escape_string($data['brand'] ?? '');
        $desc = $this->db->real_escape_string($data['description'] ?? '');
        $price = (float) ($data['price'] ?? 0);
        $stock = (int) ($data['stock'] ?? 0);
        $type = $this->db->real_escape_string($data['product_type'] ?? 'physical');
        $cat_id = (int) ($data['category_id'] ?? 0);
        $image = $this->db->real_escape_string($data['image'] ?? '');

        if (!$name || $price <= 0) {
            return ['success' => false, 'message' => 'Product name and valid price required'];
        }

        $sql = "INSERT INTO products (product_name, brand, description, price, stock, product_type, category_id, image)
                VALUES ('$name', '$brand', '$desc', $price, $stock, '$type', " . ($cat_id > 0 ? $cat_id : 'NULL') . ", '$image')";

        if ($this->db->query($sql)) {
            return ['success' => true, 'message' => 'Product added', 'id' => $this->db->insert_id];
        }
        return ['success' => false, 'message' => 'Add failed: ' . $this->db->error];
    }

    // Update product
    public function update_product($product_id, $data) {
        $product_id = (int) $product_id;
        $updates = [];

        if (isset($data['product_name'])) {
            $name = $this->db->real_escape_string($data['product_name']);
            $updates[] = "product_name = '$name'";
        }
        if (isset($data['brand'])) {
            $brand = $this->db->real_escape_string($data['brand']);
            $updates[] = "brand = '$brand'";
        }
        if (isset($data['description'])) {
            $desc = $this->db->real_escape_string($data['description']);
            $updates[] = "description = '$desc'";
        }
        if (isset($data['price'])) {
            $price = (float) $data['price'];
            $updates[] = "price = $price";
        }
        if (isset($data['stock'])) {
            $stock = (int) $data['stock'];
            $updates[] = "stock = $stock";
        }
        if (isset($data['product_type'])) {
            $type = $this->db->real_escape_string($data['product_type']);
            $updates[] = "product_type = '$type'";
        }
        if (isset($data['category_id'])) {
            $cat = (int) $data['category_id'];
            $updates[] = "category_id = " . ($cat > 0 ? $cat : 'NULL');
        }
        if (isset($data['image'])) {
            $image = $this->db->real_escape_string($data['image']);
            $updates[] = "image = '$image'";
        }

        if (empty($updates)) {
            return ['success' => false, 'message' => 'No updates'];
        }

        $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE product_id = $product_id";
        if ($this->db->query($sql)) {
            return ['success' => true, 'message' => 'Product updated'];
        }
        return ['success' => false, 'message' => 'Update failed: ' . $this->db->error];
    }

    // Delete product
    public function delete_product($product_id) {
        $product_id = (int) $product_id;
        $sql = "DELETE FROM products WHERE product_id = $product_id";
        if ($this->db->query($sql)) {
            return ['success' => true, 'message' => 'Product deleted'];
        }
        return ['success' => false, 'message' => 'Delete failed: ' . $this->db->error];
    }
}
?>
