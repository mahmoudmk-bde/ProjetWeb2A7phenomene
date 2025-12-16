<?php
// Script pour mettre à jour la base de données pour le login social
require_once __DIR__ . '/../config.php';

try {
    $db = config::getConnexion();
    
    // Vérifier si les colonnes existent déjà
    $columns = $db->query("SHOW COLUMNS FROM utilisateur LIKE 'oauth_uid'")->fetchAll();
    
    if (empty($columns)) {
        // Ajouter les colonnes
        $sql = "ALTER TABLE utilisateur 
                ADD COLUMN oauth_provider VARCHAR(50) DEFAULT NULL,
                ADD COLUMN oauth_uid VARCHAR(255) DEFAULT NULL,
                ADD INDEX (oauth_uid)"; // Ajouter un index pour la recherche rapide
        
        $db->exec($sql);
        echo "Base de données mise à jour avec succès ! Colonnes oauth_provider et oauth_uid ajoutées.";
    } else {
        echo "Les colonnes existent déjà. Aucune action n'est nécessaire.";
    }
    
} catch (PDOException $e) {
    echo "Erreur lors de la mise à jour de la base de données : " . $e->getMessage();
}
?>
