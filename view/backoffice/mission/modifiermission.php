<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../controller/missioncontroller.php';

$missionC = new missioncontroller();

// 1) Si le formulaire est soumis (POST) → on met à jour puis on redirige
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'id'                => $_POST['id'],
        'titre'             => $_POST['titre'],
        'theme'             => $_POST['theme'],
        'jeu'               => $_POST['jeu'],
        'niveau_difficulte' => $_POST['niveau_difficulte'],
        'date_debut'        => $_POST['date_debut'],
        'date_fin'          => $_POST['date_fin'],
        'description'       => $_POST['description'],
    ];

    $missionC->updateMission($data);

    header('Location: missionliste.php');
    exit;
}

// 2) Sinon (GET) → on récupère la mission à partir de l'id
$id = $_GET['id'] ?? null;

if (!$id) {
    die("ID de mission manquant.");
}

$mission = $missionC->getMissionById($id);

if (!$mission) {
    die("Mission introuvable.");
}

// IMPORTANT : on s'assure que les dates sont au format yyyy-mm-dd
$dateDebut = '';
$dateFin   = '';

if (!empty($mission['date_debut'])) {
    $dateDebut = date('Y-m-d', strtotime($mission['date_debut']));
}
if (!empty($mission['date_fin'])) {
    $dateFin = date('Y-m-d', strtotime($mission['date_fin']));
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier mission</title>

    <!--<link rel="stylesheet" href="../assets/css/back.css">-->
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
</head>

<body>
<div class="edit-card">

    <h2 class="edit-title">Modifier la mission</h2>

    <form method="POST" class="edit-form">

        <input type="hidden" name="id" value="<?= htmlspecialchars($mission['id']) ?>">

        <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($mission['titre']) ?>">
        </div>

        <div class="form-group">
            <label>Thème</label>
            <input type="text" name="theme" value="<?= htmlspecialchars($mission['theme']) ?>">
        </div>

        <div class="form-group">
            <label>Jeu</label>
            <input type="text" name="jeu" value="<?= htmlspecialchars($mission['jeu']) ?>">
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label>Difficulté</label>
                <select name="niveau_difficulte">
                    <option value="facile"    <?= $mission['niveau_difficulte']=="facile"?"selected":"" ?>>Facile</option>
                    <option value="moyen"     <?= $mission['niveau_difficulte']=="moyen"?"selected":"" ?>>Moyen</option>
                    <option value="difficile" <?= $mission['niveau_difficulte']=="difficile"?"selected":"" ?>>Difficile</option>
                </select>
            </div>

            <div class="form-group">
                <label>Date de début</label>
                <input type="date" name="date_debut" value="<?= htmlspecialchars($dateDebut) ?>">
            </div>

            <div class="form-group">
                <label>Date de fin</label>
                <input type="date" name="date_fin" value="<?= htmlspecialchars($dateFin) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5"><?= htmlspecialchars($mission['description']) ?></textarea>
        </div>

        <button type="submit" class="btn-save-pro">💾 Mettre à jour</button>

    </form>

</div>

    </div>
</div>

</body>
</html>
