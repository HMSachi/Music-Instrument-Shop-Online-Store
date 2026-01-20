<?php
/**
 * Authentication Handler
 * Manages login and signup functionality
 */

include 'db_connection.php';

class AuthHandler {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Register a new user
     */
    public function register($name, $email, $password, $role = 'customer') {
        // Validate inputs
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!is_valid_email($email)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        if (!is_strong_password($password)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with uppercase and numbers'];
        }
        
        $role = strtolower(trim($role));
        if (!in_array($role, ['admin', 'staff', 'customer'])) {
            return ['success' => false, 'message' => 'Invalid role'];
        }
        
        // Check if email already exists
        $email = sanitize_input($email);
        $result = $this->db->query("SELECT user_id FROM users WHERE email = '$email'");
        
        if ($result && $result->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert user
        $name = sanitize_input($name);
        $query = "INSERT INTO users (full_name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
        
        if ($this->db->query($query)) {
            return ['success' => true, 'message' => 'Registration successful. Please login.'];
        } else {
            return ['success' => false, 'message' => 'Registration failed: ' . $this->db->error];
        }
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        // Validate inputs
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }
        
        // Fetch user
        $email = sanitize_input($email);
        $result = $this->db->query("SELECT user_id, full_name, email, password, role FROM users WHERE email = '$email'");
        
        if (!$result || $result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        $user = $result->fetch_assoc();
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = strtolower($user['role']);
        
        return ['success' => true, 'message' => 'Login successful', 'role' => $_SESSION['role']];
    }
    
    /**
     * Get all users
     */
    public function get_all_users() {
        $result = $this->db->query("SELECT user_id, full_name, email, role, created_at FROM users ORDER BY created_at DESC");
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        
        return [];
    }
    
    /**
     * Update user role
     */
    public function update_user_role($user_id, $new_role) {
        $new_role = strtolower(trim($new_role));
        if (!in_array($new_role, ['admin', 'staff', 'customer'])) {
            return ['success' => false, 'message' => 'Invalid role'];
        }
        
        $user_id = (int) $user_id;
        $query = "UPDATE users SET role = '$new_role' WHERE user_id = $user_id";
        
        if ($this->db->query($query)) {
            return ['success' => true, 'message' => 'User role updated'];
        } else {
            return ['success' => false, 'message' => 'Update failed'];
        }
    }
    
    /**
     * Deactivate user (not used; kept for compatibility)
     */
    public function deactivate_user($user_id) {
        // No is_active column in current schema; consider adding if needed.
        return ['success' => false, 'message' => 'Deactivation not supported in current schema'];
    }
}

?>
