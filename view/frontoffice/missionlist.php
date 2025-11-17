<?php
require_once __DIR__ . '/../../controller/missioncontroller.php';
$missionC = new missioncontroller();
$missions = $missionC->missionliste();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Missions – ENGAGE</title>

    <link rel="stylesheet" href="css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/all.css">
<link rel="stylesheet" href="css/mission.css"> 

</head>

<body>
<div class="body_bg">
<div id="chatbot-button">💬</div>
<div id="chatbot-box">
    <div id="chatbot-header">ENGAGE Bot</div>
    <div id="chatbot-messages"></div>

    <input type="text" id="chatbot-input" placeholder="Écris ton message...">
</div>

<script src="../js/chatbot.js"></script>


<header class="main_menu single_page_menu">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">

            <a class="navbar-brand" href="missionlist.php">
                <img src="img/logo.png" alt="logo">
            </a>

            <button class="navbar-toggler" data-toggle="collapse" data-target="#navbarSupportedContent">
                <span class="menu_icon"><i class="fas fa-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse main-menu-item justify-content-end" id="navbarSupportedContent">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item active"><a class="nav-link" href="missionlist.php">Home</a></li>
                </ul>
            </div>

        </nav>
    </div>
</header>

<section class="container mt-5 mb-5">
    <div class="row">

        <?php foreach ($missions as $m): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="mission-card">

                <h4><?= htmlspecialchars($m['titre']) ?></h4>
                <p class="mission-theme"><i class="fa fa-tag"></i> <?= htmlspecialchars($m['theme']) ?></p>

                <p class="mission-info">🎮 Jeu : <b><?= htmlspecialchars($m['jeu']) ?></b></p>
                <p class="mission-info">🔥 Difficulté : <b><?= htmlspecialchars($m['niveau_difficulte']) ?></b></p>

                <a href="missiondetails.php?id=<?= $m['id'] ?>" class="btn btn-primary btn-block">
                    Voir la mission
                </a>

            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($missions)): ?>
            <p class="col-12 text-center">Aucune mission disponible.</p>
        <?php endif; ?>

    </div>
</section>

</div>

<script src="js/jquery-1.12.1.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/mission.js"></script><!-- si tu as un js custom -->
</body>
</html>
