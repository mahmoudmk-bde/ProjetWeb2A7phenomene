<?php
// Common header for all frontoffice pages
// Ensure default values for header context when not provided
if (!isset($sessionUserName)) {
    if (isset($_SESSION['user_id'])) {
        $sessionUserName = $_SESSION['user_name'] ?? 'Utilisateur';
    } else {
        $sessionUserName = 'Invité';
    }
}

if (!isset($sessionUserType)) {
    $sessionUserType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'guest';
}

$headerShowUserMenu = isset($headerShowUserMenu) ? (bool)$headerShowUserMenu : false;

// Determine Base URL for consistent paths
$baseUrl = defined('BASE_URL') ? BASE_URL : '/ProjetWeb2A7phenomene/';
// Ensure we point to view/frontoffice for links
$frontOfficePath = $baseUrl . 'view/frontoffice/';
?>
<!-- Header -->
<header class="main_menu single_page_menu">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="<?= isset($_SESSION['user_id']) ? $frontOfficePath.'index1.php' : $frontOfficePath.'index.php' ?>">
                        <img src="<?= $frontOfficePath ?>assets/img/logo.png" alt="logo" style="height: 45px;" />
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" 
                            data-target="#navbarSupportedContent">
                        <span class="menu_icon"><i class="fas fa-bars"></i></span>
                    </button>

                    <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item"><a class="nav-link" href="<?= $frontOfficePath ?>index.php">Accueil</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= $frontOfficePath ?>missionlist.php">Missions</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= $frontOfficePath ?>store.php?controller=Store&action=index">Store</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= $frontOfficePath ?>store.php?controller=Partenaire&action=index">Partenaires</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= $frontOfficePath ?>**********.php">Education</a></li>
                            <li class="nav-item"><a class="nav-link" href="<?= $frontOfficePath ?>**********.php">Evenement</a></li>
                        </ul>
                    </div>

                    <!-- Button / User dropdown -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                        // Compute number of distinct reclamations that have responses for current user
                        $responseCount = 0;
                        try {
                            // Only require db_config if not already included
                            if (!class_exists('config')) {
                                require_once __DIR__ . '/../../db_config.php';
                            }
                            $pdo = config::getConnexion();
                            $stmt = $pdo->prepare("SELECT COUNT(DISTINCT r.reclamation_id) AS cnt
                                FROM response r
                                JOIN reclamation rec ON rec.id = r.reclamation_id
                                WHERE rec.utilisateur_id = :uid");
                            $stmt->execute(['uid' => $_SESSION['user_id']]);
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                            $responseCount = $row && isset($row['cnt']) ? (int)$row['cnt'] : 0;
                        } catch (Exception $e) {
                            $responseCount = 0;
                        }
                        ?>

                        <div class="user-menu d-none d-sm-block">
                            <div class="user-wrapper">
                                <span class="user-name"><?= htmlspecialchars($sessionUserName) ?></span>
                                <div class="user-avatar" style="position:relative;">
                                    <i class="fas fa-user"></i>
                                    <?php if ($responseCount > 0): ?>
                                        <span class="response-badge"><?= $responseCount ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="user-dropdown">
                                <a href="<?= $frontOfficePath ?>profile.php">
                                    <i class="fas fa-user me-2"></i>Mon Profil
                                </a>
                                <a href="<?= $frontOfficePath ?>index1.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>mon espace
                                </a>
                                <a href="<?= $frontOfficePath ?>addreclamation.php">
                                    <i class="fas fa-exclamation-circle me-2"></i>Réclamation
                                </a>
                                <a href="<?= $frontOfficePath ?>historique_reclamations.php">
                                    <i class="fas fa-history me-2"></i>Historique
                                </a>
                                <a href="<?= $frontOfficePath ?>settings.php">
                                    <i class="fas fa-cog me-2"></i>Paramètres
                                </a>
                                <a href="<?= $frontOfficePath ?>logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= $frontOfficePath ?>connexion.php" class="btn_1 d-none d-sm-block">Se connecter</a>
                        <a href="<?= $frontOfficePath ?>inscription.php" class="btn_1 d-none d-sm-block">S'INSCRIREx</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
</header>

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

    /* Common Header User Menu Styles */
    .user-menu {
        position: relative;
        display: inline-block;
        margin-left: 20px;
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
        background: rgba(255,255,255,0.1); /* Subtle background for visibility */
    }
    
    .user-wrapper:hover {
        background: rgba(255,255,255,0.2);
    }
    
    .user-name {
        font-weight: 600;
        font-size: 14px;
        color: #fff;
    }
    
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s ease;
    }
    
    .user-avatar:hover {
        border-color: rgba(255,255,255,0.6);
        transform: scale(1.05);
    }
    
    .user-avatar i {
        color: white;
        font-size: 18px;
    }

    .response-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4a57;
        color: #fff;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 999px;
        border: 2px solid #fff;
        line-height: 1;
        min-width: 20px;
        text-align: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.15);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userMenu = document.querySelector('.user-menu');
    // Guard clause: if element doesn't exist, stop
    if (!userMenu) return;

    const dropdown = userMenu.querySelector('.user-dropdown');
    const trigger = userMenu.querySelector('.user-wrapper');

    if (trigger && dropdown) {
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!userMenu.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    }
});
</script>