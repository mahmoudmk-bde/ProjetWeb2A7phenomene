<?php
/**
 * Script de migration pour créer la table likes_missions
 * Usage: php migrate_likes.php
 */

require_once __DIR__ . '/db_config.php';

try {
    $db = config::getConnexion();
    
    // Lire le fichier SQL
    $sql = file_get_contents(__DIR__ . '/migrations/create_likes_table.sql');
    
    // Exécuter la requête
    $db->exec($sql);
    
    echo "✅ Table 'likes_missions' créée avec succès !\n";
    echo "Vous pouvez maintenant utiliser la fonctionnalité de like sur les missions.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "ℹ️  La table 'likes_missions' existe déjà.\n";
    } else {
        echo "❌ Erreur lors de la création de la table: " . $e->getMessage() . "\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

