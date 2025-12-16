<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();
$articlesResult = $quizController->listArticle();
$count_articles = $quizController->getArticlesCount();?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Backoffice - Engage</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/listearticlee.css">
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
                        <a href="listeArticle.php" class="active">
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
                </div>
            </section>
    <script src="js/listeArticle.js"></script>
</body>
</html>