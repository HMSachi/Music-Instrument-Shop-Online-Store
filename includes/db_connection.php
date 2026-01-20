<?php
/**
 * Database Connection Handler
 * Manages connection to MySQL database
 */

$host = 'localhost';
$dbname = 'melody_masters';
$username = 'root';
$password = '';

try {
    $db = new mysqli($host, $username, $password, $dbname);
    
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    
    // Set charset to utf8
    $db->set_charset("utf8mb4");
    
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Helper function to escape input
function sanitize_input($input) {
    global $db;
    return mysqli_real_escape_string($db, trim($input));
}

// Helper function to validate email
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper function to check password strength
function is_strong_password($password) {
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

?>
