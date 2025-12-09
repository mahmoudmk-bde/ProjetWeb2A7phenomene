<?php
require_once __DIR__ . '/../db_config.php';


class missioncontroller
{
    private $db;

    public function __construct()
    {
        $this->db = config::getConnexion();
    }

    // liste des missions
    public function missionliste()
    {
        // In projetweb3.sql, the missions table uses `date_creation` (not `created_at`)
        $sql = "SELECT * FROM missions ORDER BY date_creation DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Liste paginée des missions
    // Retourne ['data' => [...], 'total' => int]
    public function getMissionsPaginated(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        // total
        $countSql = "SELECT COUNT(*) as cnt FROM missions";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute();
        $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['cnt'];

        $sql = "SELECT * FROM missions ORDER BY date_creation DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['data' => $data, 'total' => $total];
    }

    public function getMissions()
    {
        return $this->missionliste();
    }

    public function getMissionById($id)
    {
        $sql = "SELECT * FROM missions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // modif  une mission
    public function updateMission($data)
    {
        $sql = "UPDATE missions 
                SET titre = ?, 
                    theme = ?, 
                    jeu = ?, 
                    niveau_difficulte = ?, 
                    date_debut = ?, 
                    date_fin = ?, 
                    description = ?
                WHERE id = ?";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $data['titre'],
            $data['theme'],
            $data['jeu'],
            $data['niveau_difficulte'],
            $data['date_debut'],
            $data['date_fin'],
            $data['description'],
            $data['id']
        ]);
    }

    // Ajoute  mission 
    public function addMission($data)
{
    // Let MySQL handle `date_creation` default (CURRENT_TIMESTAMP) defined in projetweb3.sql
    // Determine createur_id: prefer explicit value in $data, otherwise use logged user from session
    if (session_status() !== PHP_SESSION_ACTIVE) {
        @session_start();
    }

    $createurId = null;
    if (isset($data['createur_id']) && !empty($data['createur_id'])) {
        $createurId = (int)$data['createur_id'];
    } elseif (isset($_SESSION['user_id'])) {
        $createurId = (int) $_SESSION['user_id'];
    }

    if (!$createurId) {
        throw new Exception('Impossible d\'ajouter la mission : createur_id manquant (utilisateur non connecté)');
    }

    $sql = "INSERT INTO missions 
            (titre, theme, jeu, niveau_difficulte, date_debut, date_fin, description, competences_requises, createur_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        $data['titre'] ?? null,
        $data['theme'] ?? null,
        $data['jeu'] ?? null,
        $data['niveau_difficulte'] ?? null,
        $data['date_debut'] ?? null,
        $data['date_fin'] ?? null,
        $data['description'] ?? null,
        $data['competences_requises'] ?? null,
        $createurId
    ]);

}


    // Supp une mission
    public function deleteMission($id)
    {
        $sql = "DELETE FROM missions WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        // TODO: ajouter un système d'historique si nécessaire
    }

    /**
     * Calcule un score de matching entre un utilisateur et une mission
     * Score: 0-100 basé sur compatibilité niveau d'expérience + jeu préféré + thème
     */
    public function calculateMatchingScore($mission, $userNiveauExperience = null, $userJeuFavori = null, $userThemeFavori = null): int
    {
        $score = 50; // Score de base

        // Mapping des niveaux d'expérience
        $niveauxMap = [
            'débutant' => 1,
            'debutant' => 1,
            'intermédiaire' => 2,
            'intermediaire' => 2,
            'avancé' => 3,
            'avance' => 3,
            'expert' => 4,
        ];

        // Match niveau d'expérience
        if ($userNiveauExperience && isset($mission['niveau_difficulte'])) {
            $userLevel = $niveauxMap[strtolower($userNiveauExperience)] ?? 2;
            $missionLevel = $niveauxMap[strtolower($mission['niveau_difficulte'])] ?? 2;
            
            // Plus proche = meilleur score
            $diff = abs($userLevel - $missionLevel);
            $levelScore = max(0, 25 - ($diff * 8));
            $score += $levelScore;
        }

        // Match jeu préféré
        if ($userJeuFavori && isset($mission['jeu'])) {
            if (stripos($mission['jeu'], $userJeuFavori) !== false || stripos($userJeuFavori, $mission['jeu']) !== false) {
                $score += 20;
            }
        }

        // Match thème
        if ($userThemeFavori && isset($mission['theme'])) {
            if (stripos($mission['theme'], $userThemeFavori) !== false || stripos($userThemeFavori, $mission['theme']) !== false) {
                $score += 15;
            }
        }

        return min(100, $score); // Cap à 100
    }

    /**
     * Récupère les missions avec score de matching pour un utilisateur
     */
    public function getMissionsWithMatching($userId, $page = 1, $perPage = 10): array
    {
        // Récupérer les préférences de l'utilisateur depuis l'historique
        require_once __DIR__ . '/../controller/condidaturecontroller.php';
        $userPrefs = getUserPreferencesFromHistory($userId);

        $offset = ($page - 1) * $perPage;
        $countSql = "SELECT COUNT(*) as cnt FROM missions";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute();
        $total = (int) $countStmt->fetch(PDO::FETCH_ASSOC)['cnt'];

        $sql = "SELECT * FROM missions ORDER BY date_creation DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Ajouter le score de matching à chaque mission
        foreach ($missions as &$mission) {
            $mission['matching_score'] = $this->calculateMatchingScore(
                $mission,
                $userPrefs['niveau_moyen'],
                $userPrefs['jeu_favori'],
                $userPrefs['theme_favori']
            );
        }

        return ['data' => $missions, 'total' => $total];
    }
}
