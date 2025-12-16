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
require_once $base_dir . 'controller/utilisateurcontroller.php';
require_once $base_dir . 'model/evenementModel.php';
require_once $base_dir . 'model/participationModel.php';
require_once $base_dir . 'controller/NotificationController.php';

// Initialize notifications
$notificationController = new NotificationController();
$notifications = $notificationController->getBackofficeNotifications(20);
$notificationCount = count($notifications);

// Debug: Log if no notifications found
if (empty($notifications)) {
    error_log('DEBUG: No notifications found in backoffice dashboard');
}

// Récupération des infos utilisateur pour le header
$utilisateurC = new UtilisateurController();
$currentUser = $utilisateurC->showUtilisateur($_SESSION['user_id']);
$userImg = $currentUser['img'] ?? null;
$userName = $currentUser['prenom'] . ' ' . $currentUser['nom'];

// Chemin de l'image (attention aux chemins relatifs depuis backoffice vers frontoffice)
$imgPath = '../frontoffice/assets/uploads/profiles/' . $userImg;
$defaultImg = '../frontoffice/assets/img/user_default.png'; // Image par défaut si besoin
if (!file_exists(__DIR__ . '/../../view/frontoffice/assets/uploads/profiles/' . $userImg) || empty($userImg)) {
    // Si l'image n'existe pas physiquement ou est vide, on peut mettre une image par défaut ou gérer l'affichage d'une icône
    $userImg = null; 
}


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
$eventModel = new EvenementModel();
$participationModel = new ParticipationModel();

$missions = $missionC->getMissions();
$candidatures = $condC->getAllCondidatures();
$events = $eventModel->getAllEvents();
foreach ($events as &$ev) {
    // Utilise la méthode du modèle d'événement pour compter
    $ev['participants_count'] = $eventModel->countParticipants($ev['id_evenement']);
}
unset($ev);

/* --- STATS UTILISATEURS (From admin.php) --- */
$utilisateurs = $utilisateurC->listUtilisateurs();
$allUsers = [];
if ($utilisateurs) {
    $allUsers = $utilisateurs->fetchAll(PDO::FETCH_ASSOC);
}
// Stats Variables
$totalUsers = count($allUsers);
$totalAdmins = 0;
$totalClients = 0;
$ageDistribution = ['18-25' => 0, '26-35' => 0, '36-50' => 0, '50+' => 0];
$lastUser = null;

if ($totalUsers > 0) {
    $lastUser = $allUsers[count($allUsers) - 1]; // Assuming last added is last in list
    foreach ($allUsers as $user) {
        // By Type
        if (strtolower($user['typee']) === 'admin') {
            $totalAdmins++;
        } else {
            $totalClients++;
        }
        // By Age
        if (!empty($user['dt_naiss'])) {
            try {
                $dob = new DateTime($user['dt_naiss']);
                $now = new DateTime();
                $age = $now->diff($dob)->y;
                if ($age >= 18 && $age <= 25) $ageDistribution['18-25']++;
                elseif ($age >= 26 && $age <= 35) $ageDistribution['26-35']++;
                elseif ($age >= 36 && $age <= 50) $ageDistribution['36-50']++;
                elseif ($age > 50) $ageDistribution['50+']++;
            } catch (Exception $e) {}
        }
    }
}
$usersLabels = ['Administrateurs', 'Utilisateurs'];
$usersValues = [$totalAdmins, $totalClients];
$ageLabels = array_keys($ageDistribution);
$ageValues = array_values($ageDistribution);
/* ------------------------------------------- */

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
        /* Ensure sidebar is scrollable independently */
        nav#sidebar {
            height: 100vh;
            max-height: 100vh;
            overflow-y: auto !important;
        }

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

        /* Event cards */
        .event-grid .event-card {
            background: var(--accent-color);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 18px;
            height: 100%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.25);
            transition: all 0.2s ease;
        }
        .event-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(0,0,0,0.32);
        }
        .event-card h5 {
            color: var(--text-color);
            font-weight: 700;
        }
        .event-meta {
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .event-badge {
            background: rgba(255,74,87,0.15);
            color: #ff6b6b;
            border: 1px solid rgba(255,74,87,0.4);
            padding: 6px 12px;
            border-radius: 14px;
            font-weight: 600;
        }
        .event-actions .btn {
            border-radius: 10px;
            font-weight: 600;
        }
        .event-stats {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        .pill {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-color);
            padding: 8px 12px;
            border-radius: 12px;
            color: var(--text-color);
            font-size: 0.9rem;
        }
        /* Global bell icon sizing */
        .fas.fa-bell { font-size: 1.9rem !important; }
        /* User Menu Styles */
        .user-menu {
            position: relative;
            display: inline-block;
            margin-right: 20px;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            min-width: 200px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-radius: 8px;
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
            color: var(--primary-color);
            padding-left: 25px;
        }
        
        .user-dropdown a:last-child {
            border-bottom: none;
            color: #dc3545;
        }
        
        .user-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        /* Navbar Specifics to force right alignment */
        .navbar.topbar {
            width: 100%;
            display: flex;
            justify-content: space-between !important; /* Force toggle left, menu right */
            align-items: center;
            padding: 10px 20px;
        }
        
        .user-wrapper:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255,255,255,0.2);
        }
        
        .user-avatar i {
            color: white;
            font-size: 18px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

    </style>
</head>

<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-header">
        <img src="assets/img/logo.png" style="height:120px; width: auto; margin-bottom: 10px; display: block;">
        <h3 style="text-align: center; margin: 0; font-size: 1.1rem; font-weight: 600; color: white;">ENGAGE Admin</h3>
    </div>

    <ul class="list-unstyled components">
        <li class="active">
            <a href="#" onclick="showDashboard()"><i class="fas fa-home"></i> Tableau de bord</a>
        </li>
        <li>
            <a href="#gestionUtil" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-cogs"></i> Gestion des utilisateurs
            </a>
            <ul class="collapse list-unstyled" id="gestionUtil">
                <li><a href="#" onclick="openPage('utilisateur/listeutil.php')">📌 utilisateur</a></li>
                <li><a href="#" onclick="openPage('profile1.php')">👥Profile</a></li>
            </ul>
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

        <!-- Gestion des événements -->
        <li>
            <a href="#gestionEventsMenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                <i class="fas fa-calendar-alt"></i> Gestion des événements
            </a>
            <ul class="collapse list-unstyled" id="gestionEventsMenu">
                <li><a href="#" onclick="openPage('events/evenement.php?embed=1')">🎟️ Événements</a></li>
                <li><a href="#" onclick="openPage('events/createevent.php?embed=1')">➕ Créer événement</a></li>
                <li><a href="#" onclick="openPage('events/participation_history.php?embed=1')">👥 Participations</a></li>
                <li><a href="#" onclick="openPage('events/list_event_feedback.php')">💬 Avis Événements</a></li>
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

        <!-- Notifications Center -->
        <div style="margin-left: auto; margin-right: 20px;">
            <?php 
            if (file_exists(__DIR__ . '/assets/notifications_partial.php')) {
                include __DIR__ . '/assets/notifications_partial.php';
            } else {
                echo '<div style="color: #999;">Notifications unavailable</div>';
            }
            ?>
        </div>

        <!-- User Menu -->
        <div class="user-menu">
            <div class="user-wrapper">
                <span class="user-name"><?= htmlspecialchars($userName) ?></span>
                <div class="user-avatar">
                    <?php if ($userImg): ?>
                        <img src="<?= htmlspecialchars($imgPath) ?>" alt="Profil" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
            </div>
            <div class="user-dropdown">
                <a href="profile.php" onclick="openPage('../frontoffice/profile.php')">
                    <i class="fas fa-user me-2"></i>Mon Profil
                </a>
               
                <a href="connexion.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">

        <!-- Retour -->
        <button id="returnBtn" onclick="showDashboard()" class="btn btn-primary">
            ⬅ Retour au Dashboard
        </button>

        <!-- DASHBOARD NORMAL -->
        <div id="dashboardSection">

            <!-- SECTION DASHBOARD UTILISATEURS (Moved to Top) -->
            <h2 class="text-white mb-4 section-title">👥 Gestion des Utilisateurs</h2>

            <div class="dashboard-stats">
                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced"><i class="fas fa-users"></i></div>
                    <span class="stat-number-balanced"><?= $totalUsers ?></span>
                    <span class="stat-label-balanced">Utilisateurs Totaux</span>
                </div>
                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced"><i class="fas fa-user-shield"></i></div>
                    <span class="stat-number-balanced"><?= $totalAdmins ?></span>
                    <span class="stat-label-balanced">Administrateurs</span>
                </div>
                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced"><i class="fas fa-user"></i></div>
                    <span class="stat-number-balanced"><?= $totalClients ?></span>
                    <span class="stat-label-balanced">Clients / Membres</span>
                </div>
                <div class="stat-card-balanced">
                     <div class="stat-icon-balanced"><i class="fas fa-clock"></i></div>
                     <span class="stat-number-balanced">NEW</span>
                     <span class="stat-label-balanced">Dernier inscrit</span>
                </div>
            </div>

            <div class="row">
                 <div class="col-lg-6 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">📊 Répartition Types</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="userTypeChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">🥧 Tranches d'âge</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="ageChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLEAU UTILISATEURS -->
             

            <hr style="border-color:#ff0066; margin: 40px 0;">

            <h2 class="text-white mb-4 section-title">📊 Tableau de Bord Missions</h2>

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

            <h2 class="text-white mb-3 section-title d-flex align-items-center justify-content-between">
                🎉 Gestion des Événements
                <button class="btn btn-primary" onclick="openPage('events/createevent.php')">
                    <i class="fas fa-plus me-2"></i>Nouvel événement
                </button>
            </h2>

            <div class="row event-grid">
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $ev): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="event-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0"><?= htmlspecialchars($ev['titre']) ?></h5>
                                    <span class="event-badge">
                                        <?= ($ev['type_evenement'] ?? 'gratuit') === 'payant' ? 'Payant' : 'Gratuit' ?>
                                    </span>
                                </div>
                                <div class="event-meta mb-2">
                                    <i class="far fa-calendar-alt mr-2"></i><?= !empty($ev['date_evenement']) ? date('d/m/Y', strtotime($ev['date_evenement'])) : '--/--/----' ?>
                                    <span class="ml-3"><i class="far fa-clock mr-2"></i><?= isset($ev['heure_evenement']) && $ev['heure_evenement'] !== null ? substr($ev['heure_evenement'], 0, 5) : '--:--' ?></span>
                                </div>
                                <div class="event-meta mb-2">
                                    <i class="fas fa-map-marker-alt mr-2"></i><?= htmlspecialchars($ev['lieu'] ?? 'Lieu non défini') ?>
                                </div>
                                <p class="event-meta mb-3" style="color: var(--text-color); opacity: 0.9;">
                                    <?= htmlspecialchars(mb_strimwidth($ev['description'] ?? 'Pas de description', 0, 140, '...')) ?>
                                </p>
                                <div class="event-stats">
                                    <div class="pill"><i class="fas fa-users mr-2"></i><?= $ev['participants_count'] ?? 0 ?> participants</div>
                                    <div class="pill"><i class="fas fa-eye mr-2"></i><?= $ev['vues'] ?? 0 ?> vues</div>
                                    <?php if (($ev['type_evenement'] ?? 'gratuit') === 'payant'): ?>
                                        <div class="pill"><i class="fas fa-ticket-alt mr-2"></i><?= isset($ev['prix']) ? number_format((float)$ev['prix'], 2) . ' TND' : 'Tarif' ?></div>
                                    <?php else: ?>
                                        <div class="pill"><i class="fas fa-ticket-alt mr-2"></i>Gratuit</div>
                                    <?php endif; ?>
                                </div>
                                <div class="event-actions d-flex flex-wrap mt-3" style="gap: 8px;">
                                    <button class="btn btn-sm btn-info" onclick="openPage('events/editevent.php?id=<?= $ev['id_evenement'] ?>')">
                                        <i class="fas fa-edit mr-1"></i>Modifier
                                    </button>
                                    <button class="btn btn-sm btn-secondary" onclick="openPage('events/participation.php?event_id=<?= $ev['id_evenement'] ?>')">
                                        <i class="fas fa-users mr-1"></i>Participations
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="openPage('events/event_feedback.php?event_id=<?= $ev['id_evenement'] ?>')">
                                        <i class="fas fa-comments mr-1"></i>Avis
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="if(confirm('Supprimer cet événement ?')) { openPage('events/evenement.php?action=delete&id=<?= $ev['id_evenement'] ?>'); }">
                                        <i class="fas fa-trash mr-1"></i>Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">👥 Top 5 Participants</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="eventParticipantsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="chart-container-balanced">
                        <div class="chart-title-balanced">💰 Top 5 Revenus</div>
                        <div class="chart-wrapper-balanced">
                            <canvas id="eventRevenueChart"></canvas>
                        </div>
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
<script>
window.dashboardData = {
    themesLabels: <?= json_encode(array_keys($themes)) ?>,
    themesValues: <?= json_encode(array_values($themes)) ?>,
    candidLabels: <?= json_encode(array_keys($candParMission)) ?>,
    candidValues: <?= json_encode(array_values($candParMission)) ?>,
    expLabels: <?= json_encode(array_keys($expLevels)) ?>,
    expValues: <?= json_encode(array_values($expLevels)) ?>,
    feedbackLabels: <?= json_encode(array_keys($feedbackRatings)) ?>,
    feedbackValues: <?= json_encode(array_values($feedbackRatings)) ?>,
    // Users Data
    usersLabels: <?= json_encode($usersLabels) ?>,
    usersValues: <?= json_encode($usersValues) ?>,
    ageLabels: <?= json_encode($ageLabels) ?>,
    ageValues: <?= json_encode($ageValues) ?>
};
</script>

<script>
// Init User Charts
document.addEventListener("DOMContentLoaded", function () {
    // 1. User Type
    const ctxType = document.getElementById('userTypeChart');
    if (ctxType) {
        new Chart(ctxType, {
            type: 'doughnut',
            data: {
                labels: window.dashboardData.usersLabels,
                datasets: [{
                    data: window.dashboardData.usersValues,
                    backgroundColor: ['#ff4a57', '#3498db'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#fff' } }
                }
            }
        });
    }

    // 2. Age
    const ctxAge = document.getElementById('ageChart');
    if (ctxAge) {
        new Chart(ctxAge, {
            type: 'bar',
            data: {
                labels: window.dashboardData.ageLabels,
                datasets: [{
                    label: "Utilisateurs",
                    data: window.dashboardData.ageValues,
                    backgroundColor: '#FFCE56',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true, 
                        ticks: { color: '#fff', stepSize: 1 },
                        grid: { color: '#2d3047' } 
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
});
</script>

<!-- JS pour ONE PAGE -->
<script>
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

    // User Menu Toggle
    const userWrapper = document.querySelector('.user-wrapper');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userWrapper && userDropdown) {
        userWrapper.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function(e) {
            if (!userWrapper.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }

    // --- CHARTS ÉVÉNEMENTS ---
    const eventStats = <?= json_encode($eventStatsResult ?? []) ?>;
    
    if (eventStats && document.getElementById('eventViewsChart')) {
        // 1. Vues
        new Chart(document.getElementById('eventViewsChart'), {
            type: 'bar',
            data: {
                labels: eventStats.views.map(e => e.titre),
                datasets: [{
                    label: 'Vues',
                    data: eventStats.views.map(e => e.vues),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: { legend: { display: false }, title: { display: false } },
                scales: {
                    x: { ticks: { color: 'white' }, grid: { color: '#333' } },
                    y: { ticks: { color: 'white' }, grid: { display: false } }
                }
            }
        });

        // 2. Participants
        new Chart(document.getElementById('eventParticipantsChart'), {
            type: 'bar',
            data: {
                labels: eventStats.participants.map(e => e.titre),
                datasets: [{
                    label: 'Participants',
                    data: eventStats.participants.map(e => e.count),
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { ticks: { color: 'white' }, grid: { color: '#333' } },
                    x: { ticks: { display: false }, grid: { display: false } }
                }
            }
        });

        // 3. Revenus
        new Chart(document.getElementById('eventRevenueChart'), {
            type: 'doughnut',
            data: {
                labels: eventStats.revenue.map(e => e.titre),
                datasets: [{
                    data: eventStats.revenue.map(e => e.total),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: 'white', boxWidth: 10, font: {size: 10} } }
                }
            }
        });
    }
});
</script>

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- TON JS -->
<script src="assets/js/back.js"></script>
<script src="assets/js/charts.js"></script>

</body>
<script>
    // Modal Details Logic
    function openEventDetailsModal(event) {
        // Create modal HTML dynamically
        const modalId = 'eventDetailsModal';
        let modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" style="background-color: #1e1e1e; color: white;">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title">${event.titre}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <img src="../frontoffice/assets/img/events/${event.image}" class="img-fluid rounded mb-3" alt="${event.titre}" onerror="this.src='../frontoffice/assets/img/events/event-default.jpg'">
                                </div>
                                <div class="col-md-7">
                                    <p><strong><i class="fas fa-calendar me-2 text-primary"></i>Date:</strong> ${event.date_evenement} à ${event.heure_evenement}</p>
                                    <p><strong><i class="fas fa-clock me-2 text-primary"></i>Durée:</strong> ${event.duree_minutes} min</p>
                                    <p><strong><i class="fas fa-map-marker-alt me-2 text-primary"></i>Lieu:</strong> ${event.lieu}</p>
                                    <p><strong><i class="fas fa-euro-sign me-2 text-primary"></i>Prix:</strong> ${event.prix > 0 ? event.prix + ' DT' : 'Gratuit'}</p>
                                    <p class="mt-3">${event.description}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Update or append modal
        let existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();
    }
</script>
</html>