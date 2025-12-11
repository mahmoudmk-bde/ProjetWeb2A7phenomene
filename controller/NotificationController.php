<?php
require_once __DIR__ . '/../db_config.php';

class NotificationController {
    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    /**
     * Get all notifications for a user (answered reclamations, event responses, candidature status changes)
     */
    public function getUserNotifications($user_id, $limit = 50) {
        $user_id = intval($user_id);
        $notifications = [];

        try {
            // 1. Answered reclamations (where vu = 0, i.e., unread responses)
            $recStmt = $this->pdo->prepare("
                SELECT 
                    r.id as notification_id,
                    r.sujet as title,
                    r.description as message,
                    COUNT(resp.id) as response_count,
                    MAX(resp.date_response) as created_at,
                    CASE WHEN COUNT(CASE WHEN resp.vu = 0 THEN 1 END) > 0 THEN 1 ELSE 0 END as is_unread
                FROM reclamation r
                LEFT JOIN response resp ON resp.reclamation_id = r.id
                WHERE r.utilisateur_id = :uid AND resp.id IS NOT NULL
                GROUP BY r.id
                ORDER BY MAX(resp.date_response) DESC
                LIMIT :limit
            ");
            $recStmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
            $recStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $recStmt->execute();
            $reclamations = $recStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($reclamations as &$rec) {
                $rec['type'] = 'reclamation_answer';
                $rec['link'] = 'historique_reclamations.php?reclamation_id=' . $rec['notification_id'];
            }
            $notifications = array_merge($notifications, $reclamations);

            // 2. Accepted candidatures (mission applications accepted)
            $candStmt = $this->pdo->prepare("
                SELECT 
                    c.id as notification_id,
                    m.titre as title,
                    'Votre candidature a été acceptée' as message,
                    1 as response_count,
                    c.date_reponse as created_at,
                    c.date_reponse as created_at,
                    c.vu,
                    m.id as mission_id
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                WHERE c.utilisateur_id = :uid AND c.statut = 'acceptee' AND c.date_reponse IS NOT NULL
                ORDER BY c.date_reponse DESC
                LIMIT :limit
            ");
            $candStmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
            $candStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $candStmt->execute();
            $candidatures = $candStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($candidatures as &$cand) {
                $cand['type'] = 'candidature_accepted';
                $cand['link'] = 'missiondetails.php?id=' . $cand['mission_id'] . '&candidature_id=' . $cand['notification_id'];
                $cand['is_unread'] = ($cand['vu'] == 0);
                unset($cand['mission_id']);
                unset($cand['vu']);
            }
            $notifications = array_merge($notifications, $candidatures);

            // 3. Rejected candidatures
            $rejStmt = $this->pdo->prepare("
                SELECT 
                    c.id as notification_id,
                    m.titre as title,
                    'Votre candidature a été rejetée' as message,
                    1 as response_count,
                    c.date_reponse as created_at,
                    c.date_reponse as created_at,
                    c.vu,
                    m.id as mission_id
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                WHERE c.utilisateur_id = :uid AND c.statut = 'rejetee' AND c.date_reponse IS NOT NULL
                ORDER BY c.date_reponse DESC
                LIMIT :limit
            ");
            $rejStmt->bindValue(':uid', $user_id, PDO::PARAM_INT);
            $rejStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $rejStmt->execute();
            $rejections = $rejStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rejections as &$rej) {
                $rej['type'] = 'candidature_rejected';
                $rej['link'] = 'missiondetails.php?id=' . $rej['mission_id'] . '&candidature_id=' . $rej['notification_id'];
                $rej['is_unread'] = ($rej['vu'] == 0);
                unset($rej['mission_id']);
                unset($rej['vu']);
            }
            $notifications = array_merge($notifications, $rejections);

            // Sort by created_at descending
            usort($notifications, function($a, $b) {
                return strtotime($b['created_at'] ?? '2000-01-01') - strtotime($a['created_at'] ?? '2000-01-01');
            });

            return array_slice($notifications, 0, $limit);
        } catch (Exception $e) {
            error_log('getUserNotifications error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount($user_id) {
        $user_id = intval($user_id);
        $count = 0;

        try {
            // Count unread responses to reclamations
            $stmt = $this->pdo->prepare("
                SELECT COUNT(DISTINCT r.reclamation_id) as unread_count
                FROM response r
                JOIN reclamation rec ON rec.id = r.reclamation_id
                WHERE rec.utilisateur_id = :uid AND IFNULL(r.vu, 0) = 0
            ");
            $stmt->execute(['uid' => $user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count += $result['unread_count'] ?? 0;

            // Count unread candidatures (accepted/rejected)
            $stmtCand = $this->pdo->prepare("
                SELECT COUNT(*) as unread_count
                FROM candidatures
                WHERE utilisateur_id = :uid 
                AND statut IN ('acceptee', 'rejetee') 
                AND date_reponse IS NOT NULL 
                AND vu = 0
            ");
            $stmtCand->execute(['uid' => $user_id]);
            $resultCand = $stmtCand->fetch(PDO::FETCH_ASSOC);
            $count += $resultCand['unread_count'] ?? 0;
        } catch (Exception $e) {
            error_log('getUnreadCount error: ' . $e->getMessage());
        }

        return $count;
    }

    /**
     * Mark a notification as read (for reclamation responses)
     */
    public function markResponsesAsRead($reclamation_id, $user_id) {
        $reclamation_id = intval($reclamation_id);
        $user_id = intval($user_id);

        try {
            $sql = "UPDATE response r
                    JOIN reclamation rec ON rec.id = r.reclamation_id
                    SET r.vu = 1
                    WHERE r.reclamation_id = :rid AND rec.utilisateur_id = :uid";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['rid' => $reclamation_id, 'uid' => $user_id]);
        } catch (Exception $e) {
            error_log('markResponsesAsRead error: ' . $e->getMessage());
        }
    }

    /**
     * Mark a candidature notification as read
     */
    public function markCandidatureAsRead($candidature_id, $user_id) {
        $candidature_id = intval($candidature_id);
        $user_id = intval($user_id);

        try {
            $sql = "UPDATE candidatures 
                    SET vu = 1 
                    WHERE id = :cid AND utilisateur_id = :uid";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['cid' => $candidature_id, 'uid' => $user_id]);
        } catch (Exception $e) {
            error_log('markCandidatureAsRead error: ' . $e->getMessage());
        }
    }


    /**
     * Get admin notifications (new reclamations and candidatures)
     */
    public function getAdminNotifications($limit = 50) {
        $notifications = [];

        try {
            // 1. New Reclamations (statut = 'En attente')
            $recStmt = $this->pdo->prepare("
                SELECT 
                    id as notification_id,
                    sujet as title,
                    'Nouvelle réclamation reçue' as message,
                    date_creation as created_at,
                    'reclamation_new' as type,
                    'reclamation/listReclamation.php' as link
                FROM reclamation
                WHERE statut = 'Non traite'
                ORDER BY date_creation DESC
                LIMIT :limit
            ");
            $recStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $recStmt->execute();
            $notifications = array_merge($notifications, $recStmt->fetchAll(PDO::FETCH_ASSOC));

            // 2. New Candidatures (statut = 'en_attente')
            $candStmt = $this->pdo->prepare("
                SELECT 
                    c.id as notification_id,
                    m.titre as title,
                    CONCAT('Nouvelle candidature de ', c.pseudo_gaming) as message,
                    c.date_candidature as created_at,
                    'candidature_new' as type,
                    'condidature/listecondidature.php' as link
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                WHERE c.statut = 'en_attente'
                ORDER BY c.date_candidature DESC
                LIMIT :limit
            ");
            $candStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $candStmt->execute();
            $notifications = array_merge($notifications, $candStmt->fetchAll(PDO::FETCH_ASSOC));

            // 3. New Feedbacks
            $feedStmt = $this->pdo->prepare("
                SELECT 
                    f.id as notification_id,
                    CONCAT('Nouveau feedback - ', f.rating, '⭐') as title,
                    SUBSTRING(f.commentaire, 1, 50) as message,
                    f.date_feedback as created_at,
                    'feedback_new' as type,
                    'feedback/feedbackliste.php' as link
                FROM feedback f
                ORDER BY f.date_feedback DESC
                LIMIT :limit
            ");
            $feedStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $feedStmt->execute();
            $notifications = array_merge($notifications, $feedStmt->fetchAll(PDO::FETCH_ASSOC));

            // Sort by created_at descending
            usort($notifications, function($a, $b) {
                return strtotime($b['created_at'] ?? '2000-01-01') - strtotime($a['created_at'] ?? '2000-01-01');
            });

            return array_slice($notifications, 0, $limit);
        } catch (Exception $e) {
            error_log('getAdminNotifications error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get unread notification count for admin
     */
    public function getAdminUnreadCount() {
        $count = 0;

        try {
            // Count new reclamations
            $stmt = $this->pdo->query("SELECT COUNT(*) as cnt FROM reclamation WHERE statut = 'Non traite'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $count += $result['cnt'] ?? 0;

            // Count new candidatures
            $stmtCand = $this->pdo->query("SELECT COUNT(*) as cnt FROM candidatures WHERE statut = 'en_attente'");
            $resultCand = $stmtCand->fetch(PDO::FETCH_ASSOC);
            $count += $resultCand['cnt'] ?? 0;
        } catch (Exception $e) {
            error_log('getAdminUnreadCount error: ' . $e->getMessage());
        }

        return $count;
    }
}
?>
