<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure only admin can access dashboard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    header('Location: login.php');
    exit();
}

$base_dir = __DIR__ . '/../../';
require_once $base_dir . 'controller/missioncontroller.php';
require_once $base_dir . 'controller/condidaturecontroller.php';


// Chargement conditionnel du feedbackcontroller
try {
    if (file_exists(__DIR__ . '/../../controller/feedbackcontroller.php')) {
        require_once __DIR__ . '/../../controller/feedbackcontroller.php';
        $feedbackC = new feedbackcontroller();
        $feedbacks = $feedbackC->getAllFeedbacks();
        $totalFeedbacks = is_array($feedbacks) ? count($feedbacks) : 0;
        
        // Calcul sécurisé de la note moyenne
        $averageRating = 0;
        if ($totalFeedbacks > 0 && is_array($feedbacks)) {
            $sumRatings = 0;
            $validFeedbacks = 0;
            foreach ($feedbacks as $feedback) {
                // Vérification sécurisée de l'existence de la clé 'note'
                if (isset($feedback['note']) && is_numeric($feedback['note'])) {
                    $sumRatings += $feedback['note'];
                    $validFeedbacks++;
                }
            }
            if ($validFeedbacks > 0) {
                $averageRating = round($sumRatings / $validFeedbacks, 1);
            }
        }
    } else {
        throw new Exception("Controller feedback non trouvé");
    }
} catch (Exception $e) {
    // Fallback si le controller n'existe pas ou a des erreurs
    $feedbacks = [];
    $totalFeedbacks = 0;
    $averageRating = 0;
    error_log("Feedback controller non chargé: " . $e->getMessage());
}

$missionC = new missioncontroller();
$condC = new condidaturecontroller();

$missions = $missionC->getMissions();
$candidatures = $condC->getAllCondidatures();

/* Résumé rapide */
$totalMissions = count($missions);
$totalCandidatures = count($candidatures);
$lastMission = !empty($missions) ? $missions[0]['titre'] : "Aucune mission";

/* Missions par thème */
$themes = [];
foreach ($missions as $m) {
    if (isset($m['theme'])) {
        $themes[$m['theme']] = ($themes[$m['theme']] ?? 0) + 1;
    }
}

/* Candidatures par mission */
$candParMission = [];
foreach ($candidatures as $c) {
    if (isset($c['mission_titre'])) {
        $candParMission[$c['mission_titre']] = ($candParMission[$c['mission_titre']] ?? 0) + 1;
    }
}

/* Niveaux d'expérience */
$expLevels = [];
foreach ($candidatures as $c) {
    if (isset($c['niveau_experience'])) {
        $expLevels[$c['niveau_experience']] = ($expLevels[$c['niveau_experience']] ?? 0) + 1;
    }
}

/* Feedbacks par note - Calcul sécurisé */
$feedbackRatings = [];
if (isset($feedbacks) && is_array($feedbacks)) {
    foreach ($feedbacks as $f) {
        if (isset($f['note']) && is_numeric($f['note'])) {
            $note = (int)$f['note'];
            $feedbackRatings[$note] = ($feedbackRatings[$note] ?? 0) + 1;
        }
    }
}

// Trier les notes de 1 à 5 pour avoir un affichage cohérent
for ($i = 1; $i <= 5; $i++) {
    if (!isset($feedbackRatings[$i])) {
        $feedbackRatings[$i] = 0;
    }
}
ksort($feedbackRatings);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard – ENGAGE Admin</title>

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/custom-backoffice.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        #contentFrame {
            width: 100%;
            display: none;
            margin-top: 20px;
        }

        #frameDisplay {
            width: 100%;
            height: 900px;
            border: none;
            border-radius: 12px;
            background: #111;
            box-shadow: 0 0 15px rgba(255, 0, 102, 0.4);
        }

        #returnBtn {
            display: none;
            margin-bottom: 20px;
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 600;
            color: white;
            transition: all 0.3s;
        }

        #returnBtn:hover {
            background: linear-gradient(45deg, var(--primary-light), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        }
        
        /* STATISTIQUES BIEN PROPORTIONNÉES */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card-balanced {
            background: var(--accent-color);
            padding: 25px 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .stat-card-balanced:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 74, 87, 0.2);
        }
        
        .stat-icon-balanced {
            font-size: 2.2rem;
            color: var(--primary-color);
            margin-bottom: 12px;
        }
        
        .stat-number-balanced {
            font-size: 2rem;
            font-weight: bold;
            color: var(--text-color);
            display: block;
            line-height: 1.2;
            margin-bottom: 5px;
        }
        
        .stat-label-balanced {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        /* Graphiques bien dimensionnés */
        .chart-container-balanced {
            background: var(--accent-color);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            margin-bottom: 30px;
            height: 350px;
        }
        
        .chart-title-balanced {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 20px;
            text-align: center;
        }
        
        /* Dernière mission */
        .last-mission-balanced {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .last-mission-label-balanced {
            color: var(--text-muted);
            font-size: 1rem;
            margin-bottom: 8px;
        }
        
        .last-mission-value-balanced {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        /* Note moyenne */
        .average-rating {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            text-align: center;
            margin-bottom: 30px;
        }
        
        .rating-stars {
            color: #ffd700;
            font-size: 1.5rem;
            margin: 10px 0;
        }
        
        .rating-value {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        /* Layout équilibré */
        .dashboard-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        
        /* Wrapper pour graphiques */
        .chart-wrapper-balanced {
            height: 250px;
            position: relative;
        }
        
        /* Responsive équilibré */
        @media (max-width: 768px) {
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            
            .stat-card-balanced {
                padding: 20px 15px;
                min-height: 110px;
            }
            
            .stat-icon-balanced {
                font-size: 2rem;
            }
            
            .stat-number-balanced {
                font-size: 1.8rem;
            }
            
            .chart-container-balanced {
                padding: 20px;
                height: 300px;
                margin-bottom: 25px;
            }
            
            .chart-wrapper-balanced {
                height: 220px;
            }
        }
        
        @media (max-width: 480px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .stat-card-balanced {
                min-height: 100px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h3><img src="../img/logo.png" style="height:40px;"> ENGAGE Admin</h3>
    </div>

    <ul class="list-unstyled components">
        <li class="active">
            <a href="#" onclick="showDashboard()"><i class="fas fa-home"></i> Tableau de bord</a>
        </li>

        <li>
            <a href="#gestionMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-cogs"></i> Gestion des missions
            </a>

            <ul class="collapse list-unstyled" id="gestionMenu">
                <li><a href="#" onclick="openPage('mission/missionliste.php')">📌 Missions</a></li>
                <li><a href="#" onclick="openPage('condidature/listecondidature.php')">👥 Candidatures</a></li>
                <li><a href="#" onclick="openPage('feedback/feedbackliste.php')">⭐ Feedbacks</a></li>
                <li><a href="#" onclick="showDashboard(); document.getElementById('stats').scrollIntoView({behavior: 'smooth'});">📊 Statistiques</a></li>
            </ul>
        </li>

        <li>
            <a href="#gestionReclamationMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-exclamation-triangle"></i> Gestion des réclamations
            </a>

            <ul class="collapse list-unstyled" id="gestionReclamationMenu">
                <li><a href="#" onclick="openPage('reclamation/listReclamation.php')">⚠️ Réclamations</a></li>
                <li><a href="#" onclick="openPage('reclamation/statistiques.php')">📊 Statistiques</a></li>
            </ul>
        </li>

        <li>
            <a href="#gestionStoreMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-store"></i> Gestion Store & Partenaires
            </a>

            <ul class="collapse list-unstyled" id="gestionStoreMenu">
                <li><a href="#" onclick="openPage('store/items-list.php')">🕹️ Store</a></li>
                <li><a href="#" onclick="openPage('partenaire/list.php')">🤝 Partenaires</a></li>
                <li><a href="#" onclick="openPage('orders/orders-list.php')">📦 Commandes</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- CONTENU -->
<div id="content">

    <nav class="navbar navbar-expand-lg topbar">
        <button id="sidebarCollapse" class="btn btn-dark">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Admin Notifications -->
        <?php
        require_once __DIR__ . '/../../controller/NotificationController.php';
        $notifCtrl = new NotificationController();
        $adminUnreadCount = $notifCtrl->getAdminUnreadCount();
        $adminNotifications = $notifCtrl->getAdminNotifications(5);
        ?>
        <div class="ml-auto d-flex align-items-center">
            <!-- Notification Container with explicit relative positioning -->
            <div class="position-relative mr-4" style="position: relative;">
                <!-- Toggle Button -->
                <a href="javascript:void(0);" id="customNotifToggle" class="text-white position-relative" style="text-decoration: none; display: inline-block;">
                    <i class="fas fa-bell fa-lg"></i>
                    <?php if ($adminUnreadCount > 0): ?>
                        <span class="badge badge-danger rounded-circle position-absolute" style="top: -8px; right: -8px; font-size: 0.6rem; padding: 4px 6px; border: 2px solid #2d325a;">
                            <?= $adminUnreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Dropdown Menu -->
                <div id="customNotifMenu" style="display: none; position: absolute; top: 140%; right: -10px; width: 350px; max-height: 450px; overflow-y: auto; background-color: #1f2235; border: 1px solid #2d3047; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 10000;">
                    <!-- Header -->
                    <div style="background-color: #ff4a57; padding: 15px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                        <h6 class="m-0 text-white font-weight-bold">
                            <i class="fas fa-bullhorn mr-2"></i> Notifications
                        </h6>
                    </div>

                    <!-- Content -->
                    <div class="p-0">
                        <?php if (empty($adminNotifications)): ?>
                            <div class="text-center py-4 text-muted">
                                <i class="far fa-bell-slash fa-2x mb-2"></i><br>
                                Aucune nouvelle notification
                            </div>
                        <?php else: ?>
                            <?php foreach ($adminNotifications as $notif): ?>
                                <a href="#" onclick="openPage('<?= $notif['link'] ?>'); return false;" style="display: block; padding: 15px; color: #fff; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.backgroundColor='#2d3047'" onmouseout="this.style.backgroundColor='transparent'">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3 mt-1">
                                        <?php if ($notif['type'] == 'reclamation_new'): ?>
                                            <i class="fas fa-exclamation-circle fa-lg text-warning"></i>
                                        <?php elseif ($notif['type'] == 'candidature_new'): ?>
                                            <i class="fas fa-user-plus fa-lg text-info"></i>
                                        <?php elseif ($notif['type'] == 'feedback_new'): ?>
                                            <i class="fas fa-star fa-lg text-warning"></i>
                                        <?php else: ?>
                                            <i class="fas fa-check-circle fa-lg text-success"></i>
                                        <?php endif; ?>
                                    </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 font-weight-bold" style="font-size: 0.95rem;"><?= htmlspecialchars($notif['title']) ?></h6>
                                            <p class="mb-1 text-muted" style="font-size: 0.85rem; line-height: 1.4;"><?= htmlspecialchars($notif['message']) ?></p>
                                            <small class="text-muted" style="font-size: 0.75rem;">
                                                <i class="far fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    <div class="container mt-4">

        <!-- Retour -->
        <button id="returnBtn" onclick="showDashboard()" class="btn btn-primary">
            ⬅ Retour au Dashboard
        </button>

        <!-- DASHBOARD NORMAL -->
        <div id="dashboardSection">

            <h2 class="text-white mb-4 section-title">📊 Tableau de Bord</h2>

            <!-- STATISTIQUES BIEN PROPORTIONNÉES -->
            <div class="dashboard-stats">
                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <span class="stat-number-balanced"><?= $totalMissions ?></span>
                    <span class="stat-label-balanced">Missions</span>
                </div>

                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="stat-number-balanced"><?= $totalCandidatures ?></span>
                    <span class="stat-label-balanced">Candidatures</span>
                </div>

                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-star"></i>
                    </div>
                    <span class="stat-number-balanced"><?= $totalFeedbacks ?></span>
                    <span class="stat-label-balanced">Feedbacks</span>
                </div>

                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-tags"></i>
                    </div>
                    <span class="stat-number-balanced"><?= count($themes) ?></span>
                    <span class="stat-label-balanced">Thèmes</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <!-- Dernière mission -->
                    <div class="last-mission-balanced">
                        <div class="last-mission-label-balanced">🎯 Dernière mission ajoutée</div>
                        <div class="last-mission-value-balanced"><?= htmlspecialchars($lastMission) ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Note moyenne -->
                    <div class="average-rating">
                        <div class="last-mission-label-balanced">⭐ Note moyenne de la plateforme</div>
                        <div class="rating-stars">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <?php if ($i <= floor($averageRating)): ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif ($i == ceil($averageRating) && fmod($averageRating, 1) >= 0.5): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="rating-value"><?= $averageRating ?>/5 (<?= $totalFeedbacks ?> avis)</div>
                    </div>
                </div>
            </div>

            <hr style="border-color:#ff0066; margin: 25px 0;">

            <h2 id="stats" class="text-white mb-4 section-title">📈 Statistiques Détaillées</h2>

            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">📊 Missions par thème</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="missionsThemeChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">📈 Candidatures par mission</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="candidaturesMissionChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">🥧 Niveaux d'expérience</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="niveauExpChart"></canvas>
                        </div>
                    </div>
                </div>

                <?php if ($totalFeedbacks > 0): ?>
                <div class="col-lg-6 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">⭐ Distribution des notes</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="feedbackRatingsChart"></canvas>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- IFRAME pour chargement des pages -->
        <div id="contentFrame">
            <iframe id="frameDisplay"></iframe>
        </div>

    </div>
</div>

<!-- PASSAGE PHP → JS -->


<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- TON JS -->
<script src="assets/js/back.js"></script>
<script src="assets/js/charts.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dashboard init
    showDashboard();

    // Custom Notification Logic
    var toggleBtn = document.getElementById('customNotifToggle');
    var menu = document.getElementById('customNotifMenu');

    if (toggleBtn && menu) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
            } else {
                menu.style.display = 'none';
            }
        });

        // Close on outside click
        document.addEventListener('click', function(e) {
            if (menu.style.display === 'block' && !menu.contains(e.target) && !toggleBtn.contains(e.target)) {
                menu.style.display = 'none';
            }
        });
    } else {
        console.error('Notification elements not found!');
    }
});
</script>

<script>
window.dashboardData = {
    themesLabels: <?= json_encode(array_keys($themes)) ?>,
    themesValues: <?= json_encode(array_values($themes)) ?>,
    candidLabels: <?= json_encode(array_keys($candParMission)) ?>,
    candidValues: <?= json_encode(array_values($candParMission)) ?>,
    expLabels: <?= json_encode(array_keys($expLevels)) ?>,
    expValues: <?= json_encode(array_values($expLevels)) ?>,
    feedbackLabels: <?= json_encode(array_keys($feedbackRatings)) ?>,
    feedbackValues: <?= json_encode(array_values($feedbackRatings)) ?>
};

function openPage(page) {
    document.getElementById("dashboardSection").style.display = "none";
    document.getElementById("contentFrame").style.display = "block";
    document.getElementById("returnBtn").style.display = "inline-block";
    document.getElementById("frameDisplay").src = page;
}

function showDashboard() {
    document.getElementById("dashboardSection").style.display = "block";
    document.getElementById("contentFrame").style.display = "none";
    document.getElementById("returnBtn").style.display = "none";
    document.getElementById("frameDisplay").src = "";
}

// Initialisation au chargement
document.addEventListener('DOMContentLoaded', function() {
    showDashboard();
});
</script>

</body>
</html>