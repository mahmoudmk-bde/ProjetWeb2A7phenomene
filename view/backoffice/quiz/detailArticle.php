<?php
include_once __DIR__ . '/../../../Controller/QuizController.php';

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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/all.css" />
    <link rel="stylesheet" href="../assets/css/detailarticle.css">
    <style>
    /* Variables communes avec missionliste.php */
    :root {
        --primary-color: #ff4a57;
        --primary-light: #ff6b7a;
        --secondary-color: #1f2235;
        --accent-color: #24263b;
        --text-color: #ffffff;
        --text-muted: #b0b3c1;
        --success: #28a745;
        --warning: #ffc107;
        --danger: #dc3545;
        --info: #17a2b8;
        --border-color: #2d3047;
        --border-radius: 12px;
        --transition: all 0.3s ease;
    }
    
    body {
        background-color: var(--secondary-color);
        color: var(--text-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Form container style missionliste */
    .form-card {
        background: var(--accent-color);
        padding: 30px;
        border-radius: var(--border-radius);
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        max-width: 800px;
        width: 100%;
    }
    
    /* Header style missionliste */
    .form-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--border-color);
    }
    
    .form-header h1 {
        color: var(--text-color);
        margin: 0 0 10px 0;
        font-weight: 700;
        font-size: 2rem;
    }
    
    .form-header p {
        color: var(--text-muted);
        margin: 0;
        font-size: 1rem;
    }
    
    /* Form group style missionliste */
    .form-group-modern {
        position: relative;
        margin-bottom: 25px;
    }
    
    .form-group-modern label {
        position: absolute;
        top: -10px;
        left: 12px;
        background: var(--accent-color);
        padding: 0 8px;
        color: var(--primary-color);
        font-size: 0.85rem;
        font-weight: 600;
        z-index: 1;
    }
    
    .form-group-modern input,
    .form-group-modern textarea,
    .form-group-modern select {
        width: 100%;
        background: var(--secondary-color);
        border: 2px solid var(--border-color);
        padding: 12px 15px;
        border-radius: 8px;
        color: var(--text-color);
        font-size: 0.95rem;
        transition: var(--transition);
    }
    
    .form-group-modern input:focus,
    .form-group-modern textarea:focus,
    .form-group-modern select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.25);
    }
    
    .form-group-modern textarea {
        min-height: 120px;
        resize: vertical;
    }
    
    /* Form row style missionliste */
    .form-row-2 {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    
    .form-row-2 .form-group-modern {
        flex: 1;
        min-width: 250px;
    }
    
    /* Answers grid for quiz form */
    .answers-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    
    @media (max-width: 768px) {
        .answers-grid {
            grid-template-columns: 1fr;
        }
    }
    
    /* Form actions style missionliste */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }
    
    /* Buttons style missionliste */
    .btn {
        padding: 12px 30px;
        border-radius: 25px;
        border: none;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    
    .btn-primary {
        background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
        color: white;
    }
    
    .btn-primary:hover {
        background: linear-gradient(45deg, var(--primary-light), var(--primary-color));
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        color: white;
    }
    
    .btn-secondary {
        background: linear-gradient(45deg, var(--text-muted), #868e96);
        color: white;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(45deg, #868e96, var(--text-muted));
        transform: translateY(-2px);
        color: white;
    }
    
    /* Error message style missionliste */
    .error-message {
        background: rgba(220, 53, 69, 0.1);
        color: #f8d7da;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border: 1px solid var(--danger);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    /* Required indicator */
    .required::after {
        content: " *";
        color: var(--primary-color);
    }
    
    /* Char counter */
    .char-counter {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-align: right;
        margin-top: 5px;
    }
    
    /* Select styling */
    select {
        appearance: menulist !important;
        -webkit-appearance: menulist !important;
        -moz-appearance: menulist !important;
        background-image: none !important;
        background-color: var(--secondary-color) !important;
        color: var(--text-color) !important;
        border: 2px solid var(--border-color) !important;
        padding: 12px 15px !important;
    }
    
    select:focus {
        border-color: var(--primary-color) !important;
        box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.25) !important;
    }
    
    option {
        background-color: var(--secondary-color) !important;
        color: var(--text-color) !important;
        padding: 10px;
    }
    
    option:checked {
        background-color: var(--primary-color) !important;
        color: white !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }
        
        .form-card {
            padding: 20px;
        }
        
        .form-row-2 {
            flex-direction: column;
            gap: 0;
        }
        
        .form-row-2 .form-group-modern {
            min-width: 100%;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>
</head>

<body>
    
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
    

</body>
</html>