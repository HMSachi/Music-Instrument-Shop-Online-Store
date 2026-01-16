<?php
require_once 'config/db_connect.php';

$full_name = 'Admin User';
$email = 'admin@gmail.com';
$password = password_hash('123', PASSWORD_DEFAULT);
$role = 'admin';

try {
    // Delete existing admin if any to avoid confusion
    $pdo->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
    
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $password, $role]);
    echo "Admin user created successfully with role 'admin'!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
