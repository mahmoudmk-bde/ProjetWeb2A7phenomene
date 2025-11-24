<?php
require_once __DIR__ . '/../db_config.php';

class condidaturecontroller
{
    public function __construct() {
        // Pas besoin d'instancier un modèle si vous ne l'utilisez pas
    }

    // Ajout condidature (VERSION CORRIGÉE sans colonne message)
    public function addCondidature(array $data): bool
    {
        try {
            // Vérifier que id_util est présent mais autoriser la valeur 0
            if (!isset($data['id_util']) || (empty($data['id_util']) && $data['id_util'] !== 0 && $data['id_util'] !== '0')) {
                error_log("Erreur: ID utilisateur manquant dans les données: " . print_r($data, true));
                throw new Exception("ID utilisateur manquant. Données reçues: " . print_r($data, true));
            }

            // CORRECTION : Supprimer la colonne 'message' qui n'existe pas dans la table
            $sql = "INSERT INTO candidatures 
                    (id_util, id_mission, pseudo_gaming, niveau_experience, disponibilites, email, statut)
                    VALUES (:id_util, :id_mission, :pseudo_gaming, :niveau_experience, :disponibilites, :email, :statut)";

            $db  = config::getConnexion();
            $req = $db->prepare($sql);

            $result = $req->execute([
                ':id_util'            => $data['id_util'],
                ':id_mission'         => $data['id_mission'],
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
                JOIN missions m ON m.id = c.id_mission
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
                JOIN missions m ON m.id = c.id_mission
                WHERE c.id_mission = :mission_id
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
                JOIN missions m ON m.id = c.id_mission
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
                    LEFT JOIN missions m ON c.id_mission = m.id 
                    WHERE c.id_util = :user_id 
                    ORDER BY c.date_soumission DESC";
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
            $sql = "SELECT COUNT(*) FROM candidatures WHERE id_util = :user_id AND id_mission = :mission_id";
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
                LEFT JOIN missions m ON c.id_mission = m.id 
                WHERE c.id_util = :user_id 
                ORDER BY c.date_soumission DESC";
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