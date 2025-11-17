<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();
$articlesResult = $quizController->listArticle();
$quizsResult = $quizController->listQuiz();
$count_articles = $quizController->getArticlesCount();
$count_quiz = $quizController->getQuizCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme Quiz</title>
    <link rel="stylesheet" href="s1.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Tableau de Bord Plateforme Quiz</h1>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="location.href='addquiz.php'">Ajouter Quiz</button>
            </div>
        </header>
        <section class="content-section">
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
                            <td class="title-cell"><?php echo $article['titre']; ?></td>
                            <td class="content-cell"><?php echo $article['contenu']; ?></td>
                            <td class="date-cell"><?php echo $article['date_publication']; ?></td>
                            <td class="actions-cell">
                                <a href="updateArticle.php?id=<?php echo $article['id_article']; ?>" class="btn-action btn-edit">Modifier</a>
                            </td>
                        </tr>
                        <?php }?>
                </table>
            </div>
        </section>
        <section class="content-section">
            <div class="section-header">
                <h2>Gestion des Quiz</h2>
                <span class="badge"><?php echo $count_quiz; ?> quiz</span>
            </div>
            <div class="table-container">
                <table class="data-table">
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Réponses1</th>
                            <th>Réponses2</th>
                            <th>Réponses3</th>
                            <th>Bonne Réponse</th>
                            <th>ID Article</th>
                            <th class="actions-header">Actions</th>
                        </tr>
                        <?php foreach($quizsResult as $quiz) {?>
                        <tr>
                            <td class="id-cell">#<?php echo $quiz['id_quiz']; ?></td>
                            <td class="question-cell"><?php echo $quiz['question']; ?></td>
                            <td class="answers-cell"><?php echo $quiz['reponse1']; ?></td>
                            <td class="answers-cell"><?php echo $quiz['reponse2']; ?></td>
                            <td class="answers-cell"><?php echo $quiz['reponse3']; ?></td>
                            <td class="correct-answer-cell">
                                <span class="correct-badge"><?php echo $quiz['bonne_reponse']; ?></span>
                            </td>
                            <td class="article-id-cell">#<?php echo $quiz['id_article']; ?></td>
                            <td class="actions-cell">
                                <a href="updateQuiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-edit">Modifier</a>
                                <a href="deletequiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce quiz ?')">Supprimer</a>
                            </td>
                        <?php } ?>
                </table>
            </div>         
        </section>
        <section class="stats-section">
            <h2>Aperçu de la Plateforme</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $count_articles; ?></h3>
                        <p>Articles Totaux</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $count_quiz; ?></h3>
                        <p>Quiz Totaux</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo ($count_articles + $count_quiz); ?></h3>
                        <p>Contenu Total</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="j1.js"></script>
</body>
</html>
                