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
    <title>Détails Article - Backoffice</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/detailarticle.css">
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
            <section class="content-section" id="article-detail-section">
                
                    <!-- En-tête de l'article -->
                    <div class="article-header">
                        <div class="article-title-section">
                            <h1 class="article-title">
                                <i class="fas fa-newspaper"></i>
                                <?php echo $article['titre']; ?>
                            </h1>
                            <div class="article-meta">
                                <div class="meta-item">
                                    <i class="fas fa-hashtag"></i>
                                    <strong>ID:</strong> #<?php echo $article['id_article']; ?>
                                </div>
                                <div class="meta-item">
                                    <i class="far fa-calendar"></i>
                                    <strong>Publié le:</strong> <?php echo date('d/m/Y', strtotime($article['date_publication'])); ?>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-question-circle"></i>
                                    <strong>Quiz associés:</strong> <?php echo count($quizs); ?>
                                </div>
                            </div>
                        </div>
                        <div class="header-actions">
                            <a href="listeArticle.php" class="btn-action btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <a href="updateArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-edit">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="quizArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-quiz">
                                <i class="fas fa-list"></i> Voir les Quiz
                            </a>
                        </div>
                    </div>

                    <!-- Contenu de l'article -->
                    <div class="article-content-section">
                        <div class="section-header">
                            <h2>
                                <i class="fas fa-align-left"></i>
                                Contenu de l'Article
                            </h2>
                           
                        </div>
                        
                        <div class="article-content">
                            <?php echo $article['contenu']; ?>
                        </div>
                    </div>

                    <!-- Statistiques et informations -->
                    <div class="stats-section">
                        <div class="stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-hashtag"></i>
                                </div>
                                <div class="stat-info">
                                    <h3>#<?php echo $article['id_article']; ?></h3>
                                    <p>ID Article</p>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo count($quizs); ?></h3>
                                    <p>Quiz Associés</p>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="far fa-calendar"></i>
                                </div>
                                <div class="stat-info">
                                    <h3><?php echo date('d/m/Y', strtotime($article['date_publication'])); ?></h3>
                                    <p>Date Publication</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quiz associés (aperçu) -->
                    <?php if (!empty($quizs)): ?>
                    <div class="quiz-preview-section">
                        <div class="section-header">
                            <h2>
                                <i class="fas fa-question-circle"></i>
                                Quiz Associés (<?php echo count($quizs); ?>)
                            </h2>
                            <a href="quizArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-primary">
                                <i class="fas fa-list"></i> Voir tous les quiz
                            </a>
                        </div>
                        
                        <div class="quiz-preview">
                            <?php foreach(array_slice($quizs, 0, 3) as $index => $quiz): ?>
                                <div class="quiz-preview-card">
                                    <div class="quiz-preview-header">
                                        <h4>Quiz #<?php echo $index + 1; ?></h4>
                                        <span class="quiz-id">#<?php echo $quiz['id_quiz']; ?></span>
                                    </div>
                                    <p class="quiz-question">
                                        <?php echo htmlspecialchars(substr($quiz['question'], 0, 80)); ?>
                                        <?php if (strlen($quiz['question']) > 80): ?>...<?php endif; ?>
                                    </p>
                                    <div class="quiz-meta">
                                        <span class="correct-answer">
                                            <i class="fas fa-check-circle"></i>
                                            Réponse: <?php echo $quiz['bonne_reponse']; ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (count($quizs) > 3): ?>
                                <div class="more-quiz-card">
                                    <div class="more-quiz-content">
                                        <i class="fas fa-ellipsis-h"></i>
                                        <p>+<?php echo count($quizs) - 3; ?> autres quiz</p>
                                        <a href="quizArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-secondary">
                                            Voir tout
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="no-quiz-section">
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h3>Aucun quiz associé</h3>
                            <p>Cet article n'a pas encore de quiz. Créez-en un pour tester les connaissances de vos lecteurs.</p>
                            <a href="addquiz.php?article_id=<?php echo $article['id_article']; ?>" class="btn-action btn-primary">
                                <i class="fas fa-plus-circle"></i> Créer un quiz
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Actions rapides -->
                    <div class="quick-actions-section">
                        <h3 class="section-title">
                            <i class="fas fa-bolt"></i> Actions Rapides
                        </h3>
                        <div class="actions-grid">
                            <a href="updateArticle.php?id=<?php echo $article['id_article']; ?>" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <h4>Modifier l'Article</h4>
                                <p>Éditer le titre et le contenu</p>
                            </a>
                            
                            <a href="quizArticle.php?id=<?php echo $article['id_article']; ?>" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-list"></i>
                                </div>
                                <h4>Gérer les Quiz</h4>
                                <p>Voir tous les quiz associés</p>
                            </a>
                            
                            <a href="addquiz.php?article_id=<?php echo $article['id_article']; ?>" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <h4>Nouveau Quiz</h4>
                                <p>Créer un quiz pour cet article</p>
                            </a>
                            
                            <a href="listeArticle.php" class="action-card">
                                <div class="action-icon">
                                    <i class="fas fa-arrow-left"></i>
                                </div>
                                <h4>Retour aux Articles</h4>
                                <p>Liste complète des articles</p>
                            </a>
                        </div>
                    </div>
            </section>
        </div>
    </div>

</body>
</html>