<?php
/**
 * Script d'installation pour créer la table likes_missions
 * Usage: Ouvrez ce fichier dans votre navigateur ou exécutez: php install_likes_table.php
 */

require_once __DIR__ . '/db_config.php';

try {
    $db = config::getConnexion();
    
    // SQL pour créer la table (sans contraintes de clés étrangères pour éviter les erreurs)
    $sql = "CREATE TABLE IF NOT EXISTS `likes_missions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `mission_id` int(11) NOT NULL,
        `utilisateur_id` int(11) NOT NULL,
        `date_like` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_like` (`mission_id`, `utilisateur_id`),
        KEY `fk_like_mission` (`mission_id`),
        KEY `fk_like_utilisateur` (`utilisateur_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // Exécuter la requête
    $db->exec($sql);
    
    echo "✅ Table 'likes_missions' créée avec succès !\n";
    echo "Vous pouvez maintenant utiliser la fonctionnalité de like sur les missions.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "ℹ️  La table 'likes_missions' existe déjà.\n";
    } else {
        echo "❌ Erreur lors de la création de la table: " . $e->getMessage() . "\n";
        echo "\nVous pouvez aussi exécuter manuellement le fichier SQL 'create_likes_table.sql' dans phpMyAdmin.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

