<?php
require_once 'config/config.php';

// Unset all session variables
$_SESSION = array();

// Delete session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Destroy session
session_destroy();

// Start a new session just for the flash message
session_start();
setFlashMessage('success', 'You have been logged out successfully.');

// Redirect to login page
header("Location: " . SITE_URL . "/login.php");
exit();
?>
