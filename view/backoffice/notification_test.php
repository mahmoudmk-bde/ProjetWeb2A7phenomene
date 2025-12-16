<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'admin';

require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/../controller/NotificationController.php';

$nc = new NotificationController();
$notifications = $nc->getBackofficeNotifications(20);

echo "<!DOCTYPE html>
<html>
<head>
<style>
body { font-family: Arial; background: #1f2235; color: white; padding: 20px; }
.notif { background: #2d3142; padding: 15px; margin: 10px 0; border-left: 3px solid #ff4a57; border-radius: 5px; }
.count { font-size: 24px; font-weight: bold; color: #ff4a57; margin-bottom: 20px; }
</style>
</head>
<body>
<h1>Notification Test</h1>
<div class='count'>Total Notifications: " . count($notifications) . "</div>";

if (empty($notifications)) {
    echo "<p style='color: #999;'>Aucune notification trouvée. Cela signifie que :</p>
    <ul>
        <li>Aucune réclamation avec statut 'Non traite' n'existe</li>
        <li>Aucune participation à un événement</li>
        <li>Aucune candidature en attente</li>
        <li>Aucun feedback</li>
    </ul>";
} else {
    foreach ($notifications as $n) {
        echo "<div class='notif'>
            <strong>" . htmlspecialchars($n['title']) . "</strong><br>
            <em>" . htmlspecialchars($n['body']) . "</em><br>
            <small>" . ($n['date'] ? date('d/m/Y H:i', strtotime($n['date'])) : 'N/A') . "</small>
        </div>";
    }
}

echo "</body></html>";
?>
