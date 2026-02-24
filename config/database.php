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
 * DEPRECATED: Use preparedQuery instead to prevent SQL injection.
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

/**
 * Executes a prepared statement for safe data handling
 * 
 * @param mysqli $conn The database connection
 * @param string $sql The SQL query with placeholders (?)
 * @param array $params Array of parameters to bind
 * @param string $types String of types (e.g., "ssi")
 * @return mysqli_result|bool|int The result set, affected rows, or last insert ID
 */
function preparedQuery($conn, $sql, $params = [], $types = "") {
    if (!$conn) return false;

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error . " | SQL: " . $sql);
        return false;
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = str_repeat('s', count($params)); // Default to string if types not provided
        }
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error . " | SQL: " . $sql);
        $stmt->close();
        return false;
    }

    $result = false;
    if (stripos($sql, 'SELECT') === 0) {
        $result = $stmt->get_result();
    } elseif (stripos($sql, 'INSERT') === 0) {
        $result = $conn->insert_id ?: true;
    } else {
        $result = $stmt->affected_rows >= 0 ? true : false;
    }

    $stmt->close();
    return $result;
}

// Global connection
$conn = getDBConnection();
?>
