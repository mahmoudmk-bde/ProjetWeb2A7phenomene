<?php
require_once __DIR__ . '/../db_config.php';

class EvenementModel {
    private $conn;

    public function __construct() {
        $this->conn = config::getConnexion();
    }

    public function create($titre, $description, $date_evenement, $heure_evenement, $duree_minutes, $lieu, $image, $id_organisation, $type_evenement = 'gratuit', $prix = null) {
        $query = "INSERT INTO evenement (titre, description, date_evenement, heure_evenement, duree_minutes, lieu, image, id_organisation, type_evenement, prix) 
                  VALUES (:titre, :description, :date_evenement, :heure_evenement, :duree_minutes, :lieu, :image, :id_organisation, :type_evenement, :prix)";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':date_evenement' => $date_evenement,
            ':heure_evenement' => $heure_evenement,
            ':duree_minutes' => $duree_minutes,
            ':lieu' => $lieu,
            ':image' => $image,
            ':id_organisation' => $id_organisation,
            ':type_evenement' => $type_evenement,
            ':prix' => $prix
        ]);
    }

    public function getAllEvents() {
        try {
            $query = "SELECT e.* 
                      FROM evenement e 
                      ORDER BY e.date_evenement DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllEvents: " . $e->getMessage());
            return [];
        }
    }
 
    public function getById($id) {
        $query = "SELECT e.* 
                  FROM evenement e 
                  LEFT JOIN participation p ON p.id_evenement = e.id_evenement 
                  WHERE e.id_evenement = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':id' => $id]);
        
        return $stmt->fetch();
    }

    public function getActiveEvents() {
        try {
            $query = "SELECT e.*  
                      FROM evenement e 
                      LEFT JOIN participation p ON p.id_evenement = e.id_evenement 
                      WHERE e.date_evenement >= CURDATE() 
                      ORDER BY e.date_evenement ASC";
        
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
        
            $result = $stmt->fetchAll();
        
            // Debug: vérifier le résultat
            error_log("Événements actifs trouvés: " . count($result));
        
            return $result;
        
        } catch (PDOException $e) {
            error_log("Erreur dans getActiveEvents: " . $e->getMessage());
            return [];
        }
    }

    public function update($id, $titre, $description, $date_evenement, $heure_evenement, $duree_minutes, $lieu, $image, $id_organisation, $type_evenement = 'gratuit', $prix = null) {
        $query = "UPDATE evenement 
        SET titre = :titre, description = :description, date_evenement = :date_evenement, 
                      heure_evenement = :heure_evenement, duree_minutes = :duree_minutes,
                      lieu = :lieu, image = :image, id_organisation = :id_organisation,
                      type_evenement = :type_evenement, prix = :prix
                  WHERE id_evenement = :id_evenement";
        
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([
            ':titre' => $titre,
            ':description' => $description,
            ':date_evenement' => $date_evenement,
            ':heure_evenement' => $heure_evenement,
            ':duree_minutes' => $duree_minutes,
            ':lieu' => $lieu,
            ':image' => $image,
            ':id_organisation' => $id_organisation,
            ':type_evenement' => $type_evenement,
            ':prix' => $prix,
            ':id_evenement' => $id
        ]);
    }

    public function delete($id) {
        // Supprimer d'abord les participations
        $query_participations = "DELETE FROM participation WHERE id_evenement = :id";
        $stmt_participations = $this->conn->prepare($query_participations);
        $stmt_participations->execute([':id' => $id]);
        
        // Puis supprimer l'événement
        $query = "DELETE FROM evenement WHERE id_evenement = :id";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute([':id' => $id]);
    }

    public function countParticipants($event_id) {
        $query = "SELECT COUNT(*) as count FROM participation 
                  WHERE id_evenement = :event_id AND statut = 'acceptée'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':event_id' => $event_id]);
        
        $result = $stmt->fetch();
        return $result['count'];
    }
    private function columnExists($table, $column) {
        try {
            $sql = "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_NAME = :column";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':table' => $table, ':column' => $column]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return isset($row['cnt']) && (int)$row['cnt'] > 0;
        } catch (PDOException $e) {
            // If metadata query fails, assume column doesn't exist to avoid fatal
            error_log('columnExists check failed: ' . $e->getMessage());
            return false;
        }
    }

    public function incrementViews($id) {
        // Safely increment views only if the "vues" column exists
        if ($this->columnExists('evenement', 'vues')) {
            try {
                $query = "UPDATE evenement SET vues = COALESCE(vues, 0) + 1 WHERE id_evenement = :id";
                $stmt = $this->conn->prepare($query);
                return $stmt->execute([':id' => $id]);
            } catch (PDOException $e) {
                error_log('incrementViews failed: ' . $e->getMessage());
                return false;
            }
        }
        // Column missing: no-op to prevent fatal errors
        return false;
    }
}
?>