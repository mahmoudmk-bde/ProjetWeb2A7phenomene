<?php
require_once "../../Controller/ReclamationController.php";

$ctrl = new ReclamationController();
$ctrl->deleteReclamation($_GET['id']);
header("Location: listReclamation.php");
exit;
