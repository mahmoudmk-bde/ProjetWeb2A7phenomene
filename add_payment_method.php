<?php
// Script to add 'payment_method' column to 'orders' table

require_once 'db_config.php';

try {
    $db = config::getConnexion();

    // Check if column exists
    $check = $db->query("SHOW COLUMNS FROM orders LIKE 'payment_method'");

    if ($check->rowCount() == 0) {
        $sql = "ALTER TABLE orders ADD COLUMN payment_method VARCHAR(50) DEFAULT 'onsite' AFTER total";
        $db->exec($sql);
        echo "Column 'payment_method' added successfully.";
    } else {
        echo "Column 'payment_method' already exists.";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>