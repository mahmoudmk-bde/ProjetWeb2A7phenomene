<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../controller/missioncontroller.php';
require_once __DIR__ . '/../../controller/condidaturecontroller.php';

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
    $themes[$m['theme']] = ($themes[$m['theme']] ?? 0) + 1;
}

/* Candidatures par mission */
$candParMission = [];
foreach ($candidatures as $c) {
    $candParMission[$c['mission_titre']] = ($candParMission[$c['mission_titre']] ?? 0) + 1;
}

/* Niveaux d'expérience */
$expLevels = [];
foreach ($candidatures as $c) {
    $expLevels[$c['niveau_experience']] = ($expLevels[$c['niveau_experience']] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dashboard – ENGAGE Admin</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../frontoffice/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/back.css">

    <!-- Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            display:none;
            margin-bottom: 20px;
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
                <li><a href="#stats">📊 Statistiques</a></li>
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
    </nav>

    <div class="container mt-4">

        <!-- Retour -->
        <button id="returnBtn" onclick="showDashboard()" class="btn btn-primary">
            ⬅ Retour au Dashboard
        </button>

        <!-- DASHBOARD NORMAL -->
        <div id="dashboardSection">

            <h2 class="text-white mb-4">📌 Résumé rapide</h2>

            <div class="row mb-4">

                <div class="col-lg-4">
                    <div class="card-box p-3">
                        <h4 class="text-white">✔ Total Missions</h4>
                        <p class="text-info"><?= $totalMissions ?></p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-box p-3">
                        <h4 class="text-white">✔ Total Candidatures</h4>
                        <p class="text-info"><?= $totalCandidatures ?></p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card-box p-3">
                        <h4 class="text-white">✔ Dernière mission ajoutée</h4>
                        <p class="text-info"><?= $lastMission ?></p>
                    </div>
                </div>

            </div>

            <hr style="border-color:#ff0066;">

            <h2 id="stats" class="text-white mb-4">📊 Statistiques détaillées</h2>

            <div class="row">

                <div class="col-lg-6 mb-5">
                    <div class="card-box p-4">
                        <h4 class="text-white">📊 Missions par thème</h4>
                        <canvas id="missionsThemeChart"></canvas>
                    </div>
                </div>

                <div class="col-lg-6 mb-5">
                    <div class="card-box p-4">
                        <h4 class="text-white">📈 Candidatures par mission</h4>
                        <canvas id="candidaturesMissionChart"></canvas>
                    </div>
                </div>

                <div class="col-lg-6 mb-5">
                    <div class="card-box p-4">
                        <h4 class="text-white">🥧 Niveaux d'expérience</h4>
                        <canvas id="niveauExpChart"></canvas>
                    </div>
                </div>

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
    expValues: <?= json_encode(array_values($expLevels)) ?>
};
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
}
</script>

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- TON JS -->
<script src="assets/js/back.js"></script>
<script src="assets/js/charts.js"></script>

</body>
</html>
