<?php
require_once __DIR__ . '/../db_config.php';


class condidaturecontroller
{
    // Ajouter candidature (appelé depuis le front office)
    public function addCondidature(array $data): void
    {
        $sql = "INSERT INTO candidatures 
                (mission_id, volontaire_id, pseudo_gaming, niveau_experience, disponibilites, email, statut)
                VALUES (:mission_id, :volontaire_id, :pseudo_gaming, :niveau_experience, :disponibilites, :email, :statut)";

        $db  = config::getConnexion();
        $req = $db->prepare($sql);

        $req->execute([
            ':mission_id'        => $data['mission_id'],
            ':volontaire_id'     => null, // pour plus tard si tu ajoutes un vrai user
            ':pseudo_gaming'     => $data['pseudo_gaming'],
            ':niveau_experience' => $data['niveau_experience'],
            ':disponibilites'    => $data['disponibilites'],
            ':email'             => $data['email'],
            ':statut'            => 'en_attente',
        ]);
    }

    // Toutes les candidatures
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

    public function deleteCondidature(int $id): void
    {
        $sql = "DELETE FROM candidatures WHERE id = :id";
        $db  = config::getConnexion();
        $req = $db->prepare($sql);
        $req->execute([':id' => $id]);
    }
}

/**
 * Petit contrôleur HTTP pour le front :
 * le formulaire addcondidature POST peut pointer vers ce fichier.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mission_id'])) {
    $controller = new condidaturecontroller();
    $controller->addCondidature($_POST);

    // Redirection après la candidature
    header("Location: ../view/frontoffice/missiondetails.php?id=" . intval($_POST['mission_id']));
    exit;
}
