<?php
require_once __DIR__ . '/../includes/db_connection.php';
require_once __DIR__ . '/../includes/session.php';

if (is_logged_in()) {
    session_destroy();
}

header('Location: ' . BASE_PATH . '/index.php');
exit();
?>
