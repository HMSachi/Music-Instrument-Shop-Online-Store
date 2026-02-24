<?php
/**
 * Database Configuration for Melody Masters
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'melody_masters');

// Create connection
function getDBConnection() {
    // Suppress warnings for connection to handle errors manually
    mysqli_report(MYSQLI_REPORT_OFF);
    
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        // Log the error internally and show a generic message
        error_log("Database Connection Error: " . $conn->connect_error);
        return false;
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

/**
 * Executes a query with error checking
 * 
 * @param mysqli $conn The database connection
 * @param string $sql The SQL query
 * @return mysqli_result|bool The result set or false on failure
 */
function safeQuery($conn, $sql) {
    if (!$conn) return false;
    
    $result = $conn->query($sql);
    
    if ($result === false) {
        error_log("Database Query Error: " . $conn->error . " | SQL: " . $sql);
    }
    
    return $result;
}

// Global connection
$conn = getDBConnection();
?>
