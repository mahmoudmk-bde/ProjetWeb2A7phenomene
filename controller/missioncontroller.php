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
        $sql = "SELECT * FROM missions ORDER BY created_at DESC";
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
    $sql = "INSERT INTO missions 
            (titre, theme, jeu, niveau_difficulte, date_debut, date_fin, description, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

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
        $historiqueC->ajouterHistorique("Admin", "A supprimé la mission ID : " . $id);

    }
}
