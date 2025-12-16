<?php
include '../../../controller/QuizController.php';
$quiz = new QuizController();
$quiz->deleteHistorique($_GET["id"]);
header('Location: listeHistorique.php');
?>