<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();
$quizsSante = $quizController->getQuizByArticle(1);
$quizsEnvironnement = $quizController->getQuizByArticle(2);
$quizsEducation = $quizController->getQuizByArticle(3);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style1.css">
    <link rel="icon" href="img/favicon.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <!-- animate CSS -->
    <link rel="stylesheet" href="css/animate.css" />
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="css/owl.carousel.min.css" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="css/all.css" />
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="css/flaticon.css" />
    <link rel="stylesheet" href="css/themify-icons.css" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="css/magnific-popup.css" />
    <!-- swiper CSS -->
    <link rel="stylesheet" href="css/slick.css" />
    <!-- style CSS -->
    <link rel="stylesheet" href="css/style.css" />
    <title>Quiz</title>
</head>
<body>
    <div class="body_bg">
      <!--::header part start::-->
      <header class="main_menu single_page_menu">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12">
              <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="index.php">
                  <img src="img/logo.png" alt="logo" />
                </a>
                <button
                  class="navbar-toggler"
                  type="button"
                  data-toggle="collapse"
                  data-target="#navbarSupportedContent"
                  aria-controls="navbarSupportedContent"
                  aria-expanded="false"
                  aria-label="Toggle navigation"
                >
                  <span class="menu_icon"><i class="fas fa-bars"></i></span>
                </button>

                <div
                  class="collapse navbar-collapse main-menu-item"
                  id="navbarSupportedContent"
                >
                  <ul class="navbar-nav">
                    <li class="nav-item">
                      <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="fighter.html">mission</a>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="team.html">gamification</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="team.html">reclamation</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="team.html">evenement</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="quiz.php">education</a>
                      </li>
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="contact.html">Contact</a>
                    </li>
                  </ul>
                </div>
                <a href="#" class="btn_1 d-none d-sm-block">se connecter</a>
              </nav>
            </div>
          </div>
        </div>
      </header>
    
    <div class="container1">
        <h1>Choisissez un article pour commencer le quiz</h1>
        <div class="grid" id="articles">
            <div class="card" id="sante-card">
                <img class="thumb" src="sante.jpg" alt="Article 1">
                <div class="title">Article 1 : Santé</div>
            </div>
            <div class="card" id="environnement-card">
                <img class="thumb" src="enviro.jpg" alt="Article 2">
                <div class="title">Article 2 : Environnement</div>

            </div>
            <div class="card" id="education-card">
                <img class="thumb" src="education.jpg" alt="Article 3">
                <div class="title">Article 3 : Education</div>
            </div>
        </div>
    </div>

    <!-- Modal for Health Quiz -->
    <div id="quizModalSante" class="modal">
        <div class="modal-content">
            <span class="close-btn">X</span>
            <h2 class="quiz-title">Quiz Santé</h2>
            <form id="quizFormSante">
                <?php $quizController->generateQuizQuestions($quizsSante, 'sante'); ?>
                <button type="button" class="submit-btn" data-quiz="sante">Soumettre le quiz</button>
                <div id="quizResultSante" class="result"></div>
            </form>
        </div>
    </div>

    <!-- Modal for Environment Quiz -->
    <div id="quizModalEnvironnement" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 class="quiz-title">Quiz Environnement</h2>
            <form id="quizFormEnvironnement">
                <?php $quizController->generateQuizQuestions($quizsEnvironnement, 'environnement'); ?>
                <button type="button" class="submit-btn" data-quiz="environnement">Soumettre le quiz</button>
                <div id="quizResultEnvironnement" class="result"></div>
            </form>
        </div>
    </div>

    <!-- Modal for Education Quiz -->
    <div id="quizModalEducation" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h2 class="quiz-title">Quiz Éducation</h2>
            <form id="quizFormEducation">
                <?php $quizController->generateQuizQuestions($quizsEducation, 'education'); ?>
                <button type="button" class="submit-btn" data-quiz="education">Soumettre le quiz</button>
                <div id="quizResultEducation" class="result"></div>
            </form>
        </div>
    </div>
</div>

    <script src="j.js"></script>
</body>
</html>