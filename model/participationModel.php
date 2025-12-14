<?php
require_once __DIR__ . '/../db_config.php';

class ParticipationModel {
    private $conn;

    public function __construct() {
        $this->conn = config::getConnexion();
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
        $query = "SELECT p.*, u.nom, u.prenom, u.mail AS email, u.gamer_tag 
                  FROM participation p 
                  JOIN utilisateur u ON p.id_volontaire = u.id_util 
                  WHERE p.id_evenement = :event_id AND p.statut = 'acceptée'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':event_id' => $event_id]);
        
        return $stmt->fetchAll();
    }

    public function getEventParticipations($event_id) {
        $query = "SELECT p.*, u.nom, u.prenom, u.mail AS email, u.gamer_tag, e.titre as evenement_titre
                  FROM participation p 
                  JOIN utilisateur u ON p.id_volontaire = u.id_util 
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
        $query = "SELECT p.*, e.titre, e.description, e.date_evenement, e.heure_evenement, e.duree_minutes, e.lieu, e.image, e.prix, e.type_evenement, e.id_organisation, e.vues 
                  FROM participation p 
                  JOIN evenement e ON p.id_evenement = e.id_evenement 
                  WHERE p.id_volontaire = :user_id 
                  ORDER BY p.date_participation DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        
        return $stmt->fetchAll();
    }

    public function getAllParticipationsWithUsers() {
        $query = "SELECT p.*, e.titre, e.date_evenement, e.heure_evenement, e.duree_minutes, e.lieu, u.nom, u.prenom, u.mail AS email, e.type_evenement, e.prix
                  FROM participation p
                  JOIN evenement e ON p.id_evenement = e.id_evenement
                  JOIN utilisateur u ON p.id_volontaire = u.id_util
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

    /**
     * Valide les données de participation et paiement
     * 
     * @param array $data Données à valider
     * @return array Tableau contenant 'valid' (bool) et 'errors' (array)
     */
    public function validateParticipation($data) {
        $errors = [];

        // Validation de l'ID d'événement
        if (empty($data['id_evenement']) || !is_numeric($data['id_evenement']) || $data['id_evenement'] <= 0) {
            $errors['id_evenement'] = "ID d'événement invalide";
        }

        // Validation de l'ID du volontaire
        if (empty($data['id_volontaire']) || !is_numeric($data['id_volontaire']) || $data['id_volontaire'] <= 0) {
            $errors['id_volontaire'] = "ID du volontaire invalide";
        }

        // Validation de la date de participation
        if (empty($data['date_participation'])) {
            $errors['date_participation'] = "Date de participation requise";
        } else if (!$this->isValidDate($data['date_participation'])) {
            $errors['date_participation'] = "Format de date invalide (yyyy-MM-dd)";
        } else if (strtotime($data['date_participation']) < strtotime('today')) {
            $errors['date_participation'] = "La date ne peut pas être dans le passé";
        }

        // Validation du statut
        if (empty($data['statut'])) {
            $errors['statut'] = "Statut requis";
        } else if (!$this->isValidStatus($data['statut'])) {
            $errors['statut'] = "Statut invalide. Valeurs autorisées: acceptée, en attente, refusée";
        }

        // Validation de la quantité
        if (isset($data['quantite'])) {
            if (!is_numeric($data['quantite']) || $data['quantite'] < 1 || $data['quantite'] > 100) {
                $errors['quantite'] = "Quantité invalide. Doit être entre 1 et 100";
            }
        }

        // Validation du montant total
        if (isset($data['montant_total']) && $data['montant_total'] !== '' && $data['montant_total'] !== null) {
            if (!is_numeric($data['montant_total']) || $data['montant_total'] < 0) {
                $errors['montant_total'] = "Montant invalide. Doit être un nombre positif";
            }
        }

        // Validation du mode de paiement (si montant > 0)
        if (isset($data['montant_total']) && $data['montant_total'] > 0) {
            if (empty($data['mode_paiement'])) {
                $errors['mode_paiement'] = "Mode de paiement requis";
            } else if (!$this->isValidPaymentMethod($data['mode_paiement'])) {
                $errors['mode_paiement'] = "Mode de paiement invalide. Valeurs autorisées: Carte bancaire, Virement, Espèces, Chèque";
            }
        }

        // Validation de la référence de paiement (si mode de paiement est fourni)
        if (!empty($data['mode_paiement']) && $data['montant_total'] > 0) {
            if (empty($data['reference_paiement'])) {
                $errors['reference_paiement'] = "Référence de paiement requise";
            } else if (strlen($data['reference_paiement']) > 255) {
                $errors['reference_paiement'] = "Référence de paiement trop longue (max 255 caractères)";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Vérifie si une date est au format valide
     * 
     * @param string $date Date à vérifier
     * @return bool True si valide
     */
    private function isValidDate($date) {
        if (!is_string($date)) {
            return false;
        }
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Vérifie si le statut est valide
     * 
     * @param string $status Statut à vérifier
     * @return bool True si valide
     */
    private function isValidStatus($status) {
        $validStatuses = ['acceptée', 'en attente', 'refusée'];
        return in_array(strtolower($status), $validStatuses);
    }

    /**
     * Vérifie si le mode de paiement est valide
     * 
     * @param string $method Mode de paiement à vérifier
     * @return bool True si valide
     */
    private function isValidPaymentMethod($method) {
        $validMethods = ['carte bancaire', 'virement', 'espèces', 'chèque'];
        return in_array(strtolower($method), $validMethods);
    }

    public function getStatistics() {
        // Top 5 viewed events
        $queryViews = "SELECT titre, vues FROM evenement ORDER BY vues DESC LIMIT 5";
        $stmtViews = $this->conn->prepare($queryViews);
        $stmtViews->execute();
        $topViews = $stmtViews->fetchAll(PDO::FETCH_ASSOC);

        // Participants per event (Top 5)
        $queryPart = "SELECT e.titre, COUNT(p.id_participation) as count 
                      FROM evenement e 
                      LEFT JOIN participation p ON e.id_evenement = p.id_evenement 
                      GROUP BY e.id_evenement 
                      ORDER BY count DESC LIMIT 5";
        $stmtPart = $this->conn->prepare($queryPart);
        $stmtPart->execute();
        $topParticipants = $stmtPart->fetchAll(PDO::FETCH_ASSOC);

        // Revenue stats (Total revenue per event)
        $queryRevenue = "SELECT e.titre, SUM(p.montant_total) as total 
                         FROM evenement e 
                         JOIN participation p ON e.id_evenement = p.id_evenement 
                         WHERE p.statut = 'acceptée' 
                         GROUP BY e.id_evenement 
                         ORDER BY total DESC LIMIT 5";
        $stmtRevenue = $this->conn->prepare($queryRevenue);
        $stmtRevenue->execute();
        $revenue = $stmtRevenue->fetchAll(PDO::FETCH_ASSOC);

        return [
            'views' => $topViews,
            'participants' => $topParticipants,
            'revenue' => $revenue
        ];
    }
}
?>