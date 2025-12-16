<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();
$count_articles = $quizController->getArticlesCount();
$count_quiz = $quizController->getQuizCount();
$count_historique = $quizController->getHistoriqueCount(); // Nouveau

// Récupérer les derniers articles, quiz et historique
$articlesResult = $quizController->listArticle();
$articles = [];
if ($articlesResult) {
    $articles = array_slice($articlesResult->fetchAll(), 0, 5);
}

$quizResult = $quizController->listQuiz();
$quizs = [];
if ($quizResult) {
    $quizs = array_slice($quizResult->fetchAll(), 0, 5);
}

// Récupérer l'historique récent
$historique = $quizController->getAllHistorique();
$recent_historique = array_slice($historique, 0, 5); // 5 derniers historiques
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Tableau de Bord - Plateforme Quiz</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/indexx.css">
    <style>
        /* Styles supplémentaires pour l'historique */
        .historique-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: var(--accent-color);
            border-radius: 10px;
            border: 1px solid var(--border-color);
            margin-bottom: 10px;
            transition: var(--transition);
        }
        
        .historique-item:hover {
            border-color: var(--primary-color);
            transform: translateX(5px);
        }
        
        .historique-info h4 {
            margin: 0 0 8px 0;
            color: var(--text-color);
            font-size: 1rem;
        }
        
        .historique-meta {
            display: flex;
            gap: 15px;
            font-size: 0.875rem;
            color: var(--gray-color);
        }
        
        .score-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .score-high {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.2), rgba(32, 201, 151, 0.1));
            color: var(--success-color);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .score-medium {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 193, 7, 0.1));
            color: var(--warning-color);
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .score-low {
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.2), rgba(220, 53, 69, 0.1));
            color: var(--danger-color);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }
        
        .user-info i {
            color: var(--primary-color);
        }
    </style>
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
                        <a href="index.php" class="active">
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
            <!-- Header avec bienvenue -->
            <header class="dashboard-header">
                <div class="welcome-section">
                    <h1>Bonjour, Administrateur !</h1>
                    <p class="welcome-text">Bienvenue sur votre tableau de bord de gestion d'article et quiz</p>
                </div>
                <div class="header-actions">
                    <button class="btn-action btn-primary" onclick="location.href='addarticle.php'">
                        <i class="fas fa-file-alt"></i> Nouvel Article
                    </button>
                    <button class="btn-action btn-secondary" onclick="location.href='addquiz.php'">
                        <i class="fas fa-plus-circle"></i> Nouveau Quiz
                    </button>
                </div>
            </header>

            <!-- Section Statistiques -->
            <section class="stats-section">
                <h2 class="section-title">
                    <i class="fas fa-chart-line"></i> Aperçu Global
                </h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-newspaper"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $count_articles; ?></h3>
                            <p>Articles Publiés</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $count_quiz; ?></h3>
                            <p>Quiz Créés</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo ($count_articles + $count_quiz + $count_historique); ?></h3>
                            <p>Total Données</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section Contenu Récent -->
            <div class="content-grid">
                <!-- Derniers Articles -->
                <section class="content-section">
                    <div class="section-header">
                        <h2>
                            <i class="fas fa-newspaper"></i> Derniers Articles
                        </h2>
                        <a href="listeArticle.php" class="view-all">
                            Voir tout <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="content-list">
                        <?php if (!empty($articles)): ?>
                            <?php foreach($articles as $article): ?>
                                <div class="content-item">
                                    <div class="item-info">
                                        <h4><?php echo $article['titre']; ?></h4>
                                        <p class="item-date">
                                            <i class="far fa-calendar"></i> 
                                            <?php echo date('d/m/Y', strtotime($article['date_publication'])); ?>
                                        </p>
                                    </div>
                                    <div class="item-actions">
                                        <a href="updateArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-newspaper"></i>
                                <p>Aucun article publié</p>
                                <a href="addarticle.php" class="btn-action btn-primary">Créer un article</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <!-- Derniers Quiz -->
                <section class="content-section">
                    <div class="section-header">
                        <h2>
                            <i class="fas fa-question-circle"></i> Derniers Quiz
                        </h2>
                        <a href="listeQuiz.php" class="view-all">
                            Voir tout <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="content-list">
                        <?php if (!empty($quizs)): ?>
                            <?php foreach($quizs as $quiz): ?>
                                <div class="content-item">
                                    <div class="item-info">
                                        <h4><?php echo substr($quiz['question'], 0, 50) . '...'; ?></h4>
                                        <p class="item-meta">
                                            <span class="correct-badge">
                                                Réponse: <?php echo $quiz['bonne_reponse']; ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="item-actions">
                                        <a href="updateQuiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-question-circle"></i>
                                <p>Aucun quiz créé</p>
                                <a href="addquiz.php" class="btn-action btn-primary">Créer un quiz</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Section Historique Récent -->
            <section class="content-section" style="margin-bottom: 30px;">
                <div class="section-header">
                    <h2>
                        <i class="fas fa-history"></i> Historique Récent
                    </h2>
                    <a href="listeHistorique.php" class="view-all">
                        Voir tout <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="content-list">
                    <?php if (!empty($recent_historique)): ?>
                        <?php foreach($recent_historique as $historique): ?>
                            <div class="historique-item">
                                <div class="historique-info">
                                    <div class="user-info">
                                        <i class="fas fa-user"></i>
                                        <strong><?php echo $historique['username'] ?? 'Utilisateur'; ?></strong>
                                    </div>
                                    <h4><?php echo $historique['article_titre'] ?? 'Quiz'; ?></h4>
                                    <div class="historique-meta">
                                        <span>
                                            <i class="far fa-calendar"></i>
                                            <?php echo date('d/m/Y H:i', strtotime($historique['date_tentative'])); ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-question-circle"></i>
                                            Quiz ID: <?php echo $historique['id_quiz']; ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <?php
                                    $score = $historique['score'];
                                    $scoreClass = 'score-high';
                                    if ($score < 50) {
                                        $scoreClass = 'score-low';
                                    } elseif ($score < 80) {
                                        $scoreClass = 'score-medium';
                                    }
                                    ?>
                                    <span class="score-badge <?php echo $scoreClass; ?>">
                                        Score: <?php echo $score; ?>%
                                    </span>
                                    <button class="btn-action btn-danger" 
                                            onclick="deleteHistorique(<?php echo $historique['id_historique']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <p>Aucun historique disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Section Actions Rapides -->
            <section class="quick-actions-section">
                <h2 class="section-title">
                    <i class="fas fa-bolt"></i> Actions Rapides
                </h2>
                <div class="actions-grid">
                    <div class="action-card" onclick="location.href='addarticle.php'">
                        <div class="action-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3>Nouvel Article</h3>
                        <p>Créer un nouvel article</p>
                    </div>
                    
                    <div class="action-card" onclick="location.href='addquiz.php'">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3>Nouveau Quiz</h3>
                        <p>Créer un nouveau quiz</p>
                    </div>
                    
                    <div class="action-card" onclick="location.href='listeArticle.php'">
                        <div class="action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <h3>Gérer Articles</h3>
                        <p>Voir tous les articles</p>
                    </div>
                    
                    <div class="action-card" onclick="location.href='listeQuiz.php'">
                        <div class="action-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h3>Gérer Quiz</h3>
                        <p>Voir tous les quiz</p>
                    </div>
                    
                    <div class="action-card" onclick="location.href='listeHistorique.php'">
                        <div class="action-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3>Voir Historique</h3>
                        <p>Consulter tout l'historique</p>
                    </div>
                </div>
            </section>
        </div>
    </div>
    
    <script>
    function deleteHistorique(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet historique ?')) {
            fetch('deleteHistorique.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Historique supprimé avec succès');
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de la suppression');
                });
        }
    }
    </script>
    <script src="js/index.js"></script>
</body>
</html>