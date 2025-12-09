<?php
/**
 * Script d'installation pour créer les tables bad_words et user_bans
 * Usage: Ouvrez ce fichier dans votre navigateur ou exécutez: php install_badwords_bans.php
 */

require_once __DIR__ . '/db_config.php';

try {
    $db = config::getConnexion();
    
    // SQL pour créer la table bad_words
    $sql1 = "CREATE TABLE IF NOT EXISTS `bad_words` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `word` varchar(100) NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_word` (`word`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->exec($sql1);
    
    // SQL pour créer la table user_bans (utiliser DATETIME au lieu de TIMESTAMP)
    $sql2 = "CREATE TABLE IF NOT EXISTS `user_bans` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `utilisateur_id` int(11) NOT NULL,
        `reason` varchar(255) DEFAULT NULL,
        `banned_at` timestamp NOT NULL DEFAULT current_timestamp(),
        `expires_at` datetime NOT NULL,
        `is_active` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`),
        KEY `fk_ban_utilisateur` (`utilisateur_id`),
        KEY `idx_expires_at` (`expires_at`),
        KEY `idx_is_active` (`is_active`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // Exécuter les requêtes
    $db->exec($sql2);
    
    // Insérer quelques mots interdits par défaut
    $defaultWords = ['merde', 'con', 'connard', 'salope', 'pute', 'fuck', 'shit', 'damn', 'idiot', 'stupide'];
    foreach ($defaultWords as $word) {
        try {
            $stmt = $db->prepare("INSERT IGNORE INTO bad_words (word) VALUES (?)");
            $stmt->execute([$word]);
        } catch (PDOException $e) {
            // Ignorer les erreurs de duplication
        }
    }
    
    echo "✅ Tables 'bad_words' et 'user_bans' créées avec succès !\n";
    echo "✅ Mots interdits par défaut insérés.\n";
    echo "Vous pouvez maintenant utiliser la fonctionnalité de modération des commentaires.\n";
    
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'already exists') !== false || strpos($e->getMessage(), 'Duplicate') !== false) {
        echo "ℹ️  Les tables existent déjà.\n";
    } else {
        echo "❌ Erreur lors de la création des tables: " . $e->getMessage() . "\n";
        echo "\nVous pouvez aussi exécuter manuellement le fichier SQL 'migrations/create_badwords_bans_tables.sql' dans phpMyAdmin.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

