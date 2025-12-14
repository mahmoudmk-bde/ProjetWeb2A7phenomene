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
        echo "Colonne 'vues' ajoutée avec succès !";
    } else {
        echo "La colonne 'vues' existe déjà.";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
