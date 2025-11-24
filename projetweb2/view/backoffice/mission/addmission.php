<?php
require_once __DIR__ . '/../../../controller/missioncontroller.php';
$missionC = new missioncontroller();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $missionC->addMission($_POST);
    header('Location: missionliste.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter une mission</title>

    <link rel="stylesheet" href="../assets/css/back.css">
    <link rel="stylesheet" href="../frontoffice/css/bootstrap.min.css">

    <link rel="stylesheet" href="../../frontoffice/css/all.css">
</head>

<body>

<nav id="sidebar">
    <div class="sidebar-header">
        <h3><img src="../../img/logo.png" style="height:40px;"> ENGAGE Admin</h3>
    </div>
    <ul class="list-unstyled components">
        <li><a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li class="active"><a href="missionliste.php"><i class="fas fa-tasks"></i> Missions</a></li>
        <li><a href="../condidature/listecondidature.php"><i class="fas fa-users"></i> Candidatures</a></li>
    </ul>
</nav>

<div id="content">

<nav class="navbar navbar-expand-lg topbar">
    <button id="sidebarCollapse" class="btn btn-dark"><i class="fas fa-bars"></i></button>
</nav>

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
