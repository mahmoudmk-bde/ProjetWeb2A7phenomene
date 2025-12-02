<?php
require_once __DIR__ . '/../db_config.php';

class condidaturecontroller
{
    public function __construct() {
        // Pas besoin d'instancier un modèle si vous ne l'utilisez pas
    }

    // Ajout condidature adapté à la structure de `candidatures` dans projetweb3.sql
    public function addCondidature(array $data): bool
    {
        try {
            // Vérifier que utilisateur_id est présent mais autoriser la valeur 0
            if (!isset($data['utilisateur_id']) || (empty($data['utilisateur_id']) && $data['utilisateur_id'] !== 0 && $data['utilisateur_id'] !== '0')) {
                error_log("Erreur: ID utilisateur manquant dans les données: " . print_r($data, true));
                throw new Exception("ID utilisateur manquant. Données reçues: " . print_r($data, true));
            }

            $sql = "INSERT INTO candidatures 
                    (utilisateur_id, mission_id, pseudo_gaming, niveau_experience, disponibilites, email, statut)
                    VALUES (:utilisateur_id, :mission_id, :pseudo_gaming, :niveau_experience, :disponibilites, :email, :statut)";

            $db  = config::getConnexion();
            $req = $db->prepare($sql);

            $result = $req->execute([
                ':utilisateur_id'     => $data['utilisateur_id'],
                ':mission_id'         => $data['mission_id'],
                ':pseudo_gaming'     => $data['pseudo_gaming'],
                ':niveau_experience' => $data['niveau_experience'],
                ':disponibilites'    => $data['disponibilites'],
                ':email'             => $data['email'],
                ':statut'            => 'en_attente'
                // CORRECTION : Supprimer le paramètre ':message'
            ]);

            if (!$result) {
                $errorInfo = $req->errorInfo();
                error_log("Erreur SQL: " . print_r($errorInfo, true));
                throw new Exception("Erreur lors de l'insertion en base: " . $errorInfo[2]);
            }

            return true;

        } catch (Exception $e) {
            error_log("Erreur dans addCondidature: " . $e->getMessage());
            throw $e;
        }
    }

    // Toutes les condidatures
    public function getAllCondidatures(): array
    {
        $sql = "SELECT c.*, m.titre AS mission_titre
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                ORDER BY c.id DESC";

        $db  = config::getConnexion();
        $req = $db->prepare($sql);
        $req->execute();

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Candidatures par mission
    public function getCondidaturesByMission($mission_id)
    {
        $sql = "SELECT c.*, m.titre AS mission_titre
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                WHERE c.mission_id = :mission_id
                ORDER BY c.id DESC";

        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([':mission_id' => $mission_id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCandidaturesByEmail($email) {
        $sql = "SELECT * FROM candidatures WHERE email = :email";
        $db = config::getConnexion();
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Titre de la mission
    public function getMissionTitle($mission_id)
    {
        $sql = "SELECT titre FROM missions WHERE id = :mission_id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([':mission_id' => $mission_id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['titre'] : 'Mission inconnue';
    }

    public function updateCandidatureStatus($id, $statut)
    {
        $sql = "UPDATE candidatures SET statut = :statut WHERE id = :id";
        $db = config::getConnexion();
        $req = $db->prepare($sql);
        $req->execute([':statut' => $statut, ':id' => $id]);
    }

    public function deleteCondidature(int $id): void
    {
        $sql = "DELETE FROM candidatures WHERE id = :id";
        $db  = config::getConnexion();
        $req = $db->prepare($sql);
        $req->execute([':id' => $id]);
    }

    public function getCandidatureById($id)
    {
        $sql = "SELECT c.*, m.titre AS mission_titre
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                WHERE c.id = :id";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([':id' => $id]);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour récupérer les candidatures par utilisateur (utilisée dans index1.php)
    public function getCandidaturesByUser($user_id) {
        try {
            $sql = "SELECT c.*, m.titre as titre_mission 
                    FROM candidatures c 
                    LEFT JOIN missions m ON c.mission_id = m.id 
                    WHERE c.utilisateur_id = :user_id 
                    ORDER BY c.date_candidature DESC";
            $db = config::getConnexion();
            $query = $db->prepare($sql);
            $query->execute(['user_id' => $user_id]);
            
            return $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Erreur getCandidaturesByUser: " . $e->getMessage());
            return [];
        }
    }

    // Vérifier si l'utilisateur a déjà postulé à une mission
    public function checkExistingApplication($user_id, $mission_id) {
        try {
            $sql = "SELECT COUNT(*) FROM candidatures WHERE utilisateur_id = :user_id AND mission_id = :mission_id";
            $db = config::getConnexion();
            $query = $db->prepare($sql);
            $query->execute([
                'user_id' => $user_id,
                'mission_id' => $mission_id
            ]);
            
            return $query->fetchColumn() > 0;
            
        } catch (Exception $e) {
            error_log("Erreur checkExistingApplication: " . $e->getMessage());
            return false;
        }
    }
    // Ajouter cette méthode dans la classe condidaturecontroller

// Méthode pour récupérer l'historique détaillé des candidatures
public function getHistoriqueCandidatures($user_id) {
    try {
        $sql = "SELECT c.*, m.titre as titre_mission, m.theme, m.jeu,
                       CASE 
                           WHEN c.statut = 'en_attente' THEN 'Candidature soumise'
                           WHEN c.statut = 'accepte' THEN 'Candidature acceptée'
                           WHEN c.statut = 'refuse' THEN 'Candidature refusée'
                           ELSE 'Statut inconnu'
                       END as action_description
                FROM candidatures c 
                LEFT JOIN missions m ON c.mission_id = m.id 
                WHERE c.utilisateur_id = :user_id 
                ORDER BY c.date_candidature DESC";
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['user_id' => $user_id]);
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (Exception $e) {
        error_log("Erreur getHistoriqueCandidatures: " . $e->getMessage());
        return [];
    }
}
}

// Gestion des requêtes POST/GET
if (isset($_GET['action']) && $_GET['action'] == "update") {
    $cc = new condidaturecontroller();
    $cc->updateCandidatureStatus($_POST['id'], $_POST['statut']);
    header("Location: ../view/backoffice/condidature/listecondidature.php");
    exit;
}
?>