<?php
require_once 'config/config.php';
require_once 'config/database.php';

$res = mysqli_query($conn, 'SELECT * FROM categories');
while($row = mysqli_fetch_assoc($res)){
    echo $row['category_id'] . ': ' . $row['category_name'] . "\n";
}
?>
