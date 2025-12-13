<?php
session_start();
require_once __DIR__ . '/../../controller/LikeController.php';

$likeController = new LikeController();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamification – ENGAGE</title>
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
        }

        .body_bg {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .breadcrumb_bg {
            background: linear-gradient(135deg, rgba(255, 74, 87, 0.1) 0%, rgba(255, 107, 107, 0.1) 100%);
            padding: 80px 0 50px;
            margin-bottom: 50px;
        }

        .design-title {
            color: #fff;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .gamification-content {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            color: #fff;
            text-align: center;
        }

        .gamification-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }

        .gamification-content h2 {
            color: #fff;
            margin-bottom: 20px;
        }

        .gamification-content p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            line-height: 1.8;
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
                <h1 class="design-title">🎮 Gamification</h1>
            </div>
        </div>
    </section>

    <!-- CONTENT -->
    <section class="section_padding">
        <div class="container">
            <div class="gamification-content">
                <div class="gamification-icon">🏆</div>
                <h2>Bienvenue dans la section Gamification !</h2>
                <p>
                    Cette section vous permet de gagner des points, des badges et des récompenses en participant aux missions, 
                    en complétant des défis et en interagissant avec la communauté. 
                    Explorez le store, participez aux événements et débloquez des récompenses exclusives !
                </p>
                <p style="margin-top: 30px; font-size: 0.9rem; color: rgba(255, 255, 255, 0.6);">
                    ⚠️ Cette section est en cours de développement. Revenez bientôt pour découvrir toutes les fonctionnalités !
                </p>
            </div>
        </div>
    </section>
</div>

<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
</body>
</html>

