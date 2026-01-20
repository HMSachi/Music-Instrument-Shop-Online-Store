<?php
/**
 * Session Management
 * Handles user sessions and authentication
 */

session_start();

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Function to check user role
function has_role($required_role) {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === strtolower($required_role);
}

// Function to require login
function require_login() {
    if (!is_logged_in()) {
        header("Location: /login.php");
        exit();
    }
}

// Function to require admin access
function require_admin() {
    require_login();
    if (!has_role('admin')) {
        header("Location: /403.php");
        exit();
    }
}

// Function to require staff access
function require_staff() {
    require_login();
    if (!has_role('staff') && !has_role('admin')) {
        header("Location: /403.php");
        exit();
    }
}

// Function to require customer access
function require_customer() {
    require_login();
    if (!has_role('customer')) {
        header("Location: /403.php");
        exit();
    }
}

// Function to logout
function logout() {
    session_destroy();
    header("Location: /index.php");
    exit();
}

// Function to get current user info (renamed to avoid PHP's built-in get_current_user)
function get_session_user() {
    global $db;

    if (!is_logged_in()) {
        return null;
    }

    $user_id = (int) $_SESSION['user_id'];
    $sql = "SELECT user_id, full_name, email, role FROM users WHERE user_id = $user_id";
    $result = $db->query($sql);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

?>
