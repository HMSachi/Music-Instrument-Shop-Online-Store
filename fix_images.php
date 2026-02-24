<?php
require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>Melody Masters Image Update Script</h2>";

if (!$conn) {
    die("Database connection failed.");
}

$updates = [
    'Acoustic Guitar' => 'assets/images/guitars2.jpg',
    'Electric Guitar' => 'assets/images/guitars1.png',
    'Bass Guitar'     => 'assets/images/guitars3.jpg',
    'Classical Guitar' => 'assets/images/guitars.png'
];

$success_count = 0;
$total_count = count($updates);

foreach ($updates as $name => $path) {
    $stmt = $conn->prepare("UPDATE products SET image = ? WHERE product_name = ?");
    $stmt->bind_param("ss", $path, $name);
    
    if ($stmt->execute()) {
        if ($conn->affected_rows > 0) {
            echo "✅ Updated <strong>$name</strong> to <code>$path</code><br>";
            $success_count++;
        } else {
            echo "ℹ️ <strong>$name</strong> already has correct path or not found.<br>";
        }
    } else {
        echo "❌ Failed to update <strong>$name</strong>: " . $conn->error . "<br>";
    }
    $stmt->close();
}

echo "<br><h3>Update Complete!</h3>";
echo "Successfully updated $success_count products.<br>";
echo "<a href='index.php'>Go to Homepage</a>";
?>
