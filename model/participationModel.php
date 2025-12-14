<?php
require_once __DIR__ . '/../config.php';

class ParticipationModel {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function create($id_evenement, $id_volontaire, $date_participation, $statut, $quantite = 1, $montant_total = null, $mode_paiement = null, $reference_paiement = null) {
        // Vérifier si déjà inscrit
        $checkQuery = "SELECT id_participation FROM participation 
                      WHERE id_evenement = :id_evenement AND id_volontaire = :id_volontaire";
        
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([
            ':id_evenement' => $id_evenement,
            ':id_volontaire' => $id_volontaire
        ]);
        
        if ($checkStmt->fetch()) {
            return false; // Déjà inscrit
        }

        $query = "INSERT INTO participation (id_evenement, id_volontaire, date_participation, statut, quantite, montant_total, mode_paiement, reference_paiement) 
                  VALUES (:id_evenement, :id_volontaire, :date_participation, :statut, :quantite, :montant_total, :mode_paiement, :reference_paiement)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':id_evenement' => $id_evenement,
            ':id_volontaire' => $id_volontaire,
            ':date_participation' => $date_participation,
            ':statut' => $statut,
            ':quantite' => $quantite,
            ':montant_total' => $montant_total,
            ':mode_paiement' => $mode_paiement,
            ':reference_paiement' => $reference_paiement
        ]);
    }

    public function getEventParticipants($event_id) {
        $query = "SELECT p.*, u.nom, u.prenom, u.email, u.gamer_tag 
                  FROM participation p 
                  JOIN utilisateur u ON p.id_volontaire = u.id_utilisateur 
                  WHERE p.id_evenement = :event_id AND p.statut = 'acceptée'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':event_id' => $event_id]);
        
        return $stmt->fetchAll();
    }

    public function getEventParticipations($event_id) {
        $query = "SELECT p.*, u.nom, u.prenom, u.email, u.gamer_tag, e.titre as evenement_titre
                  FROM participation p 
                  JOIN utilisateur u ON p.id_volontaire = u.id_utilisateur 
                  JOIN evenement e ON p.id_evenement = e.id_evenement 
                  WHERE p.id_evenement = :event_id 
                  ORDER BY p.date_participation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':event_id' => $event_id]);
        
        return $stmt->fetchAll();
    }

    // public function getEventParticipations($event_id) {
    //     $query = "SELECT p.*, e.titre as evenement_titre
    //               FROM participation p 
    //               JOIN evenement e ON p.id_evenement = e.id_evenement 
    //               WHERE p.id_evenement = :event_id 
    //               ORDER BY p.date_participation DESC";
        
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->execute([':event_id' => $event_id]);
        
    //     return $stmt->fetchAll();
    // }

    public function updateStatus($participation_id, $statut) {
        $query = "UPDATE participation SET statut = :statut WHERE id_participation = :id_participation";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':statut' => $statut,
            ':id_participation' => $participation_id
        ]);
    }

    public function getUserParticipations($user_id) {
        $query = "SELECT p.*, e.titre, e.description, e.date_evenement, e.heure_evenement, e.duree_minutes, e.lieu, e.image, e.prix, e.type_evenement, e.id_organisation 
                  FROM participation p 
                  JOIN evenement e ON p.id_evenement = e.id_evenement 
                  WHERE p.id_volontaire = :user_id 
                  ORDER BY p.date_participation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        
        return $stmt->fetchAll();
    }

    public function getAllParticipationsWithUsers() {
        $query = "SELECT p.*, e.titre, e.date_evenement, e.heure_evenement, e.duree_minutes, e.lieu, u.nom, u.prenom, u.email, e.type_evenement, e.prix
                  FROM participation p
                  JOIN evenement e ON p.id_evenement = e.id_evenement
                  JOIN utilisateur u ON p.id_volontaire = u.id_utilisateur
                  ORDER BY p.date_participation DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function isUserRegistered($user_id, $event_id) {
        $query = "SELECT id_participation FROM participation 
                  WHERE id_volontaire = :user_id AND id_evenement = :event_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':event_id' => $event_id
        ]);
        
        return $stmt->fetch() !== false;
    }
}
?>