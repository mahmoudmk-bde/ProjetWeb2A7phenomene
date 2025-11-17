<?php
include_once __DIR__ . '/../../Controller/QuizController.php';
include_once __DIR__ . '/../../Model/Quiz.php';

$error = '';
$quiz = null;
$quizController = new QuizController();

if(isset($_GET['id'])) {
    $quiz = $quizController->getQuizById($_GET['id']);
}

if (
    isset($_POST['id_quiz'], $_POST['question'], $_POST['reponse1'], $_POST['reponse2'], $_POST['reponse3'], $_POST['bonne_reponse'], $_POST['id_article'])
) {
    if (
        !empty($_POST['id_quiz']) && !empty($_POST['question']) && !empty($_POST['reponse1']) && !empty($_POST['reponse2']) && 
        !empty($_POST['reponse3']) && !empty($_POST['bonne_reponse']) && !empty($_POST['id_article'])
    ) {
        try {
            $quiz = new Quiz(
                $_POST['id_quiz'],
                $_POST['question'],
                $_POST['reponse1'],
                $_POST['reponse2'],
                $_POST['reponse3'],
                $_POST['bonne_reponse'],
                $_POST['id_article']
            );
            $quizController->updateQuiz($quiz, $_POST['id_quiz']);
            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            $error = 'Erreur lors de la modification: ' . $e->getMessage();
        }
    } else {
        $error = 'Veuillez remplir tous les champs';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Quiz</title>
    <link rel="icon" href="img/favicon.png" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/all.css" />
    <link rel="stylesheet" href="UpdateQuiz.css">
</head>
<body>
    
        <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <img src="img/logo.png" alt="logo" style="height: 40px; margin-right: 10px;" /> 
            <h3> Backoffice</h3>
        </div>

       <ul class="list-unstyled components">
    <li class="active">
        <a href="#gestion-submenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
            <i class="fas fa-tachometer-alt"></i> Gestion d'article et quiz
        </a>
        <ul class="collapse list-unstyled" id="gestion-submenu">
            <li>
                <a href="index.php"><i class="fas fa-home"></i> Tableau de Bord</a>
            </li>
            <li>
                <a href="addquiz.php"><i class="fas fa-plus-circle"></i> Ajouter Quiz</a>
            </li>
            <li>
                <a href="index.php"><i class="fas fa-list"></i> Liste Articles</a>
            </li>
            <li>
                <a href="index.php"><i class="fas fa-list"></i> Liste Quiz</a>
            </li>
            <li>
                <a href="index.php"><i class="fas fa-list"></i> Statistiques</a>
            </li>
        </ul>
    </li>
</ul>
    </nav>
    <div class="form-container">
        <h1>Modifier le Quiz</h1>
        <form action="" method="POST">
            <input type="hidden" id="id_quiz" name="id_quiz" value="<?php echo $quiz['id_quiz'] ?? ''; ?>">
            
            <label for="question">Question</label>
            <input type="text" id="question" name="question" value="<?php echo $quiz['question'] ?? ''; ?>" >
  
            <label for="reponse1">Réponse 1</label>
            <input type="text" id="reponse1" name="reponse1" value="<?php echo $quiz['reponse1'] ?? ''; ?>" >
            
            <label for="reponse2">Réponse 2</label>
            <input type="text" id="reponse2" name="reponse2" value="<?php echo $quiz['reponse2'] ?? ''; ?>" >
            
            <label for="reponse3">Réponse 3</label>
            <input type="text" id="reponse3" name="reponse3" value="<?php echo $quiz['reponse3'] ?? ''; ?>" >
            
            <label for="bonne_reponse">Bonne Réponse</label>
            <select id="bonne_reponse" name="bonne_reponse" >
                <option value="1" <?php echo ($quiz['bonne_reponse'] == 1) ? 'selected' : ''; ?>>Réponse 1</option>
                <option value="2" <?php echo ($quiz['bonne_reponse'] == 2) ? 'selected' : ''; ?>>Réponse 2</option>
                <option value="3" <?php echo ($quiz['bonne_reponse'] == 3) ? 'selected' : ''; ?>>Réponse 3</option>
            </select>
            
            <label for="id_article">ID Article</label>
            <input type="number" id="id_article" name="id_article" value="<?php echo htmlspecialchars($quiz['id_article'] ?? ''); ?>" >

            <button type="submit" class="btn">Modifier le Quiz</button>
            <a href="index.php" class="btn btn-secondary">Retour au tableau de bord</a>
        </form>
    </div>
    <script src="j1.js"></script>
</body>
</html>