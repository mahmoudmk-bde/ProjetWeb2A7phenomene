<?php
require_once __DIR__ . '/../db_config.php';

class ResponseController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = config::getConnexion();
        // Ensure the 'vu' column exists in the response table so we can track seen/unseen
        $this->ensureVuColumnExists();
    }

    /**
     * Ensure the `vu` column exists in `response` table. If missing, try to add it.
     */
    private function ensureVuColumnExists() {
        try {
            $dbNameStmt = $this->pdo->query('SELECT DATABASE() AS dbname');
            $dbName = $dbNameStmt->fetchColumn();

            $colCheck = $this->pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'response' AND COLUMN_NAME = 'vu'");
            $colCheck->execute(['db' => $dbName]);
            $exists = (int)$colCheck->fetchColumn();

            if ($exists === 0) {
                // Add a tinyint flag to mark whether the response has been viewed by the user
                $this->pdo->exec("ALTER TABLE response ADD COLUMN vu TINYINT(1) NOT NULL DEFAULT 0 AFTER date_response");
            }
        } catch (Exception $e) {
            // If we cannot modify schema (permissions), silently ignore — code will fallback gracefully
            error_log('ensureVuColumnExists error: ' . $e->getMessage());
        }
    }
    
    public function addResponse($reclamation_id, $contenu, $admin_id = null) {
        // Si admin_id n'est pas fourni, utiliser l'ID de session ou une valeur par défaut
        if ($admin_id === null) {
            // Vous pouvez récupérer l'ID de l'admin depuis la session
            // Pour l'instant, on utilise 1 comme valeur par défaut
            $admin_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        }
        
        // Insert response and mark it as unseen (vu = 0) for the user
        $sql = "INSERT INTO response (reclamation_id, contenu, admin_id, date_response, vu) 
                VALUES (:reclamation_id, :contenu, :admin_id, NOW(), 0)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'reclamation_id' => $reclamation_id,
                'contenu' => $contenu,
                'admin_id' => $admin_id
            ]);
        } catch (PDOException $ex) {
            // Fallback if the `vu` column does not exist or other DB error: try the original insert
            try {
                $fallback = "INSERT INTO response (reclamation_id, contenu, admin_id, date_response) 
                            VALUES (:reclamation_id, :contenu, :admin_id, NOW())";
                $stmt = $this->pdo->prepare($fallback);
                $stmt->execute([
                    'reclamation_id' => $reclamation_id,
                    'contenu' => $contenu,
                    'admin_id' => $admin_id
                ]);
            } catch (Exception $e) {
                error_log('Response insert fallback failed: ' . $e->getMessage());
            }
        }
        
        // Mettre à jour le statut de la réclamation à "Traite"
        $updateSql = "UPDATE reclamation SET statut = 'Traite' WHERE id = :id";
        $updateStmt = $this->pdo->prepare($updateSql);
        $updateStmt->execute(['id' => $reclamation_id]);

        // --- Envoi d'email de notification à l'auteur de la réclamation ---
        try {
            // Récupérer les informations de la réclamation (email, sujet, description)
            $recStmt = $this->pdo->prepare("SELECT id, sujet, description, email, utilisateur_id FROM reclamation WHERE id = :id");
            $recStmt->execute(['id' => $reclamation_id]);
            $reclamation = $recStmt->fetch(PDO::FETCH_ASSOC);

            if ($reclamation) {
                // Déterminer l'email destinataire
                $to = null;
                if (!empty($reclamation['email'])) {
                    $to = $reclamation['email'];
                } elseif (!empty($reclamation['utilisateur_id'])) {
                    // Tenter de récupérer l'email depuis la table utilisateur
                    $userStmt = $this->pdo->prepare("SELECT mail FROM utilisateur WHERE id_util = :id_util");
                    $userStmt->execute(['id_util' => $reclamation['utilisateur_id']]);
                    $u = $userStmt->fetch(PDO::FETCH_ASSOC);
                    if ($u && !empty($u['mail'])) {
                        $to = $u['mail'];
                    }
                }

                if ($to) {
                    // Construire le lien vers la page d'historique (localhost)
                    $link = 'http://localhost/projetweb2/view/frontoffice/historique_reclamations.php?reclamation_id=' . urlencode($reclamation_id);

                        // Ensure proper UTF-8 handling for subjects/content
                        $safeSujet = htmlspecialchars($reclamation['sujet'] ?? 'Votre réclamation', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $safeDescription = htmlspecialchars($reclamation['description'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $safeResponse = htmlspecialchars($contenu ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                        $subject = "Réponse à votre réclamation : " . $safeSujet;

                        $body = "<html><body>" .
                            "<p>Bonjour,</p>" .
                            "<p>Une réponse a été publiée pour votre réclamation.<br/><strong>Sujet :</strong> " . $safeSujet . "</p>" .
                            "<p><strong>Contenu de la réclamation :</strong><br/>" . nl2br($safeDescription) . "</p>" .
                            "<p><strong>Réponse :</strong><br/>" . nl2br($safeResponse) . "</p>" .
                            "<p>Vous pouvez consulter la conversation ici : <a href=\"" . $link . "\">Voir ma réclamation</a></p>" .
                            "<p>Cordialement,<br/>L'équipe ENGAGE</p>" .
                            "</body></html>";

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: ENGAGE <no-reply@localhost>' . "\r\n";

                    // Envoyer l'email via PHPMailer (SMTP)
                    try {
                        require_once __DIR__ . '/../src/PHPMailer.php';
                        require_once __DIR__ . '/../src/SMTP.php';
                        require_once __DIR__ . '/../src/Exception.php';

                        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                        // SMTP configuration (Mailtrap sandbox)
                        $mail->isSMTP();
                        $mail->Host = 'sandbox.smtp.mailtrap.io';
                        $mail->SMTPAuth = true;
                        $mail->Username = '79d6d2d93d19f4';
                        $mail->Password = 'c4cbd8fa425936';
                        // Use TLS if available
                        $mail->SMTPSecure = 'tls';
                        $mail->Port = 2525;

                        $mail->setFrom('no-reply@engage.local', 'ENGAGE');
                        $mail->addAddress($to);
                        // Force UTF-8 charset and use base64 encoding for safe transport
                        $mail->CharSet = 'UTF-8';
                        $mail->Encoding = 'base64';
                        $mail->isHTML(true);
                        $mail->Subject = $subject;
                        $mail->Body = $body;
                        $mail->AltBody = strip_tags(str_replace(['<br/>','<br>'], "\n", $body));

                        $mail->send();
                    } catch (\PHPMailer\PHPMailer\Exception $e) {
                        error_log('PHPMailer error: ' . $e->getMessage());
                    }
                }
            }
        } catch (Exception $e) {
            // Ne pas interrompre le flux en cas d'erreur d'email
            error_log('Erreur envoi mail notification response: ' . $e->getMessage());
        }
    }
    
    public function getResponses($reclamation_id) {
        $sql = "SELECT id, reclamation_id, contenu, date_response, admin_id, IFNULL(vu,0) AS vu 
            FROM response 
            WHERE reclamation_id = :reclamation_id 
            ORDER BY date_response DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['reclamation_id' => $reclamation_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function deleteResponse($id) {
        $stmt = $this->pdo->prepare("DELETE FROM response WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    /**
     * Mark responses as seen (vu = 1) for a reclamation owned by a specific user.
     * This prevents marking other users' responses.
     */
    public function markResponsesSeenByReclamation($reclamation_id, $user_id) {
        try {
            $sql = "UPDATE response r
                    JOIN reclamation rec ON rec.id = r.reclamation_id
                    SET r.vu = 1
                    WHERE r.reclamation_id = :rid AND rec.utilisateur_id = :uid";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['rid' => $reclamation_id, 'uid' => $user_id]);
        } catch (Exception $e) {
            error_log('markResponsesSeenByReclamation error: ' . $e->getMessage());
        }
    }
}
?>

