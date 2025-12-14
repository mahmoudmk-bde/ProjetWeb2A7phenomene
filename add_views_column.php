<?php
require_once 'config.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if column exists
    $check = $conn->query("SHOW COLUMNS FROM evenement LIKE 'vues'");
    if ($check->rowCount() == 0) {
        // Add column
        $sql = "ALTER TABLE evenement ADD COLUMN vues INT DEFAULT 0";
        $conn->exec($sql);
        echo "Column 'vues' added successfully.<br>";
    } else {
        echo "Column 'vues' already exists.<br>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
