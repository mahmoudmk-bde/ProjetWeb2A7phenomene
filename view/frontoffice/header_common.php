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
?>
<!-- Header -->
<header class="main_menu single_page_menu">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="<?= isset($_SESSION['user_id']) ? 'index1.php' : 'index.php' ?>">
                        <img src="../img/logo.png" alt="logo" style="height: 110px;" />
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" 
                            data-target="#navbarSupportedContent">
                        <span class="menu_icon"><i class="fas fa-bars"></i></span>
                    </button>

                    <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                            <li class="nav-item"><a class="nav-link" href="missionlist.php">Missions</a></li>
                            <li class="nav-item"><a class="nav-link" href="store.php?controller=Store&action=index">Store</a></li>
                            <li class="nav-item"><a class="nav-link" href="store.php?controller=Partenaire&action=index">Partenaires</a></li>
                        </ul>
                    </div>

                    <!-- Button / User dropdown -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($headerShowUserMenu): ?>
                            <?php
                            // Compute number of unseen responses (vu = 0) for current user's reclamations
                            $responseCount = 0;
                            try {
                                require_once __DIR__ . '/../../db_config.php';
                                $pdo = config::getConnexion();

                                // Ensure `vu` column exists; if missing, attempt to add it (safe best-effort)
                                try {
                                    $dbNameStmt = $pdo->query('SELECT DATABASE() AS dbname');
                                    $dbName = $dbNameStmt->fetchColumn();
                                    $colCheck = $pdo->prepare("SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = 'response' AND COLUMN_NAME = 'vu'");
                                    $colCheck->execute(['db' => $dbName]);
                                    $exists = (int)$colCheck->fetchColumn();
                                    if ($exists === 0) {
                                        $pdo->exec("ALTER TABLE response ADD COLUMN vu TINYINT(1) NOT NULL DEFAULT 0 AFTER date_response");
                                    }
                                } catch (Exception $inner) {
                                    // ignore schema modification failures; we'll still try to query using vu
                                }

                                $stmt = $pdo->prepare("SELECT COUNT(*) AS cnt
                                    FROM response r
                                    JOIN reclamation rec ON rec.id = r.reclamation_id
                                    WHERE rec.utilisateur_id = :uid AND IFNULL(r.vu,0) = 0");
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
                                    <a href="profile.php">
                                        <i class="fas fa-user me-2"></i>Mon Profil
                                    </a>
                                    <a href="settings.php">
                                        <i class="fas fa-cog me-2"></i>Paramètres
                                    </a>
                                    <a href="securite.php">
                                        <i class="fas fa-shield-alt me-2"></i>Sécurité
                                    </a>
                                    <a href="historique_reclamations.php">
                                        <i class="fas fa-history me-2"></i>Historique
                                    </a>
                                    <a href="addreclamation.php">
                                        <i class="fas fa-exclamation-circle me-2"></i>Réclamation
                                    </a>
                                    <a href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="index1.php" class="btn_1 d-none d-sm-block">Mon Espace</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="connexion.php" class="btn_1 d-none d-sm-block">Se connecter</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </div>
</header>

<style>
    .response-badge {
        position: absolute;
        top: -6px;
        right: -6px;
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
    .user-dropdown a { display:block; padding:10px 16px; color:#333; border-bottom:1px solid #eee; }
    .user-dropdown a:last-child { border-bottom: none; }
    .user-dropdown a:hover { background:#f8f8f8; }
    .user-wrapper .user-name { margin-right:10px; color: #fff; }
    .user-menu .user-dropdown { right:0; left:auto; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const userMenu = document.querySelector('.user-menu');
    if (!userMenu) return;

    const dropdown = userMenu.querySelector('.user-dropdown');
    const trigger = userMenu.querySelector('.user-wrapper');

    trigger.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
    });

    document.addEventListener('click', function (event) {
        if (!userMenu.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });
});
</script>
