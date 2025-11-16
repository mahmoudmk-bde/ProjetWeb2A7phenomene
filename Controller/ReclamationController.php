<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Model/Reclamation.php';

class ReclamationController {

    private $pdo; // <---- FIX: declare pdo

    public function __construct() {
        // <---- FIX: load DB connection once
        $this->pdo = config::getConnexion();
    }

    // Add a new reclamation
    public function addReclamation(Reclamation $rec) {
        $sql = "INSERT INTO reclamation (sujet, description, email, statut) 
                VALUES (:sujet, :description, :email, :statut)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'sujet' => $rec->getSujet(),
            'description' => $rec->getDescription(),
            'email' => $rec->getEmail(),
            'statut' => $rec->getStatut()
        ]);
    }

    // List all reclamations
    public function listReclamations() {
        $sql = "SELECT id, sujet, description, email, date_creation, statut 
                FROM reclamation";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Delete a reclamation
    public function deleteReclamation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reclamation WHERE id=:id");
        $stmt->execute(['id' => $id]);
    }

    // Get a specific reclamation
    public function getReclamation($id) {
        $sql = "SELECT id, sujet, description, email, date_creation, statut 
                FROM reclamation 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
