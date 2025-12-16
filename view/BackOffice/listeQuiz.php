<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();
$quizsResult = $quizController->listQuiz();
$count_quiz = $quizController->getQuizCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Backoffice - Engage</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/listequiz.css">
    
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
                        <a href="listeQuiz.php" class="active">
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
            <section class="content-section" id="quiz-section">
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
                                <a href="detailsQuiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-details">
                                    <i class="fas fa-eye"></i> Détails  
                                </a>
                                <a href="updateQuiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-edit">
                                    <i class="fas fa-edit"></i> Modifier 
                                </a>
                                <a href="deletequiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i> Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>         
            </section>
        </div>
    </div>
    <script src="js/listeQuiz.js"></script>
</body>
</html>