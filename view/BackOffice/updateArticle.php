<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
include_once __DIR__ . '/../../Model/Quiz.php';

$error = '';
$article = null;
$quizController = new QuizController();

if(isset($_GET['id'])) {
    $article = $quizController->getArticleById($_GET['id']);
}

if (
    isset($_POST['id_article'],$_POST['titre'],$_POST['contenu'],$_POST['date_publication'])
    ) {
    if (
        !empty($_POST['id_article']) && !empty($_POST['titre']) && !empty($_POST['contenu']) && !empty($_POST['date_publication'])
    ) {
        try {
            $article = new Article(
                $_POST['id_article'],
                $_POST['titre'], 
                $_POST['contenu'],
                new DateTime($_POST['date_publication'])
            );
            $quizController->updateArticle($article, $_POST['id_article']);
            header('Location: listeArticle.php');
            exit();
        } catch (Exception $e) {
            $error = 'Erreur de format de date: ' . $e->getMessage();
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Article</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/UpdateArt.css">
</head>
<body>
    <!-- Sidebar -->
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

    <div class="form-container">
        <h1>Modifier l'Article</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message" style="background: rgba(220, 53, 69, 0.1); color: #f8d7da; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #dc3545;">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <input type="hidden" id="id_article" name="id_article" value="<?php echo $article['id_article'] ?? ''; ?>">
            
            <div class="form-group">
                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($article['titre'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="contenu">Contenu</label>
                <textarea id="contenu" name="contenu" rows="6" required><?php echo htmlspecialchars($article['contenu'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="date_publication">Date de Publication</label>
                <input type="date" id="date_publication" name="date_publication" value="<?php echo $article['date_publication'] ?? ''; ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn">Modifier l'Article</button>
                <a href="listeArticle.php" class="btn btn-secondary">Retour au la liste d'Article</a>
            </div>
        </form>
    </div>
    <script src="js/updateArticle.js"></script>

</body>
</html>