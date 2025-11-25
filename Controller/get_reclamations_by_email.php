<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/config.php';

// make sure errors are returned as JSON (helpful during dev)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// allow POST or GET
$email = '';
if (isset($_POST['email'])) $email = trim($_POST['email']);
elseif (isset($_GET['email'])) $email = trim($_GET['email']);

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email invalide']);
    exit;
}

try {
    $pdo = config::getConnexion();
    $stmt = $pdo->prepare("SELECT id, sujet, description, email, date_creation, statut FROM reclamation WHERE email = :email ORDER BY date_creation DESC");
    $stmt->execute(['email' => $email]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

?>
