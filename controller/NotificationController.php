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
}
?>
