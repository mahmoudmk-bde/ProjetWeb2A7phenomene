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

$notifications = $notifications ?? [];
$notificationCount = $notificationCount ?? 0;
$notificationTitle = 'Notifications';

// Fallback: si aucune notification fournie par la page, charger un minimum ici
if (isset($_SESSION['user_id']) && empty($notifications)) {
    try {
        require_once __DIR__ . '/../../db_config.php';
        $pdo = config::getConnexion();
        $uid = (int) $_SESSION['user_id'];

        // Réponses de réclamations
        $respStmt = $pdo->prepare("SELECT r.reclamation_id, r.contenu, r.date_response, rec.sujet
                                    FROM response r
                                    JOIN reclamation rec ON rec.id = r.reclamation_id
                                    WHERE rec.utilisateur_id = :uid
                                    ORDER BY r.date_response DESC
                                    LIMIT 10");
        $respStmt->execute(['uid' => $uid]);
        foreach ($respStmt->fetchAll(PDO::FETCH_ASSOC) as $n) {
            $notifications[] = [
                'title' => 'Réponse à votre réclamation',
                'body' => $n['sujet'] ?? 'Réclamation',
                'text' => $n['contenu'] ?? '',
                'date' => $n['date_response'] ?? null,
                'href' => 'historique_reclamations.php#rec-' . (int)($n['reclamation_id'] ?? 0),
                'key' => md5(($n['sujet'] ?? '') . '|' . ($n['contenu'] ?? '') . '|' . ($n['date_response'] ?? '') . '|' . ($n['reclamation_id'] ?? ''))
            ];
        }

        // Candidatures acceptées
        $accStmt = $pdo->prepare("SELECT c.mission_id, c.date_candidature, c.statut, m.titre
                                   FROM candidatures c
                                   LEFT JOIN missions m ON m.id = c.mission_id
                                   WHERE c.utilisateur_id = :uid AND c.statut IN ('accepte','acceptee','acceptée')
                                   ORDER BY c.date_candidature DESC
                                   LIMIT 10");
        $accStmt->execute(['uid' => $uid]);
        foreach ($accStmt->fetchAll(PDO::FETCH_ASSOC) as $n) {
            $notifications[] = [
                'title' => 'Candidature acceptée',
                'body' => $n['titre'] ?? 'Mission',
                'text' => 'Vous avez été accepté(e) dans cette mission.',
                'date' => $n['date_candidature'] ?? null,
                'href' => 'missiondetails.php?id=' . (int)($n['mission_id'] ?? 0),
                'key' => md5('cand|' . ($n['mission_id'] ?? '') . '|' . ($n['date_candidature'] ?? '') . '|' . ($n['statut'] ?? ''))
            ];
        }

        // Nouvelles missions
        $missStmt = $pdo->query("SELECT id, titre, description, date_creation FROM missions ORDER BY date_creation DESC LIMIT 3");
        foreach ($missStmt->fetchAll(PDO::FETCH_ASSOC) as $m) {
            $notifications[] = [
                'title' => 'Nouvelle mission',
                'body' => $m['titre'] ?? 'Mission',
                'text' => $m['description'] ?? '',
                'date' => $m['date_creation'] ?? null,
                'href' => 'missiondetails.php?id=' . (int)($m['id'] ?? 0),
                'key' => md5('mission|' . ($m['id'] ?? '') . '|' . ($m['date_creation'] ?? '') . '|' . ($m['titre'] ?? ''))
            ];
        }

        usort($notifications, function($a, $b) {
            $da = isset($a['date']) ? strtotime($a['date']) : 0;
            $db = isset($b['date']) ? strtotime($b['date']) : 0;
            return $db <=> $da;
        });

        $notificationCount = count($notifications);
    } catch (Exception $e) {
        // en cas d'échec, on conserve les valeurs par défaut
    }
}

$headerShowUserMenu = isset($headerShowUserMenu) ? (bool)$headerShowUserMenu : false;
?>
<!-- Header -->
<header class="main_menu single_page_menu">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="<?= isset($_SESSION['user_id']) ? '/projetweb2/view/frontoffice/index1.php' : '/projetweb2/view/frontoffice/index.php' ?>">
                        <img src="/projetweb2/view/frontoffice/assets/img/logo.png" alt="logo" style="height: 45px;" />
                    </a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" 
                            data-target="#navbarSupportedContent">
                        <span class="menu_icon"><i class="fas fa-bars"></i></span>
                    </button>

                    <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item"><a class="nav-link" href="/projetweb2/view/frontoffice/index.php">Accueil</a></li>
                            <li class="nav-item"><a class="nav-link" href="/projetweb2/view/frontoffice/missionlist.php">Missions</a></li>
                            <li class="nav-item"><a class="nav-link" href="/projetweb2/view/frontoffice/store.php?controller=Store&action=index">Store</a></li>
                            <li class="nav-item"><a class="nav-link" href="/projetweb2/view/frontoffice/store.php?controller=Partenaire&action=index">Partenaires</a></li>
                            <li class="nav-item"><a class="nav-link" href="/projetweb2/view/frontoffice/events/event.php">Événements</a></li>
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item notification-item">
                                <a class="nav-link notification-bell" href="#" id="notificationBell">
                                    <i class="fas fa-bell"></i>
                                    <?php if ($notificationCount > 0): ?>
                                        <span class="notif-badge"><?= $notificationCount ?></span>
                                    <?php endif; ?>
                                </a>
                                <div class="notification-dropdown" id="notificationDropdown">
                                    <div class="notif-header">
                                        <span><?= htmlspecialchars($notificationTitle) ?></span>
                                        <?php if ($notificationCount > 0): ?>
                                            <span class="notif-count"><?= $notificationCount ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="notif-list">
                                        <?php if (empty($notifications)): ?>
                                            <div class="notif-empty">Aucune notification pour l'instant</div>
                                        <?php else: ?>
                                            <?php foreach ($notifications as $notif): ?>
                                                <?php $nkey = $notif['key'] ?? md5(($notif['title'] ?? '') . '|' . ($notif['body'] ?? '') . '|' . ($notif['date'] ?? '') . '|' . ($notif['href'] ?? '')); ?>
                                                <a class="notif-item unread" data-key="<?= htmlspecialchars($nkey) ?>" href="<?= htmlspecialchars($notif['href'] ?? '#') ?>">
                                                    <div class="notif-title"><?= htmlspecialchars($notif['title'] ?? 'Notification') ?></div>
                                                    <div class="notif-body">
                                                        <?= htmlspecialchars($notif['body'] ?? '') ?>
                                                    </div>
                                                    <?php if (!empty($notif['text'])): ?>
                                                        <div class="notif-text"><?= nl2br(htmlspecialchars($notif['text'])) ?></div>
                                                    <?php endif; ?>
                                                    <div class="notif-date"><?= isset($notif['date']) ? date('d/m/Y H:i', strtotime($notif['date'])) : '' ?></div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Button / User dropdown -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($headerShowUserMenu): ?>
                            <?php
                            // Compute number of distinct reclamations that have responses for current user
                            $responseCount = 0;
                            try {
                                require_once __DIR__ . '/../../db_config.php';
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
    .notification-item { position: relative; }
    .notification-bell { position: relative; display: flex; align-items: center; font-size: 1.6rem; padding: 6px 4px; }
    .notif-badge {
        position:absolute;
        top:0;
        right:-8px;
        background:#ff4a57;
        color:#fff;
        font-size:0.7rem;
        padding:2px 6px;
        border-radius:999px;
        border:2px solid #fff;
        line-height:1;
        min-width:18px;
        text-align:center;
    }
    .notification-dropdown {
        position:absolute;
        right:0;
        top:120%;
        width:320px;
        background:#fff;
        border-radius:12px;
        box-shadow:0 10px 30px rgba(0,0,0,0.2);
        overflow:hidden;
        display:none;
        z-index:1200;
    }
    .notification-dropdown.show { display:block; }
    .notif-header {
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:12px 16px;
        background:linear-gradient(45deg,#ff4a57,#ff6b6b);
        color:#fff;
        font-weight:700;
    }
    .notif-count {
        background:rgba(255,255,255,0.2);
        padding:2px 8px;
        border-radius:12px;
        font-size:0.85rem;
    }
    .notif-list { max-height:360px; overflow-y:auto; }
    .notif-item { padding:12px 16px; border-bottom:1px solid #f1f1f1; display:block; color:#333; text-decoration:none; }
    .notif-item:last-child { border-bottom:none; }
    .notif-item.unread { background:#fff7e6; }
    .notif-title { font-weight:700; color:#333; margin-bottom:4px; }
    .notif-body { font-size:0.92rem; color:#555; margin-bottom:4px; }
    .notif-text { font-size:0.9rem; color:#666; }
    .notif-date { font-size:0.8rem; color:#999; margin-top:6px; }
    .notif-empty { padding:20px; text-align:center; color:#777; font-size:0.95rem; }
    .notif-footer { text-align:center; padding:10px 12px; background:#fafafa; border-top:1px solid #f1f1f1; }
    .notif-footer a { color:#ff4a57; font-weight:600; text-decoration:none; }
    .notif-footer a:hover { text-decoration:underline; }
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

    // Notifications dropdown
    const bell = document.getElementById('notificationBell');
    const notifDropdown = document.getElementById('notificationDropdown');
    const notifBadge = document.querySelector('.notif-badge');
    const notifItems = document.querySelectorAll('.notif-item');

    function loadSeen() {
        try {
            return JSON.parse(localStorage.getItem('engage_seen_notifs') || '[]');
        } catch (e) { return []; }
    }

    function saveSeen(arr) {
        localStorage.setItem('engage_seen_notifs', JSON.stringify(arr));
    }

    function updateUnreadUI() {
        const seen = loadSeen();
        let unread = 0;
        notifItems.forEach(item => {
            const key = item.dataset.key || '';
            if (key && seen.includes(key)) {
                item.classList.remove('unread');
            } else {
                unread++;
                item.classList.add('unread');
            }
        });
        if (notifBadge) {
            if (unread > 0) {
                notifBadge.textContent = unread;
                notifBadge.style.display = 'inline-block';
            } else {
                notifBadge.style.display = 'none';
            }
        }
    }

    function markSeen(key) {
        if (!key) return;
        const seen = loadSeen();
        if (!seen.includes(key)) {
            seen.push(key);
            saveSeen(seen);
        }
        updateUnreadUI();
    }
    if (bell && notifDropdown) {
        bell.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            notifDropdown.classList.toggle('show');
        });
        document.addEventListener('click', function(e){
            if (!notifDropdown.contains(e.target) && e.target !== bell) {
                notifDropdown.classList.remove('show');
            }
        });
    }

    notifItems.forEach(item => {
        item.addEventListener('click', function() {
            markSeen(item.dataset.key || '');
        });
    });

    updateUnreadUI();
});
</script>
