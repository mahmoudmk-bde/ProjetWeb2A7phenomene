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
    $sql = "INSERT INTO missions 
            (titre, theme, jeu, niveau_difficulte, date_debut, date_fin, description)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
        $data['titre'],
        $data['theme'],
        $data['jeu'],
        $data['niveau_difficulte'],
        $data['date_debut'],
        $data['date_fin'],
        $data['description']
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
}
