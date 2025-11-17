<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Importation des controllers
require_once __DIR__ . '/../../controller/missioncontroller.php';
require_once __DIR__ . '/../../controller/condidaturecontroller.php';

$missionC = new missioncontroller();
$condC = new condidaturecontroller();

// Récupérer les données
$missions = $missionC->getMissions();
$candidatures = $condC->getAllCondidatures();

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

    <!-- CSS corrects -->
    <link rel="stylesheet" href="../frontoffice/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/back.css">

    <!-- Font Awesome (CDN propre) -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<!-- SIDEBAR -->
<nav id="sidebar">
    <div class="sidebar-header">
        <h3><img src="../../img/logo.png" style="height:40px;"> ENGAGE Admin</h3>
    </div>

    <ul class="list-unstyled components">
        <li class="active"><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="mission/missionliste.php"><i class="fas fa-tasks"></i> Missions</a></li>
        <li><a href="condidature/listecondidature.php"><i class="fas fa-users"></i> Candidatures</a></li>
    </ul>
</nav>

<!-- CONTENT -->
<div id="content">

    <nav class="navbar navbar-expand-lg topbar">
        <button id="sidebarCollapse" class="btn btn-dark">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <div class="container mt-4">

        <h2 class="text-white mb-4">📊 Tableau de bord – Statistiques</h2>

        <div class="row">

            <!-- GRAPHIQUE 1 -->
            <div class="col-lg-6 mb-5">
                <div class="card-box p-4">
                    <h4 class="text-white">📊 Missions par thème</h4>
                    <canvas id="missionsThemeChart"></canvas>
                </div>
            </div>

            <!-- GRAPHIQUE 2 -->
            <div class="col-lg-6 mb-5">
                <div class="card-box p-4">
                    <h4 class="text-white">📈 Candidatures par mission</h4>
                    <canvas id="candidaturesMissionChart"></canvas>
                </div>
            </div>

            <!-- GRAPHIQUE 3 -->
            <div class="col-lg-6 mb-5">
                <div class="card-box p-4">
                    <h4 class="text-white">🥧 Niveaux d'expérience</h4>
                    <canvas id="niveauExpChart"></canvas>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- PASSAGE DES DONNÉES PHP → JS -->
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

<!-- Fichiers JS Backoffice -->
<script src="assets/js/back.js"></script>

<!-- Ton fichier charts.js -->
<script src="assets/js/charts.js"></script>

</body>
</html>
