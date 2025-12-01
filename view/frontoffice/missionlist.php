<?php
session_start();
require_once __DIR__ . '/../../controller/missioncontroller.php';
$missionC = new missioncontroller();
$missions = $missionC->missionliste();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Missions – ENGAGE</title>
    <link rel="icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mission.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
    <style>
        :root {
            --primary: #ff4a57;
            --primary-light: #ff6b6b;
            --dark: #1f2235;
            --dark-light: #2d325a;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.8);
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        .body_bg {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            min-height: 100vh;
        }

        /* Header Styles */
        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 220px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 12px;
            z-index: 1000;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .user-dropdown.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        .user-dropdown a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .user-dropdown a:hover {
            background: #f8f9fa;
            color: var(--primary);
            transform: translateX(5px);
        }
        
        .user-dropdown a:last-child {
            border-bottom: none;
            color: var(--danger);
        }
        
        .user-dropdown a:last-child:hover {
            background: var(--danger);
            color: white;
        }
        
        .user-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .user-wrapper:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255,255,255,0.3);
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            border-color: rgba(255,255,255,0.6);
            transform: scale(1.05);
        }
        
        .user-avatar i {
            color: white;
            font-size: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="body_bg">
    <?php include 'header_mission.php'; ?>

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
