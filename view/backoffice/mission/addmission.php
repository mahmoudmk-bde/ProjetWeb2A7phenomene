<?php
session_start();   // 🔥 AJOUT OBLIGATOIRE

require_once __DIR__ . '/../../../controller/missioncontroller.php';
$missionC = new missioncontroller();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $missionC->addMission($_POST);

    // 🔥 Message de succès
    $_SESSION['success'] = "🎉 Mission ajoutée avec succès !";

    header('Location: missionliste.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter une mission</title>

    <!--<link rel="stylesheet" href="../assets/css/back.css">-->
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="../frontoffice/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../frontoffice/css/all.css">
</head>

<body>
<div class="container">

<div class="form-card">

    <h2>Ajouter une nouvelle mission</h2>

    <form method="POST">

        <div class="form-group-modern">
            <input type="text" name="titre" placeholder=" " required>
            <label>Titre de la mission</label>
        </div>

        <div class="form-group-modern">
            <input type="text" name="jeu" placeholder=" " required>
            <label>Jeu utilisé</label>
        </div>
        
        <div class="form-group-modern">
            <input type="text" name="theme" placeholder=" " required>
            <label>Thème</label>
        </div>

        <div class="form-group-modern">
    <label style="position:static; display:block; margin-bottom:5px; color:#ccc; font-size:14px;">
        Niveau de difficulté
    </label>
    <select name="niveau_difficulte" style="padding:10px 15px; border-radius:12px; width:100%; background:#111; color:#fff; border:1px solid #333;" required>
        <option value="facile">Facile</option>
        <option value="moyen">Moyen</option>
        <option value="difficile">Difficile</option>
    </select>
    <input type="date" name="date_debut" required>
<input type="date" name="date_fin" required>

</div>


        <div class="form-group-modern">
            <textarea name="description" placeholder=" " rows="4"></textarea>
            <label>Description</label>
        </div>

        <div class="form-group-modern">
            <textarea name="competences_requises" placeholder=" " rows="3"></textarea>
            <label>Compétences requises</label>
        </div>

        <button class="btn-submit">Ajouter la mission</button>

    </form>

</div>

</div>

</div>

<script src="../assets/js/back.js"></script>
</body>
</html>
