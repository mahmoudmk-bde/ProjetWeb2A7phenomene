<?php
require_once __DIR__ . '/../db_config.php';

class LikeController
{
    private $db;
    private static $tableCreated = false;

    public function __construct()
    {
        $this->db = config::getConnexion();
        // Créer la table automatiquement en arrière-plan si elle n'existe pas
        $this->ensureTableExists();
    }

    // Vérifier et créer la table si nécessaire (en arrière-plan)
    private function ensureTableExists()
    {
        // Éviter de vérifier plusieurs fois dans la même requête
        if (self::$tableCreated) {
            return;
        }

        try {
            // Test simple pour vérifier si la table existe
            $this->db->query("SELECT 1 FROM likes_missions LIMIT 1");
            self::$tableCreated = true;
        } catch (PDOException $e) {
            // Si la table n'existe pas, la créer
            if (strpos($e->getMessage(), "doesn't exist") !== false || 
                strpos($e->getMessage(), "Unknown table") !== false) {
                $this->createTable();
                self::$tableCreated = true;
            }
        }
    }

    // Ajouter ou retirer un like
    public function toggleLike($mission_id, $utilisateur_id)
    {
        // S'assurer que la table existe
        $this->ensureTableExists();
        
        try {
            // Vérifier si l'utilisateur a déjà liké
            $checkSql = "SELECT id FROM likes_missions WHERE mission_id = :mission_id AND utilisateur_id = :utilisateur_id";
            $checkStmt = $this->db->prepare($checkSql);
            $checkStmt->execute([
                ':mission_id' => $mission_id,
                ':utilisateur_id' => $utilisateur_id
            ]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Retirer le like
                $deleteSql = "DELETE FROM likes_missions WHERE id = :id";
                $deleteStmt = $this->db->prepare($deleteSql);
                $deleteStmt->execute([':id' => $existing['id']]);
                return ['action' => 'unliked', 'liked' => false];
            } else {
                // Ajouter le like
                $insertSql = "INSERT INTO likes_missions (mission_id, utilisateur_id) VALUES (:mission_id, :utilisateur_id)";
                $insertStmt = $this->db->prepare($insertSql);
                $insertStmt->execute([
                    ':mission_id' => $mission_id,
                    ':utilisateur_id' => $utilisateur_id
                ]);
                return ['action' => 'liked', 'liked' => true];
            }
        } catch (PDOException $e) {
            // Si la table n'existe toujours pas, créer la table et réessayer
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $this->createTable();
                self::$tableCreated = true;
                // Réessayer après création de la table
                return $this->toggleLike($mission_id, $utilisateur_id);
            }
            throw $e;
        }
    }

    // Créer la table si elle n'existe pas
    private function createTable()
    {
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
        
        $this->db->exec($sql);
    }

    // Obtenir le nombre de likes pour plusieurs missions en une fois (optimisation)
    public function getLikesCountForMissions(array $mission_ids)
    {
        if (empty($mission_ids)) {
            return [];
        }

        // S'assurer que la table existe
        $this->ensureTableExists();

        try {
            $placeholders = implode(',', array_fill(0, count($mission_ids), '?'));
            $sql = "SELECT mission_id, COUNT(*) as count 
                    FROM likes_missions 
                    WHERE mission_id IN ($placeholders)
                    GROUP BY mission_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($mission_ids);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Créer un tableau associatif mission_id => count
            $likesCount = [];
            foreach ($results as $row) {
                $likesCount[$row['mission_id']] = (int) $row['count'];
            }

            // S'assurer que toutes les missions ont une entrée (même si 0 likes)
            foreach ($mission_ids as $mission_id) {
                if (!isset($likesCount[$mission_id])) {
                    $likesCount[$mission_id] = 0;
                }
            }

            return $likesCount;
        } catch (PDOException $e) {
            // Si la table n'existe toujours pas, retourner des zéros
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $this->createTable();
                self::$tableCreated = true;
                return array_fill_keys($mission_ids, 0);
            }
            throw $e;
        }
    }

    // Vérifier si un utilisateur a liké une mission
    public function hasUserLiked($mission_id, $utilisateur_id)
    {
        // S'assurer que la table existe
        $this->ensureTableExists();
        
        try {
            $sql = "SELECT COUNT(*) as count FROM likes_missions WHERE mission_id = :mission_id AND utilisateur_id = :utilisateur_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':mission_id' => $mission_id,
                ':utilisateur_id' => $utilisateur_id
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            // Si la table n'existe toujours pas, créer la table et retourner false
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $this->createTable();
                self::$tableCreated = true;
                return false;
            }
            throw $e;
        }
    }

    // Obtenir le nombre de likes pour une mission
    public function getLikeCount($mission_id)
    {
        // S'assurer que la table existe
        $this->ensureTableExists();
        
        try {
            $sql = "SELECT COUNT(*) as count FROM likes_missions WHERE mission_id = :mission_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':mission_id' => $mission_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int) $result['count'];
        } catch (PDOException $e) {
            // Si la table n'existe toujours pas, créer la table et retourner 0
            if (strpos($e->getMessage(), "doesn't exist") !== false) {
                $this->createTable();
                self::$tableCreated = true;
                return 0;
            }
            throw $e;
        }
    }
}

