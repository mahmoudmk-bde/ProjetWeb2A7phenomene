<?php
include_once __DIR__ . '/../../Controller/QuizController.php';

$error = "";
$quizController = new QuizController();

if (
    isset($_POST["titre"]) && isset($_POST["contenu"]) && isset($_POST["date_publication"])
) {
    if (
        !empty($_POST["titre"]) && !empty($_POST["contenu"]) && !empty($_POST["date_publication"])
    ) {
            $article = new Article(
                null,
                $_POST['titre'],
                $_POST['contenu'],
                new DateTime($_POST['date_publication'])
            );
            $result = $quizController->addArticle($article);
            header('Location: listeArticle.php');
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
    <title>Ajouter un Article - Plateforme Quiz</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/addarticle.css">
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
                        <a href="addarticle.php" class="active">
                            <i class="fas fa-plus"></i> Ajouter un article
                        </a>
                    </li>
                    <li>
                        <a href="addquiz.php">
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
                <h1>Ajouter un Nouvel Article</h1>
                <p>Créez un nouvel article pour votre plateforme</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="form-feedback error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="quiz-form">
                <div class="form-group">
                    <label for="titre" class="required">Titre de l'article</label>
                    <input type="text" id="titre" name="titre" placeholder="Entrez le titre de l'article..." >
                </div>

                <div class="form-group">
                    <label for="contenu" class="required">Contenu de l'article</label>
                    <textarea id="contenu" name="contenu" rows="8" placeholder="Rédigez le contenu de votre article..." ></textarea>
                    <div class="char-counter" style="font-size: 0.75rem; color: #8a8da5; text-align: right; margin-top: 5px;">0 caractères</div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_publication" class="required">Date de publication</label>
                        <input type="date" id="date_publication" name="date_publication" >
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="quiz-btn quiz-btn-secondary" onclick="location.href='index.php'">Annuler</button>
                    <button type="submit" class="quiz-btn quiz-btn-primary">Ajouter l'Article</button>
                </div>
            </form>
        </div>
    </div>
    <script src="js/addarticlee.js"></script>
</body>
</html>