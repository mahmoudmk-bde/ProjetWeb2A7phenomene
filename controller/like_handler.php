<?php
session_start();
require_once __DIR__ . '/../db_config.php';
require_once __DIR__ . '/LikeController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour liker']);
    exit;
}

if (!isset($_POST['mission_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID de mission manquant']);
    exit;
}

$mission_id = intval($_POST['mission_id']);
$utilisateur_id = $_SESSION['user_id'];

try {
    $likeController = new LikeController();
    $result = $likeController->toggleLike($mission_id, $utilisateur_id);
    $likeCount = $likeController->getLikeCount($mission_id);
    
    echo json_encode([
        'success' => true,
        'liked' => $result['liked'],
        'count' => $likeCount,
        'action' => $result['action']
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur: ' . $e->getMessage()
    ]);
}

