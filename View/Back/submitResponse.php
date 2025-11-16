<?php
require_once "../../Controller/ResponseController.php";

$respCtrl = new ResponseController();
if (isset($_POST['id'], $_POST['response'])) {
    $respCtrl->addResponse($_POST['id'], $_POST['response']);
}
header("Location: listReclamation.php");
exit;
