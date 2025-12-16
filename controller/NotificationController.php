<?php

class NotificationController {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../db_config.php';
        $this->db = config::getConnexion();
    }
    
    /**
     * Get all notifications for backoffice (admin)
     */
    public function getBackofficeNotifications($limit = 20) {
        $notifications = [];
        
        try {
            // New Reclamations (Non traite status)
            $recStmt = $this->db->query("
                SELECT id, sujet, description, utilisateur_id, date_creation 
                FROM reclamation 
                WHERE statut = 'Non traite'
                ORDER BY date_creation DESC 
                LIMIT 10
            ");
            foreach ($recStmt->fetchAll(PDO::FETCH_ASSOC) as $rec) {
                $notifications[] = [
                    'id' => $rec['id'],
                    'type' => 'reclamation',
                    'title' => 'Nouvelle réclamation',
                    'body' => $rec['sujet'] ?? 'Réclamation',
                    'text' => substr($rec['description'] ?? '', 0, 100),
                    'date' => $rec['date_creation'] ?? null,
                    'link' => 'reclamation/listReclamation.php#rec-' . (int)$rec['id'],
                    'icon' => 'fas fa-exclamation-circle',
                    'color' => '#ff4a57',
                    'key' => md5('rec|' . $rec['id'])
                ];
            }
            
            // New Event Participations
            $eventParStmt = $this->db->query("
                SELECT p.id_participation, p.id_evenement, p.id_volontaire, p.date_participation, e.titre, u.prenom, u.nom
                FROM participation p
                JOIN evenement e ON e.id_evenement = p.id_evenement
                JOIN utilisateur u ON u.id_util = p.id_volontaire
                ORDER BY p.date_participation DESC 
                LIMIT 10
            ");
            foreach ($eventParStmt->fetchAll(PDO::FETCH_ASSOC) as $par) {
                $notifications[] = [
                    'id' => $par['id_participation'],
                    'type' => 'event_participation',
                    'title' => '🎉 Nouvelle participation événement',
                    'body' => ($par['prenom'] ?? 'Utilisateur') . ' ' . ($par['nom'] ?? ''),
                    'text' => 'Participe à: ' . ($par['titre'] ?? 'Événement'),
                    'date' => $par['date_participation'] ?? null,
                    'link' => 'events/participation_history.php',
                    'icon' => 'fas fa-calendar-check',
                    'color' => '#28a745',
                    'key' => md5('event_par|' . $par['id_participation'])
                ];
            }
            
            // New Mission Candidatures
            $missStmt = $this->db->query("
                SELECT c.id, c.mission_id, c.utilisateur_id, c.date_candidature, m.titre, u.prenom, u.nom
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                JOIN utilisateur u ON u.id_util = c.utilisateur_id
                WHERE c.statut = 'en_attente'
                ORDER BY c.date_candidature DESC 
                LIMIT 10
            ");
            foreach ($missStmt->fetchAll(PDO::FETCH_ASSOC) as $cand) {
                $notifications[] = [
                    'id' => $cand['id'],
                    'type' => 'mission_candidature',
                    'title' => '📝 Nouvelle candidature mission',
                    'body' => ($cand['prenom'] ?? 'Utilisateur') . ' ' . ($cand['nom'] ?? ''),
                    'text' => 'Candidat pour: ' . ($cand['titre'] ?? 'Mission'),
                    'date' => $cand['date_candidature'] ?? null,
                    'link' => 'mission/missionliste.php',
                    'icon' => 'fas fa-file-alt',
                    'color' => '#ffc107',
                    'key' => md5('miss_cand|' . $cand['id'])
                ];
            }
            
            // New Feedback
            $fbStmt = $this->db->query("
                SELECT id, id_mission, commentaire, id_utilisateur, date_feedback, created_at
                FROM feedback 
                ORDER BY date_feedback DESC, created_at DESC 
                LIMIT 10
            ");
            foreach ($fbStmt->fetchAll(PDO::FETCH_ASSOC) as $fb) {
                $date = $fb['date_feedback'] ?? $fb['created_at'];
                $notifications[] = [
                    'id' => $fb['id'],
                    'type' => 'feedback',
                    'title' => '💬 Nouveau feedback',
                    'body' => 'Feedback Mission #' . ($fb['id_mission'] ?? 'N/A'),
                    'text' => substr($fb['commentaire'] ?? '', 0, 100),
                    'date' => $date,
                    'link' => 'feedback/feedbackliste.php',
                    'icon' => 'fas fa-comments',
                    'color' => '#17a2b8',
                    'key' => md5('fb|' . $fb['id'])
                ];
            }
            
            // Sort by date descending
            usort($notifications, function($a, $b) {
                $da = isset($a['date']) ? strtotime($a['date']) : 0;
                $db = isset($b['date']) ? strtotime($b['date']) : 0;
                return $db <=> $da;
            });
            
            // Limit total
            return array_slice($notifications, 0, $limit);
            
        } catch (Exception $e) {
            error_log('Error getting backoffice notifications: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Mark reclamation as treated
     */
    public function markReclamationTreated($reclamation_id) {
        try {
            $stmt = $this->db->prepare("UPDATE reclamation SET statut = 'En cours' WHERE id = :id");
            return $stmt->execute(['id' => $reclamation_id]);
        } catch (Exception $e) {
            error_log('Error marking reclamation as treated: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get count of unread reclamations (Non traite status)
     */
    public function getUnreadReclamationCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as cnt FROM reclamation WHERE statut = 'Non traite'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['cnt'] : 0;
        } catch (Exception $e) {
            error_log('Error counting unread reclamations: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Debug helper to check database connection and table data
     */
    public function debugInfo() {
        $debug = [];
        try {
            // Check reclamations
            $recCount = $this->db->query("SELECT COUNT(*) as cnt FROM reclamation")->fetch(PDO::FETCH_ASSOC);
            $recNonTraite = $this->db->query("SELECT COUNT(*) as cnt FROM reclamation WHERE statut = 'Non traite'")->fetch(PDO::FETCH_ASSOC);
            $debug['reclamation_total'] = $recCount['cnt'] ?? 0;
            $debug['reclamation_non_traite'] = $recNonTraite['cnt'] ?? 0;
            
            // Check participation
            $partCount = $this->db->query("SELECT COUNT(*) as cnt FROM participation")->fetch(PDO::FETCH_ASSOC);
            $debug['participation_total'] = $partCount['cnt'] ?? 0;
            
            // Check candidatures
            $candCount = $this->db->query("SELECT COUNT(*) as cnt FROM candidatures WHERE statut = 'en_attente'")->fetch(PDO::FETCH_ASSOC);
            $debug['candidatures_pending'] = $candCount['cnt'] ?? 0;
            
            // Check feedback
            $fbCount = $this->db->query("SELECT COUNT(*) as cnt FROM feedback")->fetch(PDO::FETCH_ASSOC);
            $debug['feedback_total'] = $fbCount['cnt'] ?? 0;
            
        } catch (Exception $e) {
            $debug['error'] = $e->getMessage();
        }
        return $debug;
    }
}
?>
