<?php
session_start();

// Use forward slashes which PHP handles correctly on Windows
$base_dir = str_replace('\\', '/', __DIR__) . '/../../../';
require_once $base_dir . 'controller/ReclamationController.php';
require_once $base_dir . 'controller/ResponseController.php';

if (!isset($_GET['id']) || !$_GET['id']) {
    header('Location: listReclamation.php'); 
    exit;
}

$id = intval($_GET['id']);
$recCtrl = new ReclamationController();
$respCtrl = new ResponseController();

// Vérifier que la réclamation existe
$rec = $recCtrl->getReclamation($id);
if (!$rec) { 
    header('Location: listReclamation.php'); 
    exit; 
}

// Supprimer toutes les réponses associées d'abord (cascade)
$responses = $respCtrl->getResponses($id);
foreach ($responses as $response) {
    $respCtrl->deleteResponse($response['id']);
}

// Supprimer la réclamation
$recCtrl->deleteReclamation($id);

// Rediriger vers la liste avec un message de succès
header('Location: listReclamation.php?deleted=1');
exit;

