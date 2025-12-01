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
                        <img src="assets/img/logo.png" alt="logo" style="height: 45px;" />
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
                            <div class="user-menu d-none d-sm-block">
                                <div class="user-wrapper">
                                    <span class="user-name"><?= htmlspecialchars($sessionUserName) ?></span>
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
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
