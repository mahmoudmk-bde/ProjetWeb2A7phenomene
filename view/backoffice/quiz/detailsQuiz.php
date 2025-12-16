<?php
include_once __DIR__ . '/../../../Controller/QuizController.php';
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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/all.css" />
    <link rel="stylesheet" href="../assets/css/detailsQuiz.css">
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

    
</body>
</html>