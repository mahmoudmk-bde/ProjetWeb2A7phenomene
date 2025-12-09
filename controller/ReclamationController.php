<?php
// Utiliser la configuration de connexion existante du projet
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../model/Reclamation.php';

class ReclamationController {
    private $pdo; 
    public function __construct() {
        $this->pdo = config::getConnexion();
    }
    public function addReclamation(Reclamation $rec) {
        // Adapter à la structure de la table `reclamation` de projetweb3.sql
        // Les champs date_creation et updated_at ont des valeurs par défaut dans la BDD
        $sql = "INSERT INTO reclamation (sujet, description, email, statut, utilisateur_id, product_id, priorite) 
                VALUES (:sujet, :description, :email, :statut, :utilisateur_id, :product_id, :priorite)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'sujet' => $rec->getSujet(),
            'description' => $rec->getDescription(),
            'email' => $rec->getEmail(),
            'statut' => $rec->getStatut(),
            'utilisateur_id' => $rec->getUtilisateurId(),
            'product_id' => $rec->getProductId(),
            'priorite' => $rec->getPriorite()
        ]);
    }
    public function listReclamations() {
        $sql = "SELECT id, sujet, description, email, date_creation, statut, priorite 
                FROM reclamation 
                ORDER BY date_creation DESC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les réclamations d'un utilisateur connecté
    public function getReclamationsByUser($user_id) {
        $sql = "SELECT id, sujet, description, email, date_creation, statut, priorite
                FROM reclamation
                WHERE utilisateur_id = :user_id
                ORDER BY date_creation DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteReclamation($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reclamation WHERE id=:id");
        $stmt->execute(['id' => $id]);
    }
    public function getReclamation($id) {
        $sql = "SELECT id, sujet, description, email, date_creation, statut, priorite, utilisateur_id
                FROM reclamation 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
