<?php
// Base path when running from a subdirectory (XAMPP htdocs)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/Music-Instrument-Shop-Online-Store');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function has_role($required_role) {
    return isset($_SESSION['role']) && strtolower($_SESSION['role']) === strtolower($required_role);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_PATH . '/login.php');
        exit();
    }
}

function require_admin() {
    require_login();
    if (!has_role('admin')) {
        header('Location: ' . BASE_PATH . '/403.php');
        exit();
    }
}

function require_staff() {
    require_login();
    if (!has_role('staff') && !has_role('admin')) {
        header('Location: ' . BASE_PATH . '/403.php');
        exit();
    }
}

function require_customer() {
    require_login();
    if (!has_role('customer')) {
        header('Location: ' . BASE_PATH . '/403.php');
        exit();
    }
}

function logout() {
    session_destroy();
    header('Location: ' . BASE_PATH . '/index.php');
    exit();
}

// Fetch the current session user
function get_session_user() {
    global $db;
    if (!is_logged_in()) {
        return null;
    }
    $user_id = (int) $_SESSION['user_id'];
    $stmt = $db->prepare('SELECT user_id, full_name, email, role FROM users WHERE user_id = ?');
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result && $result->num_rows ? $result->fetch_assoc() : null;
}
?>
