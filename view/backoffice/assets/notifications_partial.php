<?php
// Backoffice notifications partial
// Expected variables: $notifications (array), $notificationCount (int)
$notifications = $notifications ?? [];
$notificationCount = $notificationCount ?? 0;
?>

<style>
    :root {
        --primary: #ff4a57;
        --primary-light: #ff6b6b;
        --success: #28a745;
        --warning: #ffc107;
        --info: #17a2b8;
        --dark: #2d3142;
        --dark-light: #1f2235;
        --text: #ffffff;
    }

    .backoffice-notification-item {
        position: relative;
    }

    .backoffice-notification-bell {
        position: relative;
        display: flex;
        align-items: center;
        font-size: 1.8rem;
        color: var(--text);
        cursor: pointer;
        padding: 6px 4px;
        transition: all 0.3s ease;
    }

    .backoffice-notification-bell:hover {
        color: var(--primary);
        transform: scale(1.1);
    }

    .backoffice-notif-badge {
        position: absolute;
        top: 0;
        right: -8px;
        background: var(--primary);
        color: #fff;
        font-size: 0.65rem;
        padding: 2px 6px;
        border-radius: 999px;
        border: 2px solid var(--dark);
        line-height: 1;
        min-width: 18px;
        text-align: center;
        font-weight: 700;
    }

    .backoffice-notification-dropdown {
        position: fixed;
        right: 20px;
        top: 80px;
        width: 380px;
        background: var(--dark);
        border: 1px solid rgba(255, 74, 87, 0.3);
        border-radius: 12px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        overflow: hidden;
        display: none;
        z-index: 1500;
    }

    .backoffice-notification-dropdown.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .backoffice-notif-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        background: linear-gradient(45deg, var(--primary), var(--primary-light));
        color: #fff;
        font-weight: 700;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .backoffice-notif-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        min-width: 24px;
        text-align: center;
    }

    .backoffice-notif-list {
        max-height: 420px;
        overflow-y: auto;
        background: var(--dark);
    }

    .backoffice-notif-list::-webkit-scrollbar {
        width: 6px;
    }

    .backoffice-notif-list::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
    }

    .backoffice-notif-list::-webkit-scrollbar-thumb {
        background: rgba(255, 74, 87, 0.4);
        border-radius: 3px;
    }

    .backoffice-notif-list::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 74, 87, 0.6);
    }

    .backoffice-notif-item {
        padding: 14px 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: flex-start;
        gap: 12px;
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .backoffice-notif-item:hover {
        background: rgba(255, 74, 87, 0.1);
        border-left: 3px solid var(--primary);
        padding-left: 13px;
    }

    .backoffice-notif-item.unread {
        background: rgba(255, 74, 87, 0.08);
        font-weight: 500;
    }

    .backoffice-notif-item:last-child {
        border-bottom: none;
    }

    .backoffice-notif-icon {
        min-width: 28px;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 14px;
    }

    .backoffice-notif-icon.reclamation {
        background: rgba(255, 74, 87, 0.2);
        color: var(--primary);
    }

    .backoffice-notif-icon.event {
        background: rgba(40, 167, 69, 0.2);
        color: var(--success);
    }

    .backoffice-notif-icon.mission {
        background: rgba(255, 193, 7, 0.2);
        color: var(--warning);
    }

    .backoffice-notif-icon.feedback {
        background: rgba(23, 162, 184, 0.2);
        color: var(--info);
    }

    .backoffice-notif-content {
        flex: 1;
        min-width: 0;
    }

    .backoffice-notif-title {
        font-weight: 600;
        color: #fff;
        font-size: 0.95rem;
        margin-bottom: 3px;
    }

    .backoffice-notif-body {
        font-size: 0.88rem;
        color: rgba(255, 255, 255, 0.75);
        margin-bottom: 3px;
    }

    .backoffice-notif-text {
        font-size: 0.82rem;
        color: rgba(255, 255, 255, 0.6);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .backoffice-notif-date {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.45);
        margin-top: 4px;
    }

    .backoffice-notif-empty {
        padding: 30px 16px;
        text-align: center;
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.9rem;
    }

    .backoffice-notif-footer {
        text-align: center;
        padding: 10px 12px;
        background: rgba(255, 74, 87, 0.05);
        border-top: 1px solid rgba(255, 255, 255, 0.08);
    }

    .backoffice-notif-footer a {
        color: var(--primary);
        font-weight: 600;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .backoffice-notif-footer a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .backoffice-notification-dropdown {
            width: calc(100vw - 40px);
            right: 20px;
        }
    }
</style>

<div class="backoffice-notification-item">
    <a class="backoffice-notification-bell" href="#" id="backofficeNotificationBell" title="Notifications">
        <i class="fas fa-bell"></i>
        <?php if ($notificationCount > 0): ?>
            <span class="backoffice-notif-badge"><?= $notificationCount ?></span>
        <?php endif; ?>
    </a>

    <div class="backoffice-notification-dropdown" id="backofficeNotificationDropdown">
        <div class="backoffice-notif-header">
            <span>Notifications</span>
            <?php if ($notificationCount > 0): ?>
                <span class="backoffice-notif-count"><?= $notificationCount ?></span>
            <?php endif; ?>
        </div>

        <div class="backoffice-notif-list">
            <?php if (empty($notifications)): ?>
                <div class="backoffice-notif-empty">
                    <i class="fas fa-inbox" style="font-size: 28px; opacity: 0.5; display: block; margin-bottom: 8px;"></i>
                    Aucune notification pour l'instant
                </div>
            <?php else: ?>
                <?php foreach ($notifications as $notif): ?>
                    <?php 
                    $iconClass = 'reclamation';
                    if ($notif['type'] === 'event_participation') $iconClass = 'event';
                    elseif ($notif['type'] === 'mission_candidature') $iconClass = 'mission';
                    elseif ($notif['type'] === 'feedback') $iconClass = 'feedback';
                    
                    $nkey = $notif['key'] ?? md5(($notif['title'] ?? '') . '|' . ($notif['body'] ?? '') . '|' . ($notif['date'] ?? ''));
                    ?>
                    <div class="backoffice-notif-item unread" 
                       data-key="<?= htmlspecialchars($nkey) ?>"
                       onclick="openPage('<?= htmlspecialchars($notif['link'] ?? '#') ?>'); document.getElementById('backofficeNotificationDropdown').classList.remove('show');"
                       style="cursor: pointer;">
                        <div class="backoffice-notif-icon <?= htmlspecialchars($iconClass) ?>">
                            <i class="<?= htmlspecialchars($notif['icon'] ?? 'fas fa-bell') ?>"></i>
                        </div>
                        <div class="backoffice-notif-content">
                            <div class="backoffice-notif-title"><?= htmlspecialchars($notif['title'] ?? 'Notification') ?></div>
                            <div class="backoffice-notif-body"><?= htmlspecialchars($notif['body'] ?? '') ?></div>
                            <?php if (!empty($notif['text'])): ?>
                                <div class="backoffice-notif-text"><?= htmlspecialchars($notif['text']) ?></div>
                            <?php endif; ?>
                            <?php if (!empty($notif['date'])): ?>
                                <div class="backoffice-notif-date">
                                    <?= date('d/m/Y H:i', strtotime($notif['date'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bell = document.getElementById('backofficeNotificationBell');
        const dropdown = document.getElementById('backofficeNotificationDropdown');
        const notifItems = document.querySelectorAll('.backoffice-notif-item');

        function loadSeen() {
            try {
                return JSON.parse(localStorage.getItem('engage_backoffice_seen_notifs') || '[]');
            } catch (e) { return []; }
        }

        function saveSeen(arr) {
            localStorage.setItem('engage_backoffice_seen_notifs', JSON.stringify(arr));
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
            const badge = document.querySelector('.backoffice-notif-badge');
            if (badge) {
                if (unread > 0) {
                    badge.textContent = unread;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
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

        if (bell && dropdown) {
            bell.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && e.target !== bell && !bell.contains(e.target)) {
                    dropdown.classList.remove('show');
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
