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
            header('Location: index.php');
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
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/all.css" />
    <link rel="stylesheet" href="UpdateArt.css">
</head>
<body>
    <div class="form-container">
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
        <h1>Modifier l'Article</h1>
        <form action="" method="POST">
            <input type="hidden" id="id_article" name="id_article" value="<?php echo $article['id_article'] ?? ''; ?>">
            
            <label for="titre">Titre</label>
            <input type="text" id="titre" name="titre" value="<?php echo $article['titre'] ?? ''; ?>" >
  
            <label for="contenu">Contenu</label>
            <textarea id="contenu" name="contenu" rows="6" ><?php echo $article['contenu'] ?? ''; ?></textarea>
        
            <label for="date_publication">Date de Publication</label>
            <input type="date" id="date_publication" name="date_publication" value="<?php echo $article['date_publication'] ?? ''; ?>" >

            <button type="submit"  class="btn">Modifier l'Article</button>
            <a href="index.php" class="btn btn-secondary">Retour au tableau de bord</a>
        </form>
        
    </div>
    <script src="j1.js"></script>
</body>
</html>