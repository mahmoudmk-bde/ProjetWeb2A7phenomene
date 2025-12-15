<?php
// Ensure this file is included within a context that can find the lang config, or use absolute path
$config_path = __DIR__ . '/../lang/lang_config.php';
if (file_exists($config_path)) {
    require_once $config_path;
}
?>
<!DOCTYPE html>
<html lang="<?= get_current_lang() ?>" dir="<?= get_dir() ?>">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Backoffice - Engage</title>
    <link rel="icon" href="assets/img/favicon.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="assets/css/all.css" />
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #ff4a57;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --sidebar-width: 250px;
        }
        body {
            background-color: #1f2235;
            color: var(--text-color);
            overflow-x: hidden;
            font-family: 'Arial', sans-serif;
        }
        #sidebar {
            min-height: 100vh;
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            background: var(--secondary-color);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            border-right: 2px solid var(--primary-color);
        }
        #sidebar .sidebar-header { padding: 20px; background: var(--accent-color); text-align: center; }
        #sidebar .sidebar-header h3 { margin: 0; font-weight: bold; }
        #sidebar ul.components { padding: 20px 0; }
        #sidebar ul li a { padding: 15px 20px; color: var(--text-color); display: block; text-decoration: none; border-left: 4px solid transparent; transition: all 0.3s; font-size: 14px; }
        #sidebar ul li a i { margin-right: 10px; width: 20px; text-align: center; }
        #sidebar ul li a:hover { background: var(--accent-color); border-left: 4px solid var(--primary-color); color: var(--primary-color); }
        #sidebar ul li.active > a { background: var(--accent-color); border-left: 4px solid var(--primary-color); color: var(--primary-color); }
        #content { width: calc(100% - var(--sidebar-width)); margin-left: var(--sidebar-width); padding: 20px; transition: all 0.3s; background: #1f2235; }
        .card { border: none; border-radius: 10px; background: var(--secondary-color); margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); border: 1px solid #2d3047; }
        .card-header { background: var(--accent-color); border-bottom: 2px solid var(--primary-color); color: var(--text-color); font-weight: bold; }
        .stat-card { text-align: center; padding: 25px 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card i { font-size: 2.5rem; margin-bottom: 15px; color: var(--primary-color); }
        .stat-card h3 { font-size: 2rem; margin: 10px 0; color: var(--text-color); }
        .stat-card p { color: #b0b3c1; margin: 0; }
        .table-responsive { background: var(--secondary-color); border-radius: 10px; padding: 20px; }
        .table { color: var(--text-color); margin: 0; }
        .table thead th { border-bottom: 2px solid var(--primary-color); color: var(--primary-color); font-weight: bold; }
        .table tbody tr:hover { background: var(--accent-color); }
        .btn-primary { background: linear-gradient(45deg, var(--primary-color), #ff6b7a); border: none; border-radius: 25px; padding: 10px 25px; font-weight: bold; transition: all 0.3s; }
        .btn-primary:hover { background: linear-gradient(45deg, #ff6b7a, var(--primary-color)); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4); }
        .navbar { background: var(--secondary-color) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.3); border-radius: 10px; margin-bottom: 20px; }
        .badge { border-radius: 15px; padding: 5px 12px; font-weight: normal; }
        .badge-success { background: #28a745; }
        .badge-warning { background: #ffc107; color: #212529; }
        .badge-info { background: #17a2b8; }
        .list-group-item { background: var(--accent-color); border: 1px solid #2d3047; color: var(--text-color); margin-bottom: 10px; border-radius: 8px !important; }
        .list-group-item:hover { background: var(--secondary-color); border-color: var(--primary-color); }
        .dropdown-menu { background: var(--secondary-color); border: 1px solid var(--primary-color); }
        .dropdown-item { color: var(--text-color); }
        .dropdown-item:hover { background: var(--accent-color); color: var(--primary-color); }
        #sidebarCollapse { background: var(--primary-color); border: none; border-radius: 5px; }
        .text-primary { color: var(--primary-color) !important; }
        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-info { color: #17a2b8 !important; }
        
        /* Modal Fixes for Dark Theme */
        .modal-content {
            background-color: var(--secondary-color);
            color: var(--text-color);
            border: 1px solid var(--primary-color);
        }
        .modal-header {
            border-bottom: 1px solid var(--primary-color);
            background-color: var(--accent-color);
        }
        .modal-footer {
            border-top: 1px solid var(--primary-color);
            background-color: var(--accent-color);
        }
        .modal-title {
            color: var(--primary-color);
            font-weight: bold;
        }
        .close {
            color: var(--text-color);
            text-shadow: none;
            opacity: 0.8;
        }
        .close:hover {
            color: var(--primary-color);
            opacity: 1;
        }

        /* RTL Support */
        <?php if (get_dir() === 'rtl'): ?>
        #sidebar {
            right: 0;
            left: auto;
            border-right: none;
            border-left: 2px solid var(--primary-color);
        }
        
        #content {
            margin-left: 0;
            margin-right: var(--sidebar-width);
        }
        
        #sidebar.active {
            margin-right: -250px;
        }
        
        #content.active {
            margin-right: 0;
        }

        #sidebar ul li a {
            border-left: none;
            border-right: 4px solid transparent;
        }
        
        #sidebar ul li a:hover, 
        #sidebar ul li.active > a {
            border-left: none;
            border-right: 4px solid var(--primary-color);
        }

        #sidebar ul li a i {
            margin-right: 0;
            margin-left: 10px;
        }

        .dropdown-menu {
            text-align: right;
        }

        .ml-auto {
            margin-left: 0 !important;
            margin-right: auto !important;
        }
        <?php endif; ?>
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><img src="assets/img/logo.png" alt="logo" style="height: 120px; margin-right: 10px;" />BACKOFFICE</h3>
        </div>

        <ul class="list-unstyled components">
            <li>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i> <?= __('dashboard') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-tasks"></i> <?= __('missions') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-gamepad"></i> <?= __('gamification') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-exclamation-circle"></i> <?= __('reclamations') ?></a>
            </li>
            <li>
                <a href="evenement.php"><i class="fas fa-calendar-alt"></i> <?= __('events') ?></a>
            </li>
            <li>
                <a href="participation_history.php"><i class="fas fa-history"></i> <?= __('participation_history') ?? 'Historique participation' ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-graduation-cap"></i> <?= __('education') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-users"></i> <?= __('users') ?></a>
            </li>
            <li>
                <a href="#"><i class="fas fa-chart-bar"></i> <?= __('analytics') ?></a>
            </li>
            <li>
                <a href="settings.php"><i class="fas fa-cog"></i> <?= __('settings') ?></a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <!-- Top Navigation -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="collapse navbar-collapse">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" style="color: var(--text-color);">
                                <i class="fas fa-user-circle" style="color: var(--primary-color);"></i> Administrateur
                            </a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#"><i class="fas fa-user"></i> <?= __('admin_profile') ?></a>
                                <a class="dropdown-item" href="settings.php"><i class="fas fa-cog"></i> <?= __('settings') ?></a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt"></i> <?= __('logout') ?></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <!-- Page specific content starts here -->
