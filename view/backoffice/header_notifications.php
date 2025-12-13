<?php
// Header avec notifications pour les pages du backoffice
// Ne pas démarrer la session ici - elle doit être démarrée dans le fichier parent
// On vérifie seulement si la session existe et si l'utilisateur est admin

// Vérifier que la session existe et que l'utilisateur est admin
if (!isset($_SESSION) || !isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || strtolower($_SESSION['user_type']) !== 'admin') {
    return; // Ne pas afficher les notifications si pas admin ou session non disponible
}

require_once __DIR__ . '/../../controller/NotificationController.php';
$notifCtrl = new NotificationController();
$adminUnreadCount = $notifCtrl->getAdminUnreadCount();
$adminNotifications = $notifCtrl->getAdminNotifications(10);
?>
<!-- Admin Notifications -->
<div class="ml-auto d-flex align-items-center">
    <!-- Notification Container with explicit relative positioning -->
    <div class="position-relative mr-4" style="position: relative;">
        <!-- Toggle Button -->
        <a href="javascript:void(0);" id="customNotifToggle" class="text-white position-relative" style="text-decoration: none; display: inline-block;">
            <i class="fas fa-bell fa-lg"></i>
            <?php if ($adminUnreadCount > 0): ?>
                <span class="badge badge-danger rounded-circle position-absolute" style="top: -8px; right: -8px; font-size: 0.6rem; padding: 4px 6px; border: 2px solid #2d325a;">
                    <?= $adminUnreadCount ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Dropdown Menu -->
        <div id="customNotifMenu" style="display: none; position: absolute; top: 140%; right: -10px; width: 350px; max-height: 450px; overflow-y: auto; background-color: #1f2235; border: 1px solid #2d3047; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); z-index: 10000;">
            <!-- Header -->
            <div style="background-color: #ff4a57; padding: 15px; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                <h6 class="m-0 text-white font-weight-bold">
                    <i class="fas fa-bullhorn mr-2"></i> Notifications
                </h6>
            </div>

            <!-- Content -->
            <div class="p-0">
                <?php if (empty($adminNotifications)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="far fa-bell-slash fa-2x mb-2"></i><br>
                        Aucune nouvelle notification
                    </div>
                <?php else: ?>
                    <?php foreach ($adminNotifications as $notif): ?>
                        <a href="<?= htmlspecialchars($notif['link']) ?>" style="display: block; padding: 15px; color: #fff; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05); transition: background 0.2s;" onmouseover="this.style.backgroundColor='#2d3047'" onmouseout="this.style.backgroundColor='transparent'">
                            <div class="d-flex align-items-start">
                                <div class="mr-3 mt-1">
                                <?php if ($notif['type'] == 'reclamation_new'): ?>
                                    <i class="fas fa-exclamation-circle fa-lg text-warning"></i>
                                <?php elseif ($notif['type'] == 'candidature_new'): ?>
                                    <i class="fas fa-user-plus fa-lg text-info"></i>
                                <?php elseif ($notif['type'] == 'feedback_new'): ?>
                                    <i class="fas fa-star fa-lg text-warning"></i>
                                <?php else: ?>
                                    <i class="fas fa-check-circle fa-lg text-success"></i>
                                <?php endif; ?>
                            </div>
                                
                                    <div>
                                        <h6 class="mb-1 font-weight-bold" style="font-size: 0.95rem;"><?= htmlspecialchars($notif['title']) ?></h6>
                                        <p class="mb-1 text-muted" style="font-size: 0.85rem; line-height: 1.4;"><?= htmlspecialchars($notif['message']) ?></p>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            <i class="far fa-clock mr-1"></i><?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Notification toggle script
document.addEventListener('DOMContentLoaded', function() {
    const notifToggle = document.getElementById('customNotifToggle');
    const notifMenu = document.getElementById('customNotifMenu');
    
    if (notifToggle && notifMenu) {
        notifToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isVisible = notifMenu.style.display === 'block';
            notifMenu.style.display = isVisible ? 'none' : 'block';
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!notifToggle.contains(e.target) && !notifMenu.contains(e.target)) {
                notifMenu.style.display = 'none';
            }
        });
    }
});
</script>

