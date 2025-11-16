<?php
require_once __DIR__ . '/../config/config.php';


class ResponseController {

    private $pdo;

    public function __construct() {
        $this->pdo = config::getConnexion();
    }

    // Add response to response table and mark reclamation as 'Traité'
    public function addResponse($reclamation_id, $contenu) {
        // Insert response
        $sql = "INSERT INTO response (reclamation_id, contenu) VALUES (:rid, :contenu)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'rid' => $reclamation_id,
            'contenu' => $contenu
        ]);

        // Update reclamation status
        $sql2 = "UPDATE reclamation SET statut='Traité' WHERE id=:id";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute(['id' => $reclamation_id]);
    }

    // Get all responses for a reclamation
    public function getResponses($reclamation_id) {
        $sql = "SELECT * FROM response WHERE reclamation_id=:rid ORDER BY date_response ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['rid' => $reclamation_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
