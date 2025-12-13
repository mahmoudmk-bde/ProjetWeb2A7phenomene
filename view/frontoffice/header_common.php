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

// Load notifications for authenticated users
$notifications = [];
$unreadCount = 0;
if (isset($_SESSION['user_id']) && $headerShowUserMenu) {
    try {
        require_once __DIR__ . '/../../controller/NotificationController.php';
        $notifCtrl = new NotificationController();
        $notifications = $notifCtrl->getUserNotifications($_SESSION['user_id'], 20);
        $unreadCount = $notifCtrl->getUnreadCount($_SESSION['user_id']);
    } catch (Exception $e) {
        error_log('Header notification error: ' . $e->getMessage());
    }
}
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
                            <li class="nav-item"><a class="nav-link" href="gamification.php">Gamification</a></li>
                            <li class="nav-item"><a class="nav-link" href="quiz.php">Quiz</a></li>
                            <li class="nav-item"><a class="nav-link" href="evenements.php">Événements</a></li>
                        </ul>
                    </div>

                    <!-- Cart & Wishlist -->
                    <div class="d-flex align-items-center mr-3">
                        <a href="store.php?controller=Store&action=cart" class="btn_1 btn-cart d-none d-sm-inline-flex" aria-label="Panier">
                            (<?php $cnt=0; if(isset($_SESSION['cart'])){ foreach($_SESSION['cart'] as $q){ $cnt+=(int)$q; } } echo $cnt; ?>)
                        </a>
                        <a href="store.php?controller=Store&action=wishlist" class="btn_1 btn-like d-none d-sm-inline-flex" aria-label="Liste d'envies"></a>
                    </div>

                    <!-- Button / User dropdown -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($headerShowUserMenu): ?>
                            <!-- Notification Bell -->
                            <div class="notification-menu d-none d-sm-block" style="position: relative; display: inline-block; margin-right: 20px;">
                                <button class="notification-bell" type="button" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.3rem; position: relative;">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($unreadCount > 0): ?>
                                        <span class="notification-badge" style="position: absolute; top: -5px; right: -8px; background: #ff4a57; color: white; font-size: 0.7rem; padding: 2px 6px; border-radius: 999px; border: 2px solid white; line-height: 1; min-width: 20px; text-align: center; box-shadow: 0 1px 2px rgba(0,0,0,0.15);"><?= $unreadCount ?></span>
                                    <?php endif; ?>
                                </button>
                                <!-- Notification Dropdown -->
                                <div class="notification-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; min-width: 350px; max-height: 500px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); z-index: 1000; margin-top: 10px; overflow: hidden;">
                                    <div style="background: linear-gradient(45deg, #ff4a57, #ff6b6b); color: white; padding: 15px; font-weight: 700; font-size: 1rem;">
                                        📢 Notifications
                                    </div>
                                    <div style="max-height: 420px; overflow-y: auto;">
                                        <?php if (!empty($notifications)): ?>
                                            <?php foreach ($notifications as $notif): ?>
                                                <a href="<?= htmlspecialchars($notif['link']) ?>" class="notification-item" style="display: block; padding: 12px 15px; border-bottom: 1px solid #f0f0f0; text-decoration: none; color: #333; transition: background 0.2s;">
                                                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 10px;">
                                                        <div style="flex: 1;">
                                                            <div style="font-weight: 600; color: #333; margin-bottom: 4px;">
                                                                <?php if ($notif['type'] === 'reclamation_answer'): ?>
                                                                    <i class="fas fa-check-circle" style="color: #28a745;"></i> Réponse à votre réclamation
                                                                <?php elseif ($notif['type'] === 'candidature_accepted'): ?>
                                                                    <i class="fas fa-check-circle" style="color: #28a745;"></i> Candidature acceptée
                                                                <?php elseif ($notif['type'] === 'candidature_rejected'): ?>
                                                                    <i class="fas fa-times-circle" style="color: #dc3545;"></i> Candidature rejetée
                                                                <?php endif; ?>
                                                            </div>
                                                            <div style="font-size: 0.9rem; color: #666; margin-bottom: 4px;">
                                                                <?= htmlspecialchars(substr($notif['title'], 0, 40)) ?>
                                                            </div>
                                                            <div style="font-size: 0.8rem; color: #999;">
                                                                <?= date('d/m/Y H:i', strtotime($notif['created_at'] ?? 'now')) ?>
                                                            </div>
                                                        </div>
                                                        <?php if ($notif['is_unread']): ?>
                                                            <div style="background: #ff4a57; width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;"></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div style="padding: 30px 15px; text-align: center; color: #999;">
                                                <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 10px; opacity: 0.5;"></i>
                                                <p>Aucune notification</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="user-menu d-none d-sm-block">
                                <div class="user-wrapper">
                                    <span class="user-name"><?= htmlspecialchars($sessionUserName) ?></span>
                                    <div class="user-avatar" style="position:relative;">
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
    
    .notification-item:hover {
        background: #f8f9fa;
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
    if (userMenu) {
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
    }

    // Notification bell dropdown
    const notifBell = document.querySelector('.notification-bell');
    const notifDropdown = document.querySelector('.notification-dropdown');
    if (notifBell && notifDropdown) {
        notifBell.addEventListener('click', function (e) {
            e.stopPropagation();
            const isVisible = notifDropdown.style.display === 'block';
            notifDropdown.style.display = isVisible ? 'none' : 'block';
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.notification-menu')) {
                notifDropdown.style.display = 'none';
            }
        });
    }
});
</script>

