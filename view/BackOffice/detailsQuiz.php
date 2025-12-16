<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();

if (isset($_GET['id'])) {
    $quizId = $_GET['id'];
    $quiz = $quizController->getQuizById($quizId);
    
    if (!$quiz) {
        header("Location: listeQuiz.php");
        exit();
    }
} else {
    header("Location: listeQuiz.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Détails Quiz - Backoffice Engage</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css_temp/bootstrap.min.css" />
    <link rel="stylesheet" href="css_temp/all.css" />
    <link rel="stylesheet" href="css/detailsQuiz.css">
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
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="listeQuiz.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <span class="quiz-id-badge">Quiz #<?php echo $quiz['id_quiz']; ?></span>
            </div>

            <div class="quiz-details-container">
                <!-- Section Question -->
                <div class="detail-card">
                    <div class="detail-label">Question</div>
                    <div class="detail-value"><?php echo htmlspecialchars($quiz['question']); ?></div>
                </div>

                <!-- Section Réponses -->
                <div class="detail-card">
                    <div class="detail-label">Options de Réponse</div>
                    
                    <?php
                    $reponses = [
                        '1' => $quiz['reponse1'],
                        '2' => $quiz['reponse2'], 
                        '3' => $quiz['reponse3']
                    ];
                    $bonneReponse = $quiz['bonne_reponse'];
                    
                    foreach ($reponses as $numero => $reponse) {
                        $isCorrect = ($numero == $bonneReponse);
                        $class = $isCorrect ? 'correct' : 'incorrect';
                        $badge = $isCorrect ? '<span style="color: var(--success-color); margin-left: 10px;"><i class="fas fa-check-circle"></i> Bonne réponse</span>' : '';
                        
                        echo "
                        <div class='answer-option $class'>
                            <strong>Réponse $numero:</strong> " . htmlspecialchars($reponse) . $badge . "
                        </div>";
                    }
                    ?>
                </div>

                <!-- Section Informations -->
                <div class="detail-card">
                    <div class="detail-label">Informations du Quiz</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="detail-label">ID Quiz</div>
                            <div class="detail-value">#<?php echo $quiz['id_quiz']; ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">ID Article Associé</div>
                            <div class="detail-value">#<?php echo $quiz['id_article']; ?></div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="detail-label">Bonne Réponse</div>
                            <div class="detail-value">Réponse <?php echo $bonneReponse; ?></div>
                        </div>
                    </div>
                </div>

                <!-- Section Actions -->
                <div class="detail-card text-center">
                    <div class="detail-label">Actions</div>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="updateQuiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-edit">
                            <i class="fas fa-edit"></i> Modifier le Quiz
                        </a>
                        <a href="deletequiz.php?id=<?php echo $quiz['id_quiz']; ?>" class="btn-action btn-delete">
                            <i class="fas fa-trash"></i> Supprimer le Quiz
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>