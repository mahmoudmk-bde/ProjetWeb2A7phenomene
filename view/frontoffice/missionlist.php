<?php
session_start();
// Pour les pages qui nécessitent une connexion, ajoutez :
// require_once 'auth_check.php';
// checkAuth(); // Décommentez si la page nécessite une connexion
?>
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

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/mission.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
    
</head>

<body>
<div class="body_bg">

    <!-- Chatbot -->
<!-- Chatbot - Version simplifiée -->
<div id="chatbot-button">💬</div>
<div id="chatbot-box">
    <div id="chatbot-header">
        ENGAGE Bot 
        <span id="chatbot-close">×</span>
    </div>
    <div id="chatbot-messages">
        <div class="chatbot-message bot-message">
            👋 Salut ! Je suis ENGAGE Bot. Tape ton message et clique sur le bouton ou appuie sur Entrée !
        </div>
    </div>
    <div id="chatbot-input-container">
        <input type="text" id="chatbot-input" placeholder="Tape ton message ici...">
        <button type="button" id="chatbot-send" style="cursor: pointer;">➤</button>
    </div>
</div>

    <!-- Header -->
    <header class="main_menu single_page_menu">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="index.php">
                    <img src="img/logo.png" alt="logo" style="height:45px;">
                </a>

                <button class="navbar-toggler" type="button" data-toggle="collapse" 
                        data-target="#navbarSupportedContent">
                    <span class="menu_icon"><i class="fas fa-bars"></i></span>
                </button>
                

                <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link active" href="missionlist.php">Missions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Gamification</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Réclamations</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Événements</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Quizzes</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <a href="login.php" class="btn_1 d-none d-sm-block">Se connecter</a>
            </nav>
            

        </div>
    </header>

    <!-- BREADCRUMB + HERO -->
    <section class="breadcrumb_bg">
        <div class="container">
            <div class="breadcrumb_iner_item text-center">
                <h1 class="design-title">🚀 Nos Missions</h1>
                <p class="design-subtitle">Transformez votre passion gaming en aventure solidaire</p>

                <!-- Stats -->
                <div class="stats-panel" style="margin-top: 30px;">
                    <span><i class="fas fa-rocket"></i> <strong><?= count($missions) ?></strong> Missions Actives</span>
                    <span><i class="fas fa-users"></i> Communauté Engagée</span>
                    <span><i class="fas fa-trophy"></i> Récompenses Uniques</span>
                    <span><i class="fas fa-heart"></i> Impact Social</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION MISSIONS -->
    <section class="section_padding">
        <div class="container">

            <div class="mission-grid-enhanced">
            <?php if (!empty($missions)): ?>
                <?php foreach ($missions as $m): ?>

                <?php
                // Normalisation du thème
                $theme = strtolower(trim($m['theme']));

                // Images PNG selon le thème
                $images = [
                    "sport" => "sport.png",
                    "football" => "tournoi.png",
                    "fifa" => "tournoi.png",
                    "éducation" => "education.png",
                    "education" => "education.png",
                    "esport" => "valorant.png",
                    "valorant" => "valorant.png",
                    "minecraft" => "minecraft.png",
                    "création" => "roblox.png",
                    "creation" => "roblox.png",
                    "prévention" => "sante.png",
                    "prevention" => "sante.png",
                    "coaching" => "coaching.png",
                    "compétition" => "cyber.png",
                    "competition" => "cyber.png",
                ];

                $image = $images[$theme] ?? "default.png";
                $imagePath = "assets/img/" . $image;
                ?>

                <div class="mission-card-enhanced">

                    <!-- IMAGE DU THÈME -->
                    <div class="game-card-img">
                        <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($m['theme']) ?>">
                    </div>

                    <!-- CONTENU -->
                    <div class="game-card-body">

                        <h3 class="game-title"><?= htmlspecialchars($m['titre']) ?></h3>

                        <div class="game-info">
                            <i class="fas fa-tag"></i>
                            <span><?= htmlspecialchars($m['theme']) ?></span>
                        </div>

                        <div class="game-info">
                            <i class="fas fa-gamepad"></i>
                            <span><?= htmlspecialchars($m['jeu']) ?></span>
                        </div>

                        <div class="game-info">
                            <i class="fas fa-fire"></i>
                            <span><?= htmlspecialchars($m['niveau_difficulte']) ?></span>
                        </div>

                        <!-- BOUTON FONCTIONNEL -->
                        <a href="missiondetails.php?id=<?= $m['id'] ?>" 
                           class="btn-view-game mt-3 mission-link">
                           <i class="fas fa-eye"></i> Voir la Mission
                          

                        </a>

                    </div>
                </div>

                <?php endforeach; ?>
            <?php else: ?>
                <h3 style="color:white;">Aucune mission disponible</h3>
            <?php endif; ?>

            </div>

        </div>
    </section>

<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/chatbot.js"></script>

</body>
</html>
