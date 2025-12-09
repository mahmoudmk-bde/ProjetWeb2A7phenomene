<?php
require_once __DIR__ . '/../db_config.php';

class BadWordController
{
    private $db;
    private static $badWords = null;
    private static $tableCreated = false;

    public function __construct()
    {
        $this->db = config::getConnexion();
        $this->ensureTablesExist();
    }

    // Vérifier et créer les tables si nécessaire
    private function ensureTablesExist()
    {
        if (self::$tableCreated) {
            return;
        }

        try {
            // Test si les tables existent
            $this->db->query("SELECT 1 FROM bad_words LIMIT 1");
            $this->db->query("SELECT 1 FROM user_bans LIMIT 1");
            self::$tableCreated = true;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "doesn't exist") !== false || 
                strpos($e->getMessage(), "Unknown table") !== false) {
                $this->createTables();
                self::$tableCreated = true;
            }
        }
    }

    // Créer les tables si elles n'existent pas
    private function createTables()
    {
        // Créer la table bad_words
        $sql1 = "CREATE TABLE IF NOT EXISTS `bad_words` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `word` varchar(100) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_word` (`word`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $this->db->exec($sql1);
        
        // Créer la table user_bans (utiliser DATETIME au lieu de TIMESTAMP pour expires_at)
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
        
        $this->db->exec($sql2);
        
        // Insérer quelques mots interdits par défaut
        $defaultWords = ['merde', 'con', 'connard', 'salope', 'pute', 'fuck', 'shit', 'damn', 'idiot', 'stupide'];
        foreach ($defaultWords as $word) {
            try {
                $stmt = $this->db->prepare("INSERT IGNORE INTO bad_words (word) VALUES (?)");
                $stmt->execute([$word]);
            } catch (PDOException $e) {
                // Ignorer les erreurs de duplication
            }
        }
    }

    // Charger la liste des mots interdits (avec cache)
    private function loadBadWords()
    {
        if (self::$badWords !== null) {
            return self::$badWords;
        }

        $this->ensureTablesExist();

        try {
            $stmt = $this->db->query("SELECT word FROM bad_words");
            $words = $stmt->fetchAll(PDO::FETCH_COLUMN);
            self::$badWords = array_map('strtolower', $words);
            return self::$badWords;
        } catch (PDOException $e) {
            error_log("Error loading bad words: " . $e->getMessage());
            return [];
        }
    }

    // Vérifier si un texte contient des mots interdits
    public function containsBadWords($text)
    {
        if (empty($text)) {
            return false;
        }

        $badWords = $this->loadBadWords();
        if (empty($badWords)) {
            return false;
        }

        $textLower = mb_strtolower($text, 'UTF-8');
        
        // Normaliser le texte (enlever les accents, caractères spéciaux)
        $textNormalized = $this->normalizeText($textLower);

        foreach ($badWords as $badWord) {
            $badWordNormalized = $this->normalizeText($badWord);
            
            // Vérifier si le mot interdit est présent (mot entier ou partie)
            if (strpos($textNormalized, $badWordNormalized) !== false) {
                return true;
            }
        }

        return false;
    }

    // Normaliser le texte (enlever accents, caractères spéciaux)
    private function normalizeText($text)
    {
        // Remplacer les caractères accentués
        $text = str_replace(
            ['à', 'á', 'â', 'ã', 'ä', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'ç'],
            ['a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'c'],
            $text
        );
        
        // Enlever les caractères spéciaux et garder seulement lettres et chiffres
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);
        
        return $text;
    }

    // Bannir un utilisateur pendant 3 jours
    public function banUser($utilisateur_id, $reason = null)
    {
        $this->ensureTablesExist();

        try {
            // Calculer la date d'expiration (3 jours)
            $expiresAt = date('Y-m-d H:i:s', strtotime('+3 days'));

            // Désactiver les anciens bannissements actifs
            $stmt = $this->db->prepare("UPDATE user_bans SET is_active = 0 WHERE utilisateur_id = ? AND is_active = 1");
            $stmt->execute([$utilisateur_id]);

            // Créer un nouveau bannissement
            $stmt = $this->db->prepare("INSERT INTO user_bans (utilisateur_id, reason, expires_at) VALUES (?, ?, ?)");
            return $stmt->execute([$utilisateur_id, $reason, $expiresAt]);
        } catch (PDOException $e) {
            error_log("Error banning user: " . $e->getMessage());
            return false;
        }
    }

    // Vérifier si un utilisateur est banni
    public function isUserBanned($utilisateur_id)
    {
        $this->ensureTablesExist();

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_bans 
                WHERE utilisateur_id = ? 
                AND is_active = 1 
                AND expires_at > NOW()
                ORDER BY banned_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$utilisateur_id]);
            $ban = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $ban ? $ban : false;
        } catch (PDOException $e) {
            error_log("Error checking user ban: " . $e->getMessage());
            return false;
        }
    }

    // Obtenir les informations de bannissement d'un utilisateur
    public function getBanInfo($utilisateur_id)
    {
        return $this->isUserBanned($utilisateur_id);
    }

    // Nettoyer les bannissements expirés
    public function cleanExpiredBans()
    {
        $this->ensureTablesExist();

        try {
            $stmt = $this->db->prepare("UPDATE user_bans SET is_active = 0 WHERE expires_at <= NOW() AND is_active = 1");
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error cleaning expired bans: " . $e->getMessage());
            return false;
        }
    }

    // Ajouter un mot interdit
    public function addBadWord($word)
    {
        $this->ensureTablesExist();

        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO bad_words (word) VALUES (?)");
            $result = $stmt->execute([strtolower(trim($word))]);
            
            // Réinitialiser le cache
            self::$badWords = null;
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error adding bad word: " . $e->getMessage());
            return false;
        }
    }

    // Supprimer un mot interdit
    public function removeBadWord($word)
    {
        $this->ensureTablesExist();

        try {
            $stmt = $this->db->prepare("DELETE FROM bad_words WHERE word = ?");
            $result = $stmt->execute([strtolower(trim($word))]);
            
            // Réinitialiser le cache
            self::$badWords = null;
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error removing bad word: " . $e->getMessage());
            return false;
        }
    }

    // Obtenir tous les mots interdits
    public function getAllBadWords()
    {
        $this->ensureTablesExist();

        try {
            $stmt = $this->db->query("SELECT * FROM bad_words ORDER BY word ASC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting bad words: " . $e->getMessage());
            return [];
        }
    }
}

