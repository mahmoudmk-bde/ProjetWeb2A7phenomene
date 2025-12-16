<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure only admin can access dashboard
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
     header('Location: connexion.php');
     exit();
}

$base_dir = __DIR__ . '/../../';
require_once $base_dir . 'controller/utilisateurcontroller.php';

$utilisateurC = new UtilisateurController();
$utilisateurs = $utilisateurC->listUtilisateurs();
$allUsers = [];
if ($utilisateurs) {
    $allUsers = $utilisateurs->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les infos de l'admin connecté pour sa photo de profil
$currentUser = $utilisateurC->showUtilisateur($_SESSION['user_id']);
$adminImg = $currentUser['img'] ?? '';
$adminImgPath = 'assets/uploads/profiles/' . $adminImg;
if (!file_exists(__DIR__ . '/' . $adminImgPath) || empty($adminImg)) {
    $adminImgPath = 'assets/img/default_avatar.jpg';
}

// Calcul des statistiques
$totalUsers = count($allUsers);
$totalAdmins = 0;
$totalClients = 0;
$ageDistribution = [
    '18-25' => 0,
    '26-35' => 0,
    '36-50' => 0,
    '50+' => 0
];
$lastUser = null;

if ($totalUsers > 0) {
    // Supposant que le dernier ajouté est à la fin du tableau
    $lastUser = $allUsers[count($allUsers) - 1]; 
    
    foreach ($allUsers as $user) {
        // Stats par Type
        if (strtolower($user['typee']) === 'admin') {
            $totalAdmins++;
        } else {
            $totalClients++;
        }
        
        // Stats par Age
        if (!empty($user['dt_naiss'])) {
            try {
                $dob = new DateTime($user['dt_naiss']);
                $now = new DateTime();
                $age = $now->diff($dob)->y;
                
                if ($age >= 18 && $age <= 25) $ageDistribution['18-25']++;
                elseif ($age >= 26 && $age <= 35) $ageDistribution['26-35']++;
                elseif ($age >= 36 && $age <= 50) $ageDistribution['36-50']++;
                elseif ($age > 50) $ageDistribution['50+']++;
            } catch (Exception $e) {
                // Ignore invalid dates
            }
        }
    }
}

// Pour les graphs JS
$usersLabels = ['Administrateurs', 'Utilisateurs'];
$usersValues = [$totalAdmins, $totalClients];

$ageLabels = array_keys($ageDistribution);
$ageValues = array_values($ageDistribution);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Utilisateurs – ENGAGE Admin</title>

    <!-- CSS -->
    <!-- Using style.css as custom-backoffice.css is missing -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #ff4a57;
            --primary-light: #ff6b7a;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --text-muted: #b0b3c1;
            --border-color: #2d3047;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #151828;
            color: var(--text-color);
            margin: 0;
            overflow-x: hidden;
        }

        #contentFrame {
            width: 100%;
            display: none;
            margin-top: 20px;
        }

        #dashboardSection {
            display: block;
        }
        
        /* LAYOUT & SIDEBAR */
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: var(--secondary-color);
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 999;
        }

        #sidebar.active {
            margin-left: -250px;
        }

        .sidebar-header {
            padding: 20px;
            background: var(--secondary-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-header img {
            height: 40px; 
            filter: brightness(0) invert(1);
        }

        #sidebar ul.components {
            padding: 20px 0;
        }

        #sidebar ul li a {
            padding: 15px 20px;
            font-size: 1.1em;
            display: block;
            color: var(--text-color);
            text-decoration: none;
            transition: 0.3s;
        }

        #sidebar ul li a:hover {
            color: var(--primary-color);
            background: var(--accent-color);
            padding-left: 25px;
        }

        #sidebar ul li.active > a {
            color: #fff;
            background: var(--primary-color);
        }
        
        /* Content Wrapper */
        #content {
            width: calc(100% - 250px);
            padding: 20px;
            min-height: 100vh;
            transition: all 0.3s;
            position: absolute;
            top: 0;
            right: 0;
        }
        
        #content.active {
            width: 100%;
        }

        /* Nav Bar */
        .topbar {
            background: var(--accent-color);
            border-bottom: 1px solid var(--border-color);
            padding: 10px 20px; 
            margin-bottom: 30px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* HEADER PROFILE STYLES - IMPORTED FROM PROFILE.CSS */
        .user-menu {
            position: relative;
            display: inline-block;
        }

        .user-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-color);
            padding: 8px 15px;
            border-radius: 25px;
            background: var(--accent-color);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .user-wrapper:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.3);
        }

        .user-name {
            font-weight: 600;
            font-size: 14px;
            margin-right: 0 !important; /* Reset inline overrides */
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            overflow: hidden; /* Ensure image stays inside */
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-wrapper:hover .user-avatar {
            border-color: white;
            transform: scale(1.1);
        }

        /* STATISTIQUES BIEN PROPORTIONNÉES - STYLE DEMANDÉ */
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
            border-color: var(--primary-color);
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
        
        /* Note moyenne / Last Item styles */
        .last-mission-balanced {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            text-align: center;
            margin-bottom: 30px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
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
        
        /* Layout équilibré */
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: var(--text-color);
        }
        
        .chart-wrapper-balanced {
            height: 250px;
            position: relative;
        }

        /* TABLEAU STYLE */
        .table-section {
            background: var(--accent-color);
            padding: 25px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text-color);
        }

        .data-table th {
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid var(--border-color);
            color: var(--primary-color);
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .badge-role {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .role-admin { background: rgba(255, 74, 87, 0.2); color: var(--primary-color); border: 1px solid var(--primary-color); }
        .role-user { background: rgba(46, 204, 113, 0.2); color: #2ecc71; border: 1px solid #2ecc71; }
        
        .btn-action {
             margin-right: 5px;
             color: #fff;
             padding: 5px 10px;
             border-radius: 5px;
        }
        
        .btn-edit { background: #3498db; }
        .btn-delete { background: #e74c3c; }

        /* Responsive équilibré */
        @media (max-width: 768px) {
            #sidebar { margin-left: -250px; }
            #sidebar.active { margin-left: 0; }
            #content { width: 100%; }
            
            .dashboard-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .stat-card-balanced {
                padding: 20px 15px;
                min-height: 110px;
            }
            .data-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        @media (max-width: 480px) {
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h3><img src="assets/img/logo.png" alt="ENGAGE"> ENGAGE Admin</h3>
    </div>

    <ul class="list-unstyled components">
        <li class="active">
            <a href="admin.php"><i class="fas fa-home"></i> Tableau de bord</a>
        </li>
        <li>
            <a href="settings.php"><i class="fas fa-cog"></i> Paramètres</a>
        </li>
        
        <!-- Only Dashboard and Logout as requested -->
        
        <li>
            <a href="connexion.php?action=logout" style="margin-top:20px; border-top:1px solid #2d3047;">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </li>
    </ul>
</nav>

<!-- CONTENU -->
<div id="content">

    <nav class="navbar navbar-expand-lg topbar">
        <button id="sidebarCollapse" class="btn btn-dark" style="background:var(--secondary-color); color:#fff; border:1px solid var(--border-color);">
            <i class="fas fa-bars"></i> Menu
        </button>
        
        <!-- User Menu in Header -->
        <div class="user-menu" style="position: relative; cursor: pointer;">
             <div class="user-wrapper d-flex align-items-center" onclick="toggleUserMenu()">
                 <span class="user-name" style="color: #fff; font-weight:500; margin-right: 10px;"><?php echo htmlspecialchars($_SESSION['user_name']); ?> <i class="fas fa-chevron-down small ml-1"></i></span>
                 <div class="user-avatar" style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden;">
                     <img src="<?php echo htmlspecialchars($adminImgPath); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                 </div>
             </div>
             <div class="user-dropdown" id="userDropdown" style="display:none; position:absolute; right:0; top:45px; background:var(--accent-color); border:1px solid var(--border-color); border-radius:5px; min-width:150px; z-index:1000; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
                 <a href="settings.php" style="display:block; padding:10px; color:#fff; text-decoration:none; border-bottom:1px solid var(--border-color);"><i class="fas fa-cog mr-2"></i> Paramètres</a>
                 <a href="connexion.php?action=logout" style="display:block; padding:10px; color:#ff4a57; text-decoration:none;"><i class="fas fa-sign-out-alt mr-2"></i> Déconnexion</a>
             </div>
        </div>
    </nav>

    <div class="container-fluid">

        <!-- DASHBOARD SECTION -->
        <div id="dashboardSection">

            <h2 class="section-title">📊 Tableau de Bord Utilisateurs</h2>

            <!-- STATISTIQUES BIEN PROPORTIONNÉES -->
            <div class="dashboard-stats">
                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="stat-number-balanced"><?= $totalUsers ?></span>
                    <span class="stat-label-balanced">Utilisateurs Totaux</span>
                </div>

                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <span class="stat-number-balanced"><?= $totalAdmins ?></span>
                    <span class="stat-label-balanced">Administrateurs</span>
                </div>

                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="stat-number-balanced"><?= $totalClients ?></span>
                    <span class="stat-label-balanced">Clients / Membres</span>
                </div>

                <div class="stat-card-balanced">
                    <div class="stat-icon-balanced">
                        <i class="fas fa-clock"></i>
                    </div>
                    <span class="stat-number-balanced">NEW</span>
                    <span class="stat-label-balanced">Dernier inscrit</span>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <!-- Dernier Utilisateur -->
                    <div class="last-mission-balanced">
                        <div class="last-mission-label-balanced">🎯 Dernier inscrit sur la plateforme</div>
                        <div class="last-mission-value-balanced">
                            <?php if ($lastUser): ?>
                                <div class="d-flex align-items-center justify-content-center mt-2">
                                    <?php if (!empty($lastUser['img'])): ?>
                                        <img src="<?= htmlspecialchars($lastUser['img']) ?>" style="width:40px;height:40px;border-radius:50%; margin-right:10px; object-fit:cover;">
                                    <?php endif; ?>
                                    <?= htmlspecialchars($lastUser['prenom'] . ' ' . $lastUser['nom']) ?>
                                </div>
                            <?php else: ?>
                                Aucun utilisateur
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Info Admin -->
                    <div class="last-mission-balanced">
                        <div class="last-mission-label-balanced">👤 Connecté en tant que</div>
                        <div class="last-mission-value-balanced d-flex align-items-center justify-content-center mt-2">
                            <div style="width: 50px; height: 50px; border-radius: 50%; overflow: hidden; margin-right: 15px; border: 2px solid var(--primary-color);">
                                <img src="<?php echo htmlspecialchars($adminImgPath); ?>" alt="Admin" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div>
                                <div><?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Admin' ?></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal;">Administrateur</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h2 class="section-title">📈 Statistiques Détaillées</h2>

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
             <h2 class="section-title">👥 Liste des Utilisateurs</h2>
             <div class="table-section">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Utilisateur</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Rôle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($allUsers) > 0): ?>
                                <?php foreach($allUsers as $u): ?>
                                <tr>
                                    <td>#<?= $u['id_util'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php 
                                            // Construct path
                                            $userImgPath = 'assets/img/default_avatar.jpg'; // default
                                            if (!empty($u['img'])) {
                                                $potentialPath = 'assets/uploads/profiles/' . $u['img'];
                                                if (file_exists(__DIR__ . '/' . $potentialPath)) {
                                                    $userImgPath = $potentialPath;
                                                }
                                            }
                                            ?>
                                            <div style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden; margin-right: 10px; border: 1px solid var(--border-color); flex-shrink: 0;">
                                                <img src="<?= htmlspecialchars($userImgPath) ?>" alt="User" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($u['mail']) ?></td>
                                    <td><?= htmlspecialchars($u['num']) ?></td>
                                    <td>
                                        <span class="badge-role <?= strtolower($u['typee'])=='admin'?'role-admin':'role-user' ?>">
                                            <?= htmlspecialchars($u['typee']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="updateUtilisateur.php?id=<?= $u['id_util'] ?>" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
                                        <a href="deleteUtilisateur.php?id=<?= $u['id_util'] ?>" class="btn-action btn-delete" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="6" style="text-align:center;">Aucun utilisateur.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
             </div>

        </div>

    </div>
</div>

<!-- DATA JS -->
<script>
window.dashboardData = {
    usersLabels: <?= json_encode($usersLabels) ?>,
    usersValues: <?= json_encode($usersValues) ?>,
    ageLabels: <?= json_encode($ageLabels) ?>,
    ageValues: <?= json_encode($ageValues) ?>
};
</script>

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Toggle Menu
function toggleUserMenu() {
    var menu = document.getElementById('userDropdown');
    if (menu.style.display === 'none' || menu.style.display === '') {
        menu.style.display = 'block';
    } else {
        menu.style.display = 'none';
    }
}

// Close menu on click outside
document.addEventListener('click', function(event) {
    var wrapper = document.querySelector('.user-wrapper');
    var menu = document.getElementById('userDropdown');
    if (wrapper && !wrapper.contains(event.target) && menu) {
        menu.style.display = 'none';
    }
});

// Sidebar Toggle
$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        $('#content').toggleClass('active');
    });
});

// Charts
document.addEventListener("DOMContentLoaded", function () {
    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'];

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

</body>
</html>