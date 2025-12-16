<?php
include_once __DIR__ . '/../../Controller/QuizController.php';

$quizController = new QuizController();
$error = "";
$article = null;
$quizs = [];

// Récupérer l'ID de l'article
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_article = $_GET['id'];
    
    // Récupérer les informations de l'article
    $article = $quizController->getArticleById($id_article);
    
    if (!$article) {
        $error = "Article non trouvé.";
    } else {
        // Récupérer les quiz associés à cet article
        $quizs = $quizController->getQuizByArticle($id_article);
    }
} else {
    $error = "ID de l'article non spécifié.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Quiz de l'Article - Backoffice</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/quizarticle.css">
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

    <div id="content">
        <div class="container">
            <section class="content-section" id="quiz-article-section">
                <?php if ($error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h2>Erreur</h2>
                        <p><?php echo htmlspecialchars($error); ?></p>
                        <a href="listeArticle.php" class="btn-action btn-primary">
                            <i class="fas fa-arrow-left"></i> Retour aux articles
                        </a>
                    </div>
                <?php else: ?>
                    <!-- En-tête de l'article -->
                    <div class="article-header">
                        <div class="article-info">
                            <h1>
                                <i class="fas fa-newspaper"></i>
                                Quiz de l'article : <?php echo htmlspecialchars($article['titre']); ?>
                            </h1>
                            <div class="article-meta">
                                <span class="article-id">ID: #<?php echo $article['id_article']; ?></span>
                                <span class="publication-date">
                                    <i class="far fa-calendar"></i>
                                    Publié le: <?php echo date('d/m/Y', strtotime($article['date_publication'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="listeArticle.php" class="btn-action btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour aux articles
                            </a>
                            <a href="addquiz.php?article_id=<?php echo $article['id_article']; ?>" class="btn-action btn-primary">
                                <i class="fas fa-plus-circle"></i> Ajouter un Quiz
                            </a>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="stats-cards">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-question-circle"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo count($quizs); ?></h3>
                                <p>Quiz associés</p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div class="stat-info">
                                <h3><?php echo $article['id_article']; ?></h3>
                                <p>ID Article</p>
                            </div>
                        </div>
                    </div>

                    <!-- Liste des quiz -->
                    <div class="quiz-section">
                        <div class="section-header">
                            <h2>
                                <i class="fas fa-list"></i>
                                Liste des Quiz
                            </h2>
                            <span class="badge"><?php echo count($quizs); ?> quiz</span>
                        </div>

                        <?php if (empty($quizs)): ?>
                            <div class="empty-state">
                                <i class="fas fa-question-circle"></i>
                                <h3>Aucun quiz trouvé</h3>
                                <p>Cet article n'a pas encore de quiz associé.</p>
                                <a href="addquiz.php?article_id=<?php echo $article['id_article']; ?>" class="btn-action btn-primary">
                                    <i class="fas fa-plus-circle"></i> Créer le premier quiz
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="quiz-grid">
                                <?php foreach($quizs as $index => $quiz): ?>
                                    <div class="quiz-card">
                                        <div class="quiz-header">
                                            <h3>Quiz #<?php echo $index + 1; ?></h3>
                                            <span class="quiz-id">ID: #<?php echo $quiz['id_quiz']; ?></span>
                                        </div>
                                        
                                        <div class="quiz-content">
                                            <div class="question-section">
                                                <h4>Question :</h4>
                                                <p class="question"><?php echo htmlspecialchars($quiz['question']); ?></p>
                                            </div>
                                            
                                            <div class="answers-section">
                                                <h4>Réponses :</h4>
                                                <div class="answers-list">
                                                    <div class="answer <?php echo $quiz['bonne_reponse'] == '1' ? 'correct' : ''; ?>">
                                                        <span class="answer-number">1</span>
                                                        <span class="answer-text"><?php echo htmlspecialchars($quiz['reponse1']); ?></span>
                                                        <?php if ($quiz['bonne_reponse'] == '1'): ?>
                                                            <span class="correct-badge"><i class="fas fa-check"></i></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="answer <?php echo $quiz['bonne_reponse'] == '2' ? 'correct' : ''; ?>">
                                                        <span class="answer-number">2</span>
                                                        <span class="answer-text"><?php echo htmlspecialchars($quiz['reponse2']); ?></span>
                                                        <?php if ($quiz['bonne_reponse'] == '2'): ?>
                                                            <span class="correct-badge"><i class="fas fa-check"></i></span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="answer <?php echo $quiz['bonne_reponse'] == '3' ? 'correct' : ''; ?>">
                                                        <span class="answer-number">3</span>
                                                        <span class="answer-text"><?php echo htmlspecialchars($quiz['reponse3']); ?></span>
                                                        <?php if ($quiz['bonne_reponse'] == '3'): ?>
                                                            <span class="correct-badge"><i class="fas fa-check"></i></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="quiz-actions">
                                            <a href="updateQuiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-edit">
                                                <i class="fas fa-edit"></i> Modifier
                                            </a>
                                            <a href="deletequiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?')">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

</body>
</html>