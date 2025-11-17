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
        $quizController->addQuiz($quiz);
        header('Location: index.php');
        exit;
    } else {
        $error = "Tous les champs sont obligatoires";
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
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/all.css" />
    <link rel="stylesheet" href="Styleadd.css">
</head>
<body>
          <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="img/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" /> 
            <h3> Backoffice</h3>
        </div>

       <ul class="list-unstyled components">
    <li class="active">
        <a href="#gestion-submenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <i class="fas fa-tachometer-alt"></i> Gestion d'article et quiz
        </a>
        <ul class="collapse list-unstyled" id="gestion-submenu">
            <li>
                <a href="index.php"><i class="fas fa-home"></i> Tableau de Bord</a>
            </li>
            <li>
                <a href="addquiz.php"><i class="fas fa-plus-circle"></i> Ajouter Quiz</a>
            </li>
            <li>
                <a href="index.php"><i class="fas fa-list"></i> Liste Articles</a>
            </li>
            <li>
                <a href="index.php"><i class="fas fa-list"></i> Liste Quiz</a>
            </li>
            <li>
                <a href="index.php"><i class="fas fa-list"></i> Statistiques</a>
            </li>
        </ul>
    </li>
</ul>
    </nav>
    <a href="index.php" class="back-link">← Retour au tableau de bord</a>
    <div class="quiz-form-container">
        <div class="form-header">
            <h1>Ajouter un Nouveau Quiz</h1>
            <p>Créez une nouvelle question de quiz pour votre plateforme</p>
        </div>
    <form method="POST" action="" class="quiz-form">
        <div class="form-group">
            <label for="question" class="required">Question</label>
            <textarea id="question" name="question" rows="4" placeholder="Entrez la question du quiz..." required></textarea>
        </div>
        <div class="form-row">
            <div class="answers-grid">
                <div class="form-group answer-input">
                    <label for="reponse1" class="required">Réponse A</label>
                    <input type="text" id="reponse1" name="reponse1" placeholder="Première réponse..." required>
                </div>
                <div class="form-group answer-input">
                    <label for="reponse2" class="required">Réponse B</label>
                    <input type="text" id="reponse2" name="reponse2" placeholder="Deuxième réponse..." required>
                </div>

                <div class="form-group answer-input">
                    <label for="reponse3" class="required">Réponse C</label>
                    <input type="text" id="reponse3" name="reponse3" placeholder="Troisième réponse..." required>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="bonne_reponse" class="required">Bonne Réponse</label>
                <select id="bonne_reponse" name="bonne_reponse" class="bonne-reponse-select" required>
                    <option value="">Sélectionnez la bonne réponse</option>
                    <option value="1">A - Première réponse</option>
                    <option value="2">>B - Deuxième réponse</option>
                    <option value="3">C - Troisième réponse</option>
                </select>
            </div>

            <div class="form-group">
                <label for="id_article" class="required">Article Associé</label>
                <select id="id_article" name="id_article" class="article-select" required>
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
    <script src="j1.js"></script>
</body>
</html>