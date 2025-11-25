<?php

$pageTitle = isset($pageTitle) ? $pageTitle : 'Backoffice - Engage';
$content = isset($content) ? $content : '';
$activePage = isset($activePage) ? $activePage : '';

// Compute a BASE_URL pointing to the app root and make it available to templates
$scriptName = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$base = 'http://' . $host . dirname($scriptName);
$base = str_replace('/View/Back', '', $base);
$base = rtrim($base, '/\\') . '/';
define('BASE_URL', $base);

require_once __DIR__ . '/helpers.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" href="<?= BASE_URL ?>View/Back/assets/img/favicon.png" />
    <!-- Local backoffice CSS (copied from partenaire template) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>View/Back/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>View/Back/assets/css/all.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>View/Back/assets/css/custom-backoffice.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>View/Back/assets/css/flaticon.css" />
    <link rel="stylesheet" href="<?= BASE_URL ?>View/Back/assets/css/font-awesome.min.css" />
    <style>
        /* small compatibility tweaks kept locally */
        body { background:#f5f6fa; }
        #content { padding:20px; }
    </style>
</head>
<body>
    <?php /* Sidebar + nav markup kept from original layout */ ?>
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-cog"></i> BACKOFFICE</h3>
        </div>

        <ul class="list-unstyled components">
            <li <?= $activePage === 'dashboard' ? 'class="active"' : '' ?>>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li <?= $activePage === 'reclamations' ? 'class="active"' : '' ?>>
                <a href="listReclamation.php"><i class="fas fa-exclamation-circle"></i> Réclamations</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-users"></i> Utilisateurs</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-cog"></i> Paramètres</a>
            </li>
        </ul>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> Administrateur
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#"><i class="fas fa-user"></i> Mon Profil</a>
                                <a class="dropdown-item" href="#"><i class="fas fa-cog"></i> Paramètres</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <?= $content ?>
        </div>
    </div>

    <!-- JavaScript (local copies) -->
    <script src="<?= BASE_URL ?>View/Back/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?= BASE_URL ?>View/Back/assets/js/popper.min.js"></script>
    <script src="<?= BASE_URL ?>View/Back/assets/js/bootstrap.min.js"></script>
    <script>
        (function(){
            var btn = document.getElementById('sidebarCollapse');
            if (btn) {
                btn.addEventListener('click', function(){
                    var sb = document.getElementById('sidebar');
                    var ct = document.getElementById('content');
                    if (sb) sb.classList.toggle('active');
                    if (ct) ct.classList.toggle('active');
                });
            }
        })();
    </script>
</body>
</html>
