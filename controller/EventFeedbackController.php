<?php
require_once __DIR__ . '/../db_config.php';

class EventFeedbackController
{
    private $db;

    public function __construct()
    {
        $this->db = config::getConnexion();
        $this->ensureTable();
    }

    private function ensureTable()
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS event_feedback (
                id INT(11) NOT NULL AUTO_INCREMENT,
                id_event INT(11) NOT NULL,
                id_utilisateur INT(11) NOT NULL,
                rating TINYINT(1) NOT NULL DEFAULT 5,
                commentaire LONGTEXT DEFAULT NULL,
                date_feedback TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY unique_event_user (id_event, id_utilisateur),
                KEY idx_event (id_event),
                KEY idx_user (id_utilisateur)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            $this->db->exec($sql);
        } catch (PDOException $e) {
            error_log('Failed to ensure event_feedback table: ' . $e->getMessage());
        }
    }

    public function addFeedback($id_event, $id_utilisateur, $rating, $commentaire)
    {
        try {
            $sql = "INSERT INTO event_feedback (id_event, id_utilisateur, rating, commentaire)
                    VALUES (:id_event, :id_utilisateur, :rating, :commentaire)
                    ON DUPLICATE KEY UPDATE rating = VALUES(rating), commentaire = VALUES(commentaire), updated_at = NOW()";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id_event' => $id_event,
                ':id_utilisateur' => $id_utilisateur,
                ':rating' => $rating,
                ':commentaire' => $commentaire,
            ]);
        } catch (PDOException $e) {
            error_log('addFeedback (event) failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getFeedbacksByEvent($id_event)
    {
        try {
            $sql = "SELECT f.*, u.prenom, u.nom
                    FROM event_feedback f
                    JOIN utilisateur u ON f.id_utilisateur = u.id_util
                    WHERE f.id_event = :id
                    ORDER BY f.date_feedback DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id_event]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getFeedbacksByEvent failed: ' . $e->getMessage());
            return [];
        }
    }

    public function getUserFeedback($id_event, $id_utilisateur)
    {
        try {
            $sql = "SELECT f.*, u.prenom, u.nom
                    FROM event_feedback f
                    JOIN utilisateur u ON f.id_utilisateur = u.id_util
                    WHERE f.id_event = :id_event AND f.id_utilisateur = :id_user";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id_event' => $id_event, ':id_user' => $id_utilisateur]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('getUserFeedback (event) failed: ' . $e->getMessage());
            return null;
        }
    }

    public function getFeedbackStats($id_event)
    {
        try {
            $sql = "SELECT 
                        AVG(rating) as avg_rating,
                        COUNT(*) as total_feedbacks,
                        SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                        SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                        SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                        SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                        SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                    FROM event_feedback 
                    WHERE id_event = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id_event]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$result) {
                return [
                    'avg_rating' => 0,
                    'total_feedbacks' => 0,
                    'five_star' => 0,
                    'four_star' => 0,
                    'three_star' => 0,
                    'two_star' => 0,
                    'one_star' => 0,
                ];
            }
            return $result;
        } catch (PDOException $e) {
            error_log('getFeedbackStats (event) failed: ' . $e->getMessage());
            return [
                'avg_rating' => 0,
                'total_feedbacks' => 0,
                'five_star' => 0,
                'four_star' => 0,
                'three_star' => 0,
                'two_star' => 0,
                'one_star' => 0,
            ];
        }
    }
}
