<?php
session_start();
require_once __DIR__ . '/../../controller/missioncontroller.php';
require_once __DIR__ . '/../../controller/LikeController.php';

$missionC = new missioncontroller();
$likeController = new LikeController();

// Pagination avec matching intelligent
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 2; // afficher 2 missions par page
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Récupérer les missions avec scores de matching
if ($userId) {
    $pag = $missionC->getMissionsWithMatching($userId, $page, $perPage);
} else {
    $pag = $missionC->getMissionsPaginated($page, $perPage);
}

$missions = $pag['data'];
$totalMissions = $pag['total'];
$totalPages = (int) ceil($totalMissions / $perPage);

// Récupérer les likes pour toutes les missions en une fois (optimisation)
$missionIds = array_column($missions, 'id');
$likesCount = $likeController->getLikesCountForMissions($missionIds);
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

        /* Style pour le compteur de likes (style Facebook) */
        .mission-likes-count {
            transition: all 0.3s ease;
        }

        .mission-likes-count:hover {
            background: rgba(255, 74, 87, 0.2) !important;
            transform: translateX(3px);
        }

        .mission-likes-count i {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
    </style>
    <style>
        /* Pagination styles matching the front template */
        .pagination .page-item .page-link {
            color: #ff4a57;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            padding: 8px 12px;
            margin: 0 4px;
            border-radius: 8px;
            min-width: 44px;
            text-align: center;
            transition: all 0.15s ease;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%);
            color: #fff;
            border-color: rgba(255,255,255,0.12);
            box-shadow: 0 6px 18px rgba(255,74,87,0.18);
        }

        .pagination .page-item .page-link:hover {
            transform: translateY(-3px);
            background: rgba(255,255,255,0.12);
            color: #fff;
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.45;
            pointer-events: none;
            transform: none;
        }

        @media (max-width: 576px) {
            .pagination .page-item .page-link { padding: 6px 8px; min-width: 36px; margin: 0 2px; }
        }

        /* Matching Score Badge */
        .matching-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            margin-top: 8px;
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .matching-excellent {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.2) 0%, rgba(34, 197, 94, 0.2) 100%);
            border: 1px solid #28a745;
            color: #28a745;
        }

        .matching-good {
            background: linear-gradient(135deg, rgba(76, 175, 80, 0.2) 0%, rgba(67, 160, 71, 0.2) 100%);
            border: 1px solid #66bb6a;
            color: #66bb6a;
        }

        .matching-fair {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 152, 0, 0.2) 100%);
            border: 1px solid #ffc107;
            color: #ffc107;
        }

        .matching-low {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2) 0%, rgba(244, 67, 54, 0.2) 100%);
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        .matching-icon {
            font-size: 1rem;
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
                    <span><i class="fas fa-rocket"></i> <strong><?= $totalMissions ?></strong> Missions Actives</span>
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

                        <!-- NOMBRE DE LIKES (style Facebook) -->
                        <div class="mission-likes-count" style="display: flex; align-items: center; gap: 8px; margin: 15px 0; padding: 10px; background: rgba(255, 74, 87, 0.1); border-radius: 8px; border-left: 3px solid #ff4a57;">
                            <i class="fas fa-heart" style="color: #ff4a57; font-size: 1.1rem;"></i>
                            <span style="color: rgba(255,255,255,0.9); font-weight: 600; font-size: 0.95rem;">
                                <?= isset($likesCount[$m['id']]) ? number_format($likesCount[$m['id']], 0, ',', ' ') : '0' ?> 
                                <?= isset($likesCount[$m['id']]) && $likesCount[$m['id']] > 1 ? 'personnes aiment' : 'personne aime' ?> cette mission
                            </span>
                        </div>

                        <!-- MATCHING SCORE BADGE -->
                        <?php if (isset($m['matching_score'])): ?>
                            <?php 
                                $score = $m['matching_score'];
                                if ($score >= 75) {
                                    $badge_class = 'matching-excellent';
                                    $badge_text = '🎯 Excellent Match';
                                } elseif ($score >= 60) {
                                    $badge_class = 'matching-good';
                                    $badge_text = '✅ Bon Match';
                                } elseif ($score >= 45) {
                                    $badge_class = 'matching-fair';
                                    $badge_text = '⚠️ Acceptable';
                                } else {
                                    $badge_class = 'matching-low';
                                    $badge_text = '❌ Peu Compatible';
                                }
                            ?>
                            <div class="matching-badge <?= $badge_class ?>">
                                <span class="matching-icon"><?= substr($badge_text, 0, 2) ?></span>
                                <span><?= $badge_text ?> (<?= $score ?>%)</span>
                            </div>
                        <?php endif; ?>

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

            <!-- Pagination controls -->
            <?php if ($totalPages > 1): ?>
            <div class="text-center mt-4">
                <nav aria-label="Pagination">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= max(1, $page-1) ?>" aria-label="Précédent">&laquo;</a>
                        </li>
                        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                            <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= min($totalPages, $page+1) ?>" aria-label="Suivant">&raquo;</a>
                        </li>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </section>

<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/chatbot.js"></script>

</body>
</html>
