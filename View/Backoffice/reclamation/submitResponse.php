<?php
session_start();
require_once __DIR__ . '/../../controller/ResponseController.php';

$respCtrl = new ResponseController();
if (isset($_POST['id'], $_POST['response'])) {
    $respCtrl->addResponse($_POST['id'], $_POST['response']);
}
header("Location: listReclamation.php");
exit;
