<?php
include '../../../Controller/QuizController.php';
$quiz = new QuizController();
$quiz->deleteQuiz($_GET["id"]);
header('Location: listeQuiz.php');
?>


