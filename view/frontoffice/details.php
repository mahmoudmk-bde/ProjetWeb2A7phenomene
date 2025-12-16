<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Vérifier si un ID d'historique est spécifié
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: historiaue_user.php');
    exit;
}

$history_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Inclure les contrôleurs nécessaires
include_once __DIR__ . '/../../controller/QuizController.php';
include_once __DIR__ . '/../../controller/UtilisateurController.php';

$quizController = new QuizController();
$utilisateurController = new UtilisateurController();

// Récupérer les détails du quiz
$quizDetails = $quizController->getHistoriqueDetails($history_id, $user_id);

// Vérifier si le quiz existe et appartient à l'utilisateur
if (!$quizDetails) {
    header('Location: historiaue_user.php?error=quiz_not_found');
    exit;
}

// Récupérer les informations utilisateur
$current_user = $utilisateurController->showUtilisateur($user_id);
$user_name = $_SESSION['user_name'] ?? 'Utilisateur';

// Récupérer la photo de profil
$profile_picture = $current_user['img'] ?? 'default_avatar.jpg';

// Fonction pour formater la date
function formatDate($date) {
    if (empty($date)) return 'Date non disponible';
    return date('d/m/Y à H:i', strtotime($date));
}

// Calculer le score en pourcentage
$score = $quizDetails['score'] ?? 0;
$score_percentage = $score; // Déjà en points (sur 100)
$correct_answers = floor($score / 10); // 10 points par question
$total_questions = 10; // Par défaut
$wrong_answers = $total_questions - $correct_answers;

// Déterminer la classe du score
function getScoreClass($score) {
    if ($score >= 80) return 'score-high';
    if ($score >= 50) return 'score-medium';
    return 'score-low';
}

// Récupérer les réponses détaillées si disponibles
$detailed_answers = $quizController->getQuizAnswers($history_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Détails du Quiz - Engage</title>
    <link rel="icon" href="assets/img/favicon.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="assets/css/all.css" />
    <!-- animate CSS -->
    <link rel="stylesheet" href="assets/css/animate.css" />
    <!-- style CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="general.css">
    
    <style>
        :root {
            --primary-color: #ff4a57;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --border-color: #2d3047;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }

        .details-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #15172b 100%);
            min-height: 100vh;
        }

        .details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .details-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .details-header h1 {
            color: var(--text-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .quiz-meta {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .meta-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 12px 25px;
            border-radius: 25px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .meta-item i {
            color: var(--primary-color);
        }

        .meta-label {
            color: #b0b3c1;
            font-size: 0.9rem;
        }

        .meta-value {
            color: var(--text-color);
            font-weight: 600;
        }

        /* Score Display */
        .score-display {
            text-align: center;
            margin: 40px 0;
        }

        .score-circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto;
            position: relative;
            background: conic-gradient(var(--primary-color) <?php echo $score_percentage; ?>%, var(--accent-color) 0%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(255, 74, 87, 0.3);
        }

        .score-circle-inner {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: var(--secondary-color);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .score-value {
            font-size: 3rem;
            font-weight: bold;
            color: var(--text-color);
            line-height: 1;
        }

        .score-label {
            color: #b0b3c1;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .score-badge {
            margin-top: 20px;
            padding: 8px 25px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            display: inline-block;
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            color: white;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: var(--primary-color);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-correct .stat-number {
            color: var(--success-color);
        }

        .stat-wrong .stat-number {
            color: var(--danger-color);
        }

        .stat-percentage .stat-number {
            color: var(--primary-color);
        }

        /* Questions Section */
        .questions-section {
            margin: 50px 0;
        }

        .section-title {
            color: var(--text-color);
            font-size: 1.8rem;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-color);
        }

        .questions-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .question-item {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: 10px;
            padding: 25px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .question-item.correct {
            border-left: 5px solid var(--success-color);
        }

        .question-item.incorrect {
            border-left: 5px solid var(--danger-color);
        }

        .question-text {
            color: var(--text-color);
            font-size: 1.1rem;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .answers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }

        .answer-option {
            padding: 15px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
        }

        .answer-option.correct-answer {
            background: rgba(40, 167, 69, 0.2);
            border-color: var(--success-color);
        }

        .answer-option.user-answer {
            background: rgba(220, 53, 69, 0.2);
            border-color: var(--danger-color);
        }

        .answer-option.user-correct {
            background: rgba(40, 167, 69, 0.2);
            border-color: var(--success-color);
        }

        .answer-text {
            color: #b0b3c1;
        }

        .answer-status {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 0.8rem;
            padding: 3px 10px;
            border-radius: 15px;
            font-weight: 600;
        }

        .status-correct {
            background: var(--success-color);
            color: white;
        }

        .status-incorrect {
            background: var(--danger-color);
            color: white;
        }

        .question-explanation {
            margin-top: 15px;
            padding: 15px;
            background: rgba(255, 74, 87, 0.1);
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }

        .explanation-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .explanation-text {
            color: #b0b3c1;
            font-size: 0.95rem;
        }

        /* Actions */
        .actions-section {
            text-align: center;
            margin: 50px 0 30px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 74, 87, 0.3);
            color: white;
            text-decoration: none;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #b0b3c1;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--accent-color);
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: var(--text-color);
            margin-bottom: 15px;
            font-size: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .details-header h1 {
                font-size: 2rem;
            }
            
            .score-circle {
                width: 150px;
                height: 150px;
            }
            
            .score-circle-inner {
                width: 120px;
                height: 120px;
            }
            
            .score-value {
                font-size: 2.5rem;
            }
            
            .quiz-meta {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .answers-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 576px) {
            .details-header h1 {
                font-size: 1.6rem;
            }
            
            .score-value {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .question-item {
            animation: fadeInUp 0.5s ease-out forwards;
            opacity: 0;
        }
    </style>
</head>

<body>
    <div class="body_bg">
        <!-- Header -->
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="index1.php">
                                <img src="img/logo.png" alt="logo" />
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>

                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index1.php">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="fighter.html">mission</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="team.html">gamification</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="team.html">reclamation</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="team.html">evenement</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="team.html">education</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="contact.html">Contact</a>
                                    </li>
                                </ul>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="user-menu d-none d-sm-block">
                                    <div class="user-wrapper" onclick="toggleUserMenu()">
                                        <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                                        <div class="user-avatar">
                                            <?php if ($profile_picture && $profile_picture !== 'default_avatar.jpg'): ?>
                                                <img src="assets/uploads/profiles/<?php echo htmlspecialchars($profile_picture); ?>" 
                                                     alt="Photo de profil" 
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <i class="fas fa-user" style="display: none;"></i>
                                            <?php else: ?>
                                                <i class="fas fa-user"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="user-dropdown" id="userDropdown">
                                        <a href="profile.php">
                                            <i class="fas fa-user me-2"></i>Mon Profil
                                        </a>
                                        <a href="settings.php">
                                            <i class="fas fa-cog me-2"></i>Paramètres
                                        </a>
                                        <a href="historiaue_user.php">
                                            <i class="fas fa-history"></i>Mon Historique
                                        </a>
                                        <a href="logout.php">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="connexion.php" class="btn_1 d-none d-sm-block">se connecter</a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <!-- Détails du Quiz -->
        <section class="details-section">
            <div class="details-container">
                <!-- Header -->
                <div class="details-header">
                    <h1><i class="fas fa-chart-bar me-2"></i>Détails du Quiz</h1>
                    <div class="quiz-meta">
                        <div class="meta-item">
                            <i class="fas fa-book"></i>
                            <div>
                                <div class="meta-label">Article</div>
                                <div class="meta-value"><?php echo htmlspecialchars($quizDetails['article_titre'] ?? 'Article inconnu'); ?></div>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <div>
                                <div class="meta-label">Date</div>
                                <div class="meta-value"><?php echo formatDate($quizDetails['date_tentative'] ?? ''); ?></div>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <div>
                                <div class="meta-label">Durée</div>
                                <div class="meta-value"><?php echo $quizDetails['duree'] ?? 'N/A'; ?> min</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Score Display -->
                <div class="score-display">
                    <div class="score-circle">
                        <div class="score-circle-inner">
                            <div class="score-value"><?php echo $score; ?>pts</div>
                            <div class="score-label">Score total</div>
                        </div>
                    </div>
                    <div class="score-badge <?php echo getScoreClass($score); ?>">
                        <?php echo $correct_answers; ?>/<?php echo $total_questions; ?> bonnes réponses
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card stat-correct">
                        <div class="stat-number"><?php echo $correct_answers; ?></div>
                        <div class="stat-label">Réponses correctes</div>
                    </div>
                    <div class="stat-card stat-wrong">
                        <div class="stat-number"><?php echo $wrong_answers; ?></div>
                        <div class="stat-label">Réponses incorrectes</div>
                    </div>
                    <div class="stat-card stat-percentage">
                        <div class="stat-number"><?php echo round(($correct_answers / $total_questions) * 100, 1); ?>%</div>
                        <div class="stat-label">Taux de réussite</div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="questions-section">
                    <h2 class="section-title">
                        <i class="fas fa-question-circle me-2"></i>
                        Détail des questions
                    </h2>
                    
                    <?php if (!empty($detailed_answers)): ?>
                        <div class="questions-list">
                            <?php $question_num = 1; ?>
                            <?php foreach($detailed_answers as $answer): 
                                $is_correct = $answer['is_correct'] ?? false;
                                $user_answer = $answer['user_answer'] ?? '';
                                $correct_answer = $answer['correct_answer'] ?? '';
                            ?>
                                <div class="question-item <?php echo $is_correct ? 'correct' : 'incorrect'; ?>" style="animation-delay: <?php echo ($question_num * 0.1); ?>s;">
                                    <div class="question-text">
                                        <strong>Question <?php echo $question_num; ?>:</strong> 
                                        <?php echo htmlspecialchars($answer['question_text'] ?? 'Question non disponible'); ?>
                                    </div>
                                    
                                    <div class="answers-grid">
                                        <div class="answer-option <?php echo ($user_answer === $correct_answer && $user_answer !== '') ? 'user-correct' : ''; ?> <?php echo ($user_answer !== '' && $user_answer !== $correct_answer && $user_answer === $answer['option_text']) ? 'user-answer' : ''; ?> <?php echo ($correct_answer === $answer['option_text']) ? 'correct-answer' : ''; ?>">
                                            <div class="answer-text">
                                                <?php echo htmlspecialchars($answer['option_text'] ?? 'Option non disponible'); ?>
                                            </div>
                                            <?php if ($correct_answer === $answer['option_text']): ?>
                                                <span class="answer-status status-correct">Correcte</span>
                                            <?php elseif ($user_answer !== '' && $user_answer === $answer['option_text'] && $user_answer !== $correct_answer): ?>
                                                <span class="answer-status status-incorrect">Votre réponse</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($answer['explanation'] ?? ''): ?>
                                        <div class="question-explanation">
                                            <div class="explanation-title">
                                                <i class="fas fa-lightbulb"></i>
                                                Explication
                                            </div>
                                            <div class="explanation-text">
                                                <?php echo htmlspecialchars($answer['explanation']); ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php $question_num++; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-question-circle"></i>
                            <h3>Détails non disponibles</h3>
                            <p>Les réponses détaillées ne sont pas disponibles pour ce quiz.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Actions -->
                <div class="actions-section">
                    <div class="action-buttons">
                        <a href="historiaue_user.php" class="btn-action btn-outline">
                            <i class="fas fa-arrow-left me-2"></i>Retour à l'historique
                        </a>
                        <a href="quiz.php?article_id=<?php echo $quizDetails['article_id'] ?? ''; ?>" class="btn-action btn-primary">
                            <i class="fas fa-redo me-2"></i>Refaire ce quiz
                        </a>
                        <a href="article_detail.php?id=<?php echo $quizDetails['article_id'] ?? ''; ?>" class="btn-action btn-outline">
                            <i class="fas fa-book me-2"></i>Voir l'article
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="footer_part">
            <div class="footer_top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <a href="index1.php" class="footer_logo_iner">
                                    <img src="img/logo.png" alt="logo">
                                </a>
                                <p>Engage - La plateforme de matchmaking pour le volontariat par le jeu vidéo</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Contact Info</h4>
                                <p>Adresse : Tunis, Tunisie</p>
                                <p>Téléphone : +216 XX XXX XXX</p>
                                <p>Email : contact@engage.tn</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Liens Importants</h4>
                                <ul class="list-unstyled">
                                    <li><a href="store.html">Store</a></li>
                                    <li><a href="partenaires.html">Partenaires</a></li>
                                    <li><a href="missions.html">Missions</a></li>
                                    <li><a href="evenements.html">Événements</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Newsletter</h4>
                                <p>Inscrivez-vous pour recevoir nos nouveautés</p>
                                <div id="mc_embed_signup">
                                    <form action="#" method="get" class="subscribe_form relative mail_part">
                                        <input type="email" name="email" placeholder="Adresse Email" class="placeholder hide-on-focus">
                                        <button type="submit" class="email_icon newsletter-submit">
                                            <i class="far fa-paper-plane"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copygight_text">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-8">
                            <div class="copyright_text">
                                <p>© <script>document.write(new Date().getFullYear());</script> Engage. Tous droits réservés</p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="footer_icon social_icon">
                                <ul class="list-unstyled">
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts JavaScript -->
    <script src="assets/js/jquery-1.12.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/custom.js"></script>
    
    <script>
        // Fonction pour afficher/masquer le menu utilisateur
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('show');
        }
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', function(event) {
            const userMenu = document.querySelector('.user-menu');
            const dropdown = document.getElementById('userDropdown');
            
            if (userMenu && dropdown) {
                if (!userMenu.contains(event.target)) {
                    dropdown.classList.remove('show');
                }
            }
        });
        
        // Animation des questions
        document.addEventListener('DOMContentLoaded', function() {
            const questionItems = document.querySelectorAll('.question-item');
            questionItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.opacity = '1';
                }, index * 100);
            });
        });
    </script>
</body>
</html>