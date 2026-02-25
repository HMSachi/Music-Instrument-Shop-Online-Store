<?php
/**
 * General Configuration
 */

// Site settings
define('SITE_NAME', 'Melody Masters');
define('SITE_URL', 'http://localhost/Music-Instrument-Shop-Online-Store');

// Session settings
if (session_status() === PHP_SESSION_NONE) {
    // Secure session settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_samesite', 'Lax');
    
    session_start();
    
    // Session timeout (30 minutes of inactivity)
    $inactive_timeout = 1800;
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive_timeout)) {
        session_unset();
        session_destroy();
        session_start();
    }
    $_SESSION['last_activity'] = time();

    // Session Fingerprinting (Prevent Hijacking)
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'CLI-Agent';
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $user_agent;
    } elseif ($_SESSION['user_agent'] !== $user_agent) {
        session_unset();
        session_destroy();
        session_start();
    }
}

// Security Helper Functions
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrfInput() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

function sanitizeInput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    return $data;
}

// Global Security Sweep (XSS Mitigation)
// Note: We don't overwrite $_POST/$_GET globally to avoid breaking logic, 
// but we provide the sanitizeInput function for targeted use.

// Timezone
date_default_timezone_set('Asia/Manila');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Helper functions
function redirect($url) {
    header("Location: " . SITE_URL . "/" . $url);
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isStaff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
}

function isCustomer() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('index.php');
    }
}

function requireStaff() {
    if (!isStaff() && !isAdmin()) {
        redirect('index.php');
    }
}

function formatPrice($price) {
    return '£' . number_format($price, 2);
}

/**
 * Sets a flash message to be displayed on the next page load
 */
function setFlashMessage($type, $message) {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][$type] = $message;
}

/**
 * Gets and clears a flash message
 */
function getFlashMessage($type) {
    if (isset($_SESSION['flash_messages'][$type])) {
        $message = $_SESSION['flash_messages'][$type];
        unset($_SESSION['flash_messages'][$type]);
        return $message;
    }
    return null;
}
?>
