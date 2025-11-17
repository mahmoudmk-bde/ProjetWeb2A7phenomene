<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();
$quizController->deleteQuiz($_GET['id']);
header('Location: index.php');
?>