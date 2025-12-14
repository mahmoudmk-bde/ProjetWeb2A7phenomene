<?php
require_once __DIR__ . '/../db_config.php';

class feedbackcontroller
{
    private $db;

    public function __construct()
    {
        $this->db = config::getConnexion();
    }

    // Ajouter ou mettre à jour un feedback
    public function addFeedback($id_mission, $id_utilisateur, $rating, $commentaire)
    {
        try {
            $sql = "INSERT INTO feedback (id_mission, id_utilisateur, rating, commentaire) 
                    VALUES (?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE rating = ?, commentaire = ?, updated_at = NOW()";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $id_mission,
                $id_utilisateur,
                $rating,
                $commentaire,
                $rating,
                $commentaire
            ]);
        } catch (PDOException $e) {
            // Table doesn't exist - log error and return false
            error_log("Feedback table error: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les feedbacks d'une mission
    public function getFeedbacksByMission($id_mission)
    {
        try {
            $sql = "SELECT f.*, u.prenom, u.nom 
                    FROM feedback f 
                    JOIN utilisateur u ON f.id_utilisateur = u.id_util 
                    WHERE f.id_mission = ? 
                    ORDER BY f.date_feedback DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_mission]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Table doesn't exist - return empty array
            error_log("Feedback table error: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer le feedback d'un utilisateur pour une mission
    public function getUserFeedback($id_mission, $id_utilisateur)
    {
        try {
            $sql = "SELECT f.*, u.prenom, u.nom 
                    FROM feedback f 
                    JOIN utilisateur u ON f.id_utilisateur = u.id_util 
                    WHERE f.id_mission = ? AND f.id_utilisateur = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_mission, $id_utilisateur]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Table doesn't exist - return null
            error_log("Feedback table error: " . $e->getMessage());
            return null;
        }
    }

    // Calculer la note moyenne d'une mission
    public function getAverageRating($id_mission)
    {
        try {
            $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_feedbacks 
                    FROM feedback 
                    WHERE id_mission = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_mission]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['avg_rating' => 0, 'total_feedbacks' => 0];
        } catch (PDOException $e) {
            error_log("Feedback table error: " . $e->getMessage());
            return ['avg_rating' => 0, 'total_feedbacks' => 0];
        }
    }

    // Récupérer toutes les statistiques de feedback
    public function getFeedbackStats($id_mission)
    {
        try {
            $sql = "SELECT 
                        AVG(rating) as avg_rating,
                        COUNT(*) as total_feedbacks,
                        COUNT(CASE WHEN rating = 5 THEN 1 END) as five_star,
                        COUNT(CASE WHEN rating = 4 THEN 1 END) as four_star,
                        COUNT(CASE WHEN rating = 3 THEN 1 END) as three_star,
                        COUNT(CASE WHEN rating = 2 THEN 1 END) as two_star,
                        COUNT(CASE WHEN rating = 1 THEN 1 END) as one_star
                    FROM feedback 
                    WHERE id_mission = ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_mission]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Return default values if no feedbacks found
            if (!$result) {
                return [
                    'avg_rating' => 0,
                    'total_feedbacks' => 0,
                    'five_star' => 0,
                    'four_star' => 0,
                    'three_star' => 0,
                    'two_star' => 0,
                    'one_star' => 0
                ];
            }
            
            return $result;
        } catch (PDOException $e) {
            // Table doesn't exist or other error - return default values
            error_log("Feedback table error: " . $e->getMessage());
            return [
                'avg_rating' => 0,
                'total_feedbacks' => 0,
                'five_star' => 0,
                'four_star' => 0,
                'three_star' => 0,
                'two_star' => 0,
                'one_star' => 0
            ];
        }
    }

    // Récupérer le nombre total de feedbacks
    public function getTotalFeedbacks()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM feedback";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Feedback table error: " . $e->getMessage());
            return 0;
        }
    }

    // Récupérer la note moyenne de la plateforme
    public function getPlatformAverageRating()
    {
        try {
            $sql = "SELECT AVG(rating) as avg_rating FROM feedback";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['avg_rating'] ?: 0;
        } catch (PDOException $e) {
            error_log("Feedback table error: " . $e->getMessage());
            return 0;
        }
    }

    // Récupérer tous les feedbacks (pour l'admin)
    public function getAllFeedbacks()
    {
        try {
            $sql = "SELECT f.*, u.prenom, u.nom, u.mail, m.titre as mission_titre
                    FROM feedback f 
                    JOIN utilisateur u ON f.id_utilisateur = u.id_util 
                    JOIN missions m ON f.id_mission = m.id
                    ORDER BY f.date_feedback DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Feedback table error: " . $e->getMessage());
            return [];
        }
    }

    // Supprimer un feedback
    public function deleteFeedback($id)
    {
        try {
            $sql = "DELETE FROM feedback WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Feedback table error: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les missions les mieux notées
    public function getTopRatedMissions($limit = 5)
    {
        try {
            $sql = "SELECT m.*, AVG(f.rating) as avg_rating, COUNT(f.id) as total_feedbacks
                    FROM missions m 
                    LEFT JOIN feedback f ON m.id = f.id_mission 
                    GROUP BY m.id 
                    HAVING avg_rating IS NOT NULL 
                    ORDER BY avg_rating DESC, total_feedbacks DESC 
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Feedback table error: " . $e->getMessage());
            return [];
        }
    }

    // Vérifier si l'utilisateur peut donner un feedback (a participé à la mission)
    public function canUserGiveFeedback($id_mission, $id_utilisateur)
    {
        try {
            // Cette méthode peut être adaptée selon votre logique métier
            // Par exemple, vérifier si l'utilisateur a été accepté dans la mission
            $sql = "SELECT COUNT(*) as can_feedback 
                    FROM condidature 
                    WHERE id_mission = ? AND id_util = ? AND statut = 'accepte'";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_mission, $id_utilisateur]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ($result['can_feedback'] ?? 0) > 0;
        } catch (PDOException $e) {
            error_log("Feedback table error: " . $e->getMessage());
            return false;
        }
    }
}
?>