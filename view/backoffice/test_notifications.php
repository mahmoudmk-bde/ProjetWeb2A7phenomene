<?php
session_start();

// Debug: Check if admin is logged in
if (!isset($_SESSION['user_id'])) {
    die('Not logged in');
}

require_once __DIR__ . '/../../controller/NotificationController.php';

$notifController = new NotificationController();
$notifications = $notifController->getBackofficeNotifications(20);

echo '<pre>';
echo "Total Notifications: " . count($notifications) . "\n\n";

foreach ($notifications as $notif) {
    echo "---\n";
    echo "Type: " . $notif['type'] . "\n";
    echo "Title: " . $notif['title'] . "\n";
    echo "Body: " . $notif['body'] . "\n";
    echo "Link: " . $notif['link'] . "\n";
    echo "Date: " . $notif['date'] . "\n";
    echo "\n";
}
echo '</pre>';
?>
