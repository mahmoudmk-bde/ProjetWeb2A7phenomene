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

            // Vérifier si l'utilisateur est banni
            require_once __DIR__ . '/BadWordController.php';
            $badWordController = new BadWordController();
            $banInfo = $badWordController->isUserBanned($data['utilisateur_id']);
            if ($banInfo) {
                throw new Exception("Vous êtes banni jusqu'au " . date('d/m/Y à H:i', strtotime($banInfo['expires_at'])) . ". Raison: " . ($banInfo['reason'] ?? 'Utilisation de mots interdits'));
            }

                // Préparer l'insertion en fonction des colonnes réelles de la table
                $db  = config::getConnexion();

                $colsStmt = $db->query("SHOW COLUMNS FROM candidatures");
                $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);
                $hasCv = in_array('cv', $cols, true);

                if ($hasCv) {
                    $sql = "INSERT INTO candidatures 
                            (utilisateur_id, mission_id, pseudo_gaming, niveau_experience, disponibilites, email, cv, statut)
                            VALUES (:utilisateur_id, :mission_id, :pseudo_gaming, :niveau_experience, :disponibilites, :email, :cv, :statut)";

                    $params = [
                        ':utilisateur_id'     => $data['utilisateur_id'],
                        ':mission_id'         => $data['mission_id'],
                        ':pseudo_gaming'      => $data['pseudo_gaming'],
                        ':niveau_experience'  => $data['niveau_experience'],
                        ':disponibilites'     => $data['disponibilites'],
                        ':email'              => $data['email'],
                        ':cv'                 => isset($data['cv']) ? $data['cv'] : null,
                        ':statut'             => 'en_attente'
                    ];
                } else {
                    // Table ancienne version sans colonne 'cv'
                    $sql = "INSERT INTO candidatures 
                            (utilisateur_id, mission_id, pseudo_gaming, niveau_experience, disponibilites, email, statut)
                            VALUES (:utilisateur_id, :mission_id, :pseudo_gaming, :niveau_experience, :disponibilites, :email, :statut)";

                    $params = [
                        ':utilisateur_id'     => $data['utilisateur_id'],
                        ':mission_id'         => $data['mission_id'],
                        ':pseudo_gaming'      => $data['pseudo_gaming'],
                        ':niveau_experience'  => $data['niveau_experience'],
                        ':disponibilites'     => $data['disponibilites'],
                        ':email'              => $data['email'],
                        ':statut'             => 'en_attente'
                    ];
                }

                $req = $db->prepare($sql);
                $result = $req->execute($params);

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

    // Liste paginée des candidatures
    // Retourne ['data' => [...], 'total' => int]
    public function getCondidaturesPaginated(int $page = 1, int $perPage = 10, $mission_id = null): array
    {
        $offset = ($page - 1) * $perPage;
        $db = config::getConnexion();

        // Construire la requête avec ou sans filtre mission
        $whereClause = $mission_id ? "WHERE c.mission_id = :mission_id" : "";
        $params = [];

        // Total
        $countSql = "SELECT COUNT(*) as cnt FROM candidatures c JOIN missions m ON m.id = c.mission_id $whereClause";
        $countStmt = $db->prepare($countSql);
        if ($mission_id) {
            $params[':mission_id'] = $mission_id;
            $countStmt->execute($params);
        } else {
            $countStmt->execute();
        }
        $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['cnt'];

        // Données paginées
        $sql = "SELECT c.*, m.titre AS mission_titre
                FROM candidatures c
                JOIN missions m ON m.id = c.mission_id
                $whereClause
                ORDER BY c.id DESC 
                LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($sql);
        if ($mission_id) {
            $stmt->bindValue(':mission_id', (int)$mission_id, PDO::PARAM_INT);
        }
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['data' => $data, 'total' => $total];
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
        $sql = "UPDATE candidatures SET statut = :statut, date_reponse = NOW() WHERE id = :id";
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

/**
 * Déduit les préférences d'un utilisateur basées sur son historique de candidatures
 * @param int $userId ID de l'utilisateur
 * @return array Préférences (jeu_favori, theme_favori, niveau_moyen)
 */
function getUserPreferencesFromHistory($userId) {
    require_once __DIR__ . '/../db_config.php';
    
    try {
        $db = config::getConnexion();
        
        // Récupérer toutes les missions auquelles l'utilisateur a postulé
        $sql = "SELECT m.jeu, m.theme, m.niveau_difficulte 
                FROM candidatures c
                JOIN missions m ON c.mission_id = m.id
                WHERE c.utilisateur_id = :user_id
                ORDER BY c.date_candidature DESC
                LIMIT 10"; // dernières 10 candidatures
        
        $stmt = $db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $candidatures = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($candidatures)) {
            // Pas d'historique = préférences vides
            return [
                'jeu_favori' => null,
                'theme_favori' => null,
                'niveau_moyen' => null
            ];
        }
        
        // Compter les occurrences des jeux et thèmes
        $jeuCount = [];
        $themeCount = [];
        $niveaux = [];
        
        foreach ($candidatures as $cand) {
            $jeu = strtolower(trim($cand['jeu']));
            $theme = strtolower(trim($cand['theme']));
            $niveau = strtolower(trim($cand['niveau_difficulte']));
            
            $jeuCount[$jeu] = ($jeuCount[$jeu] ?? 0) + 1;
            $themeCount[$theme] = ($themeCount[$theme] ?? 0) + 1;
            $niveaux[] = $niveau;
        }
        
        // Récupérer le plus fréquent (jeu et thème)
        arsort($jeuCount);
        arsort($themeCount);
        
        $jeuFavori = key($jeuCount);
        $themeFavori = key($themeCount);
        
        // Niveau moyen (prendre le plus fréquent ou la médiane)
        $niveauFavori = null;
        if (!empty($niveaux)) {
            $niveauCount = array_count_values($niveaux);
            arsort($niveauCount);
            $niveauFavori = key($niveauCount);
        }
        
        return [
            'jeu_favori' => $jeuFavori,
            'theme_favori' => $themeFavori,
            'niveau_moyen' => $niveauFavori
        ];
        
    } catch (Exception $e) {
        error_log("Erreur getUserPreferencesFromHistory: " . $e->getMessage());
        return [
            'jeu_favori' => null,
            'theme_favori' => null,
            'niveau_moyen' => null
        ];
    }
}
?>