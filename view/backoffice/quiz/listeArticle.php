<?php
include_once __DIR__ . '/../../../Controller/QuizController.php';
$quizController = new QuizController();
$articlesResult = $quizController->listArticle();
$count_articles = $quizController->getArticlesCount();?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Backoffice - Engage</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/all.css" />
    <link rel="stylesheet" href="../assets/css/listearticlee.css">
    <link rel="stylesheet" href="../assets/css/custom-backoffice.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    :root {
        --primary-color: #ff4a57;
        --secondary-color: #1f2235;
        --accent-color: #24263b;
        --text-color: #ffffff;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --info-color: #17a2b8;
        --border-color: #2d3047;
        --danger-color: #dc3545;
        --dark-color: #15172b;
        --light-color: #f8f9fa;
        --gray-color: #6c757d;
        --text-muted: #8a8da5;
        --transition: all 0.3s ease;
        --border-radius: 12px;
        --box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }
    
    body {
        background: var(--secondary-color);
        color: var(--text-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .articles-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .articles-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .article-stat-card {
        background: var(--accent-color);
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }
    
    .article-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
    }
    
    .article-stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
        display: block;
    }
    
    .article-stat-label {
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    
    .add-article-btn {
        background: linear-gradient(45deg, var(--primary-color), #ff6b6b);
        border: none;
        border-radius: 25px;
        padding: 12px 25px;
        font-weight: 600;
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
    }
    
    .add-article-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        color: white;
    }
    
    .articles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .article-card {
        background: var(--accent-color);
        padding: 20px;
        border-radius: 15px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
    }
    
    .article-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
        border-color: var(--primary-color);
    }
    
    .article-header {
        margin-bottom: 15px;
    }
    
    .article-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--text-color);
        margin: 0 0 10px 0;
        line-height: 1.3;
    }
    
    .article-id {
        background: var(--secondary-color);
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--primary-color);
        display: inline-block;
    }
    
    .article-content {
        flex: 1;
        margin-bottom: 15px;
    }
    
    .article-excerpt {
        color: var(--text-muted);
        font-size: 0.9rem;
        line-height: 1.5;
        margin: 0;
    }
    
    .article-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 15px;
        font-size: 0.85rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
    }
    
    .meta-item i {
        color: var(--primary-color);
        width: 16px;
        text-align: center;
    }
    
    .article-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid var(--border-color);
    }
    
    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: var(--transition);
        border: none;
        font-size: 1rem;
        cursor: pointer;
    }
    
    .btn-icon:hover {
        transform: translateY(-3px) scale(1.1);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    .btn-details {
        background: linear-gradient(45deg, #007bff, #0056b3);
        color: white;
    }
    
    .btn-details:hover {
        background: linear-gradient(45deg, #0056b3, #004099);
        box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
    }
    
    .btn-users {
        background: linear-gradient(45deg, #6f42c1, #5a32a3);
        color: white;
    }
    
    .btn-users:hover {
        background: linear-gradient(45deg, #5a32a3, #4a2d8c);
        box-shadow: 0 5px 15px rgba(111, 66, 193, 0.4);
    }
    
    .btn-quiz {
        background: linear-gradient(45deg, var(--primary-color), #ff6b6b);
        color: white;
    }
    
    .btn-quiz:hover {
        background: linear-gradient(45deg, #ff6b6b, var(--primary-color));
        box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
    }
    
    .btn-edit {
        background: linear-gradient(45deg, #ffc107, #e0a800);
        color: #212529;
    }
    
    .btn-edit:hover {
        background: linear-gradient(45deg, #e0a800, #d39e00);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
        grid-column: 1 / -1;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: var(--text-muted);
        opacity: 0.5;
    }
    
    @media (max-width: 768px) {
        .articles-grid {
            grid-template-columns: 1fr;
        }
        
        .articles-header {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
</head>

<body>
        <div class="container">
            <section class="content-section" id="articles-section">
                <div class="section-header">
                    <h2>Gestion des Articles</h2>
                    <p class="badge"><?php echo $count_articles; ?> articles</p>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Aperçu du Contenu</th>
                            <th>Date de Publication</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                        <?php foreach($articlesResult as $article) {?>
                        <tr>
                            <td class="id-cell">#<?php echo $article['id_article']; ?></td>
                            <td class="title-cell"><?php echo htmlspecialchars($article['titre']); ?></td>
                            <td class="content-cell"><?php echo htmlspecialchars(substr($article['contenu'], 0, 100)) . '...'; ?></td>
                            <td class="date-cell"><?php echo date('d/m/Y', strtotime($article['date_publication'])); ?></td>
                            <td class="actions-cell">
                                <a href="detailArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i> Détails 
                                </a>
                                <!-- AJOUTEZ CE BOUTON -->
    <a href="histr_user.php?id=<?php echo $article['id_article']; ?>" class="btn-action" style="background: #6f42c1; color: white;">
        <i class="fas fa-users"></i> Utilisateurs
    </a>
                                <a href="quizArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-quiz">
                                    <i class="fas fa-question-circle"></i> Quiz 
                                </a>
                                <a href="updateArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </td>
                        </tr>
                        <?php }?>
                    </table>
                
            </section>
    <script src="../assets/js/listeArticle.js"></script>
</body>
</html>