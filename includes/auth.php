<?php
/**
 * Authentication Handler
 * Handles user registration and login
 */

require_once __DIR__ . '/db_connection.php';

class AuthHandler {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /** Register a new user */
    public function register($name, $email, $password, $role = 'customer') {
        $name = sanitize_input($name);
        $email = sanitize_input($email);
        $password = $password ?? '';
        $role = strtolower(trim($role ?? 'customer'));

        if (!$name || !$email || !$password) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        if (!is_valid_email($email)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        if (!is_strong_password($password)) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters with an uppercase letter and a number'];
        }
        if (!in_array($role, ['admin', 'staff', 'customer'])) {
            return ['success' => false, 'message' => 'Invalid role'];
        }

        // Check duplicate email
        $stmt = $this->db->prepare('SELECT user_id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $exists = $stmt->get_result();
        if ($exists && $exists->num_rows > 0) {
            return ['success' => false, 'message' => 'Email already registered'];
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssss', $name, $email, $hashed, $role);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful. Please log in.'];
        }
        return ['success' => false, 'message' => 'Registration failed: ' . $this->db->error];
    }

    /** Login user */
    public function login($email, $password) {
        $email = sanitize_input($email);
        $password = $password ?? '';

        if (!$email || !$password) {
            return ['success' => false, 'message' => 'Email and password are required'];
        }

        $stmt = $this->db->prepare('SELECT user_id, full_name, email, password, role FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = strtolower($user['role']);

        return ['success' => true, 'message' => 'Login successful', 'role' => $_SESSION['role']];
    }
}
?>
