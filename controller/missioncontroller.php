<?php
require_once __DIR__ . '/../db_config.php';

require_once __DIR__ . '/../model/mission.php';

class missioncontroller
{
    public function missionliste(): array
    {
        $sql = "SELECT * FROM missions ORDER BY id DESC";
        $db  = config::getConnexion();
        $req = $db->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMissionById($id): ?Mission
    {
        $sql = "SELECT * FROM missions WHERE id = :id";
        $db  = config::getConnexion();
        $req = $db->prepare($sql);
        $req->execute([':id' => $id]);
        $row = $req->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Mission(
            $row['id'],
            $row['titre'],
            $row['jeu'],
            $row['theme'],
            $row['niveau_difficulte'],
            $row['description'] ?? null,
            $row['competences_requises'] ?? null
        );
    }

    public function addMission(array $data): void
    {
        $sql = "INSERT INTO missions (titre, jeu, theme, niveau_difficulte, description, competences_requises)
                VALUES (:titre, :jeu, :theme, :niveau_difficulte, :description, :competences_requises)";

        $db  = config::getConnexion();
        $req = $db->prepare($sql);

        $req->execute([
            ':titre'               => $data['titre'],
            ':jeu'                 => $data['jeu'],
            ':theme'               => $data['theme'],
            ':niveau_difficulte'   => $data['niveau_difficulte'],
            ':description'         => $data['description'] ?? null,
            ':competences_requises'=> $data['competences_requises'] ?? null,
        ]);
    }

    public function deletemission(int $id): void
    {
        $sql = "DELETE FROM missions WHERE id = :id";
        $db  = config::getConnexion();
        $req = $db->prepare($sql);
        $req->execute([':id' => $id]);
    }
    public function getMissions()
{
    $sql = "SELECT * FROM missions ORDER BY id DESC";
    $db = config::getConnexion();
    $req = $db->prepare($sql);
    $req->execute();
    return $req->fetchAll();
}

}
