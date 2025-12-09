<?php
require_once __DIR__ . '/../db_config.php';

class ResponseController {
    private $pdo;
    
    public function __construct() {
        $this->pdo = config::getConnexion();
    }
    
    public function addResponse($reclamation_id, $contenu, $admin_id = null) {
        // Si admin_id n'est pas fourni, utiliser l'ID de session ou une valeur par défaut
        if ($admin_id === null) {
            // Vous pouvez récupérer l'ID de l'admin depuis la session
            // Pour l'instant, on utilise 1 comme valeur par défaut
            $admin_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
        }
        
        $sql = "INSERT INTO response (reclamation_id, contenu, admin_id, date_response) 
                VALUES (:reclamation_id, :contenu, :admin_id, NOW())";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'reclamation_id' => $reclamation_id,
            'contenu' => $contenu,
            'admin_id' => $admin_id
        ]);
        
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
        $sql = "SELECT id, reclamation_id, contenu, date_response, admin_id 
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
}
?>

