<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
include_once __DIR__ . '/../../Model/Quiz.php';

$error = "";
$quizController = new QuizController();

$articlesResult = $quizController->listArticle();
$articles = [];
if ($articlesResult) {
    $articles = $articlesResult->fetchAll();
}

if (
    isset($_POST["question"]) && isset($_POST["reponse1"]) && isset($_POST["reponse2"]) && isset($_POST["reponse3"]) && isset($_POST["bonne_reponse"]) && isset($_POST["id_article"])
) {
    if (
        !empty($_POST["question"]) && !empty($_POST["reponse1"]) && !empty($_POST["reponse2"]) && !empty($_POST["reponse3"]) && !empty($_POST["bonne_reponse"]) && !empty($_POST["id_article"]) 
    ) {
            $quiz = new Quiz(
                null,
                $_POST['question'],
                $_POST['reponse1'],
                $_POST['reponse2'],
                $_POST['reponse3'],
                $_POST['bonne_reponse'],
                $_POST['id_article']
            );
            $result = $quizController->addQuiz($quiz);
            header('Location: listeQuiz.php');
            exit;
    } else {
        $error = "Missing information";
    }
}

?> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Quiz - Plateforme Quiz</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/addquiz.css">
</head>
<body>
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="img/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" /> 
            <h3>Backoffice</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="#gestion-submenu" data-bs-toggle="collapse" aria-expanded="true" class="dropdown-toggle">
                    <i class="fas fa-tachometer-alt"></i> Gestion d'article et quiz
                </a>
                <ul class="collapse list-unstyled show" id="gestion-submenu">
                    <li>
                        <a href="index.php">
                            <i class="fas fa-home"></i> Tableau de Bord
                        </a>
                    </li>
                    <li>
                        <a href="addarticle.php">
                            <i class="fas fa-plus"></i> Ajouter un article
                        </a>
                    </li>
                    <li>
                        <a href="addquiz.php" class="active">
                            <i class="fas fa-plus"></i> Ajouter un quiz
                        </a>
                    </li>
                    <li>
                        <a href="listeArticle.php">
                            <i class="fas fa-list"></i> Liste des articles
                        </a>
                    </li>
                    <li>
                        <a href="listeQuiz.php">
                            <i class="fas fa-list"></i> Liste des quiz
                        </a>
                    </li>
                     <li>
                        <a href="listeHistorique.php">
                            <i class="fas fa-history"></i> Historique
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="main-content">
        <div class="quiz-form-container">
        <div class="form-header">
            <h1>Ajouter un Nouveau Quiz</h1>
            <p>Créez une nouvelle question de quiz pour votre plateforme</p>
        </div>
    <form method="POST" action="" class="quiz-form">
        <div class="form-group">
            <label for="question" class="required">Question</label>
            <textarea id="question" name="question" rows="4" placeholder="Entrez la question du quiz..." ></textarea>
        </div>
        <div class="form-row">
            <div class="answers-grid">
                <div class="form-group answer-input">
                    <label for="reponse1" class="required">Réponse A</label>
                    <input type="text" id="reponse1" name="reponse1" placeholder="Première réponse..." >
                </div>
                <div class="form-group answer-input">
                    <label for="reponse2" class="required">Réponse B</label>
                    <input type="text" id="reponse2" name="reponse2" placeholder="Deuxième réponse..." >
                </div>

                <div class="form-group answer-input">
                    <label for="reponse3" class="required">Réponse C</label>
                    <input type="text" id="reponse3" name="reponse3" placeholder="Troisième réponse..." >
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bonne_reponse" class="required">Bonne Réponse</label>
                <select id="bonne_reponse" name="bonne_reponse" class="bonne-reponse-select" >
                    <option value="">Sélectionnez la bonne réponse</option>
                    <option value="1">A - Première réponse</option>
                    <option value="2">B - Deuxième réponse</option>
                    <option value="3">C - Troisième réponse</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_article" class="required">Article Associé</label>
                <select id="id_article" name="id_article" class="article-select" >
                    <option value="">Sélectionnez un article</option>
                    <?php foreach($articles as $article){ ?>
                        <option value="<?php echo $article['id_article']; ?>" <?php echo (isset($_POST['id_article']) && $_POST['id_article'] == $article['id_article']) ? 'selected' : ''; ?>>
                            #<?php echo $article['id_article']; ?> - <?php echo $article['titre']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <button type="button" class="quiz-btn quiz-btn-secondary" onclick="location.href='index.php'">Annuler</button>
            <button type="submit" class="quiz-btn quiz-btn-primary">Ajouter le Quiz</button>
        </div>
    </form>
</div>
</div>
    <script src="js/addquizz.js"></script>
</body>
</html>