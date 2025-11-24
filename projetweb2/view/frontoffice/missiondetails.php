<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../controller/missioncontroller.php';

$missionC = new missioncontroller();

// Vérifier si un id existe dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<h2 style='color:white;text-align:center;margin-top:50px;'>❌ ID de mission manquant</h2>");
}

$id = intval($_GET['id']);
$mission = $missionC->getMissionById($id);

if (!$mission) {
    die("<h2 style='color:white;text-align:center;margin-top:50px;'>❌ Mission introuvable</h2>");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Détails de la mission</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/all.css">
<link rel="stylesheet" href="css/mission.css"> 

</head>

<body style="background:#111; color:white;">
<div id="chatbot-button">💬</div>
<div id="chatbot-box">
    <div id="chatbot-header">ENGAGE Bot</div>
    <div id="chatbot-messages"></div>

    <input type="text" id="chatbot-input" placeholder="Écris ton message...">
</div>

<script src="../js/chatbot.js"></script>

<div class="body_bg">

<header class="main_menu single_page_menu">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="missionlist.php">
                <img src="img/logo.png" alt="logo">
            </a>
        </nav>
    </div>
</header>

<section class="container mt-5 mb-5">

<div class="mission-details-box">

    <h2><?= htmlspecialchars($mission->getTitre()) ?></h2>

    <p><b>🎮 Jeu :</b> <?= htmlspecialchars($mission->getJeu()) ?></p>
    <p><b>🏷 Thème :</b> <?= htmlspecialchars($mission->getTheme()) ?></p>
    <p><b>🔥 Difficulté :</b> <?= htmlspecialchars($mission->getNiveauDifficulte()) ?></p>

    <hr>

    <h4>Description</h4>
    <p><?= nl2br(htmlspecialchars($mission->getDescription() ?? "")) ?></p>

    <h4>Compétences requises</h4>
    <p><?= nl2br(htmlspecialchars($mission->getCompetencesRequises() ?? "")) ?></p>

    <div class="text-center mt-4">
        <a href="addcondidature.php?mission_id=<?= $mission->getId() ?>" 
           class="btn btn-primary btn-lg">
           Postuler maintenant
        </a>
    </div>

</div>

</section>

</div>

<script src="js/jquery-1.12.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>

</body>
</html>
