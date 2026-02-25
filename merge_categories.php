<?php
require_once 'config/config.php';
require_once 'config/database.php';

echo "Merging categories...\n";

$requested_categories = [
    'Guitars',
    'Keyboards',
    'Drums and Percussion',
    'Wind Instruments',
    'String Instruments',
    'Accessories',
    'Digital Sheet Music'
];

foreach ($requested_categories as $name) {
    echo "Processing '$name'...\n";
    
    // Get all IDs for this name
    $sql = "SELECT category_id FROM categories WHERE category_name = ? ORDER BY category_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $ids = [];
    while ($row = $result->fetch_assoc()) {
        $ids[] = $row['category_id'];
    }
    
    if (count($ids) > 1) {
        $primary_id = $ids[0];
        $secondary_ids = array_slice($ids, 1);
        $secondary_ids_str = implode(',', $secondary_ids);
        
        echo "  Primary ID: $primary_id. Secondary IDs: $secondary_ids_str. Merging...\n";
        
        // Update products
        $update_sql = "UPDATE products SET category_id = $primary_id WHERE category_id IN ($secondary_ids_str)";
        if ($conn->query($update_sql)) {
            echo "  Updated products successfully.\n";
            
            // Delete secondary categories
            $delete_sql = "DELETE FROM categories WHERE category_id IN ($secondary_ids_str)";
            if ($conn->query($delete_sql)) {
                echo "  Deleted duplicate categories successfully.\n";
            } else {
                echo "  Failed to delete duplicate categories: " . $conn->error . "\n";
            }
        } else {
            echo "  Failed to update products: " . $conn->error . "\n";
        }
    } else {
        echo "  No duplicates found for '$name'.\n";
    }
}

echo "Cleanup complete.\n";

// Final Check
$res = mysqli_query($conn, 'SELECT * FROM categories');
echo "\nFinal Categories:\n";
while($row = mysqli_fetch_assoc($res)){
    echo $row['category_id'] . ': ' . $row['category_name'] . "\n";
}
?>
