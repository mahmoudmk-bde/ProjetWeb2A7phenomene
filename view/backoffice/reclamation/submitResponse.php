<?php
session_start();
require_once __DIR__ . '/../../controller/ResponseController.php';

$respCtrl = new ResponseController();
if (isset($_POST['id'], $_POST['response'])) {
    $respCtrl->addResponse($_POST['id'], $_POST['response']);
    // Set a flash message to inform admin that email was sent
    $_SESSION['flash_success'] = 'La réponse a été envoyée et l\'email a été transmis avec succès.';
}
header("Location: listReclamation.php");
exit;
