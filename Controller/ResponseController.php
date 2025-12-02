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

