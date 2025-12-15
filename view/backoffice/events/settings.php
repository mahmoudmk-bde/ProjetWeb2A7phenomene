<?php require_once 'lang/lang_config.php'; ?>
<!DOCTYPE html>
<html lang="<?= get_current_lang() ?>" dir="<?= get_dir() ?>">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title><?= __('settings') ?> - Backoffice</title>
    <link rel="icon" href="assets/img/favicon.png" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/all.css" />
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
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: var(--accent-color);
            text-align: center;
        }
        
        #sidebar .sidebar-header h3 {
            margin: 0;
            font-weight: bold;
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 15px 20px;
            color: var(--text-color);
            display: block;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        #sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        #sidebar ul li a:hover {
            background: var(--accent-color);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        #sidebar ul li.active > a {
            background: var(--accent-color);
            border-left: 4px solid var(--primary-color);
            color: var(--primary-color);
        }
        
        #content {
            width: calc(100% - var(--sidebar-width));
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s;
            background: #1f2235;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            background: var(--secondary-color);
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            border: 1px solid #2d3047;
        }
        
        .card-header {
            background: var(--accent-color);
            border-bottom: 2px solid var(--primary-color);
            color: var(--text-color);
            font-weight: bold;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #ff6b7a, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        }
        
        .navbar {
            background: var(--secondary-color) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .dropdown-menu {
            background: var(--secondary-color);
            border: 1px solid var(--primary-color);
        }
        
        .dropdown-item {
            color: var(--text-color);
        }
        
        .dropdown-item:hover {
            background: var(--accent-color);
            color: var(--primary-color);
        }
        
        #sidebarCollapse {
            background: var(--primary-color);
            border: none;
            border-radius: 5px;
        }
        
        .text-primary { color: var(--primary-color) !important; }
        .form-control, .form-select {
            background-color: var(--accent-color);
            color: var(--text-color);
            border: 1px solid #2d3047;
        }
        
        .form-control:focus, .form-select:focus {
            background-color: var(--secondary-color);
            color: var(--text-color);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.25);
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
                <a href="../listReclamation.php"><i class="fas fa-exclamation-circle"></i> <?= __('reclamations') ?></a>
            </li>
            <li>
                <a href="evenement.php"><i class="fas fa-calendar-alt"></i> <?= __('events') ?></a>
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
            <li class="active">
                <a href="settings.php"><i class="fas fa-cog"></i> <?= __('settings') ?></a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
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
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fas fa-language"></i> <?= __('language_settings') ?></h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="languageSelect"><?= __('select_language') ?></label>
                                    <select class="form-control" id="languageSelect" name="lang">
                                        <option value="fr" <?= get_current_lang() === 'fr' ? 'selected' : '' ?>>Français</option>
                                        <option value="en" <?= get_current_lang() === 'en' ? 'selected' : '' ?>>English</option>
                                        <option value="ar" <?= get_current_lang() === 'ar' ? 'selected' : '' ?>>العربية</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3"><?= __('save_changes') ?></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/jquery-1.12.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/all.js"></script>

    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
            });
        });
    </script>
</body>
</html>
