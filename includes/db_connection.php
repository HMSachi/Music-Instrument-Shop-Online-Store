<?php
// Database connection settings (adjust if your MySQL credentials differ)
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', '');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'melody_masters');
}

// Create mysqli connection
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_errno) {
    die('Database connection failed: ' . $db->connect_error);
}

// Basic helpers used across the app
function sanitize_input($value) {
    return trim($value ?? '');
}

function is_valid_email($email) {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_strong_password($password) {
    // At least 8 chars, one uppercase, one digit
    return (bool) preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password);
}
?>
