<?php
include_once __DIR__ . '/../../../Controller/QuizController.php';
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
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../assets/css/all.css" />
    <link rel="stylesheet" href="../assets/css/listequiz.css">
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
    
    .quiz-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .quiz-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }
    
    .quiz-stat-card {
        background: var(--accent-color);
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid var(--border-color);
        transition: var(--transition);
    }
    
    .quiz-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(255, 74, 87, 0.2);
    }
    
    .quiz-stat-number {
        font-size: 2rem;
        font-weight: bold;
        color: var(--primary-color);
        display: block;
    }
    
    .quiz-stat-label {
        color: var(--text-muted);
        font-size: 0.9rem;
    }
    
    .add-quiz-btn {
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
    
    .add-quiz-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        color: white;
    }
    
    .quiz-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .quiz-card {
        background: var(--accent-color);
        padding: 20px;
        border-radius: 15px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: var(--transition);
        display: flex;
        flex-direction: column;
    }
    
    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(255, 74, 87, 0.3);
        border-color: var(--primary-color);
    }
    
    .quiz-header-card {
        margin-bottom: 15px;
    }
    
    .quiz-question {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-color);
        margin: 0 0 10px 0;
        line-height: 1.4;
    }
    
    .quiz-id {
        background: var(--secondary-color);
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--primary-color);
        display: inline-block;
    }
    
    .answers-list {
        flex: 1;
        margin-bottom: 15px;
    }
    
    .answer-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px;
        margin-bottom: 8px;
        background: var(--secondary-color);
        border-radius: 8px;
        font-size: 0.9rem;
    }
    
    .answer-number {
        background: var(--primary-color);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
    }
    
    .answer-text {
        color: var(--text-color);
        flex: 1;
    }
    
    .correct-answer {
        border-left: 3px solid var(--success-color);
    }
    
    .correct-badge {
        background: var(--success-color);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: bold;
    }
    
    .quiz-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        font-size: 0.85rem;
        color: var(--text-muted);
    }
    
    .quiz-actions {
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
    
    .btn-edit {
        background: linear-gradient(45deg, #ffc107, #e0a800);
        color: #212529;
    }
    
    .btn-edit:hover {
        background: linear-gradient(45deg, #e0a800, #d39e00);
        box-shadow: 0 5px 15px rgba(255, 193, 7, 0.4);
    }
    
    .btn-delete {
        background: linear-gradient(45deg, #dc3545, #c82333);
        color: white;
    }
    
    .btn-delete:hover {
        background: linear-gradient(45deg, #c82333, #bd2130);
        box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
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
        .quiz-grid {
            grid-template-columns: 1fr;
        }
        
        .quiz-header {
            flex-direction: column;
            align-items: stretch;
        }
    }
</style>
</head>

<body>
  
    
    
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
    
    <script src="../assets/js/listeQuiz.js"></script>
</body>
</html>