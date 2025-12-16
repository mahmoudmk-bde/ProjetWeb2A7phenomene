<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit;
}

// Vérifier si un ID d'historique est fourni
$historique_id = $_GET['id'] ?? null;
if (!$historique_id) {
    header('Location: historiaue_user.php');
    exit;
}

// Inclure les fichiers nécessaires pour récupérer les données utilisateur
if (isset($_SESSION['user_id'])) {
    include '../../controller/utilisateurcontroller.php';
    $utilisateurController = new UtilisateurController();
    $user_id = $_SESSION['user_id'];
    $current_user = $utilisateurController->showUtilisateur($user_id);
    
    // Récupérer la photo de profil depuis la base de données
    $profile_picture = $current_user['img'] ?? 'default_avatar.jpg';
    $_SESSION['profile_picture'] = $profile_picture;
}

$user_name = $_SESSION['user_name'] ?? 'Utilisateur';

// Inclure le contrôleur
include_once __DIR__ . '/../../controller/QuizController.php';
$quizController = new QuizController();

// Récupérer les détails de l'historique
$historique = $quizController->getHistoriqueById($historique_id, $user_id);
if (!$historique) {
    header('Location: historiaue_user.php');
    exit;
}

// Récupérer les questions du quiz pour cet article
$questions = $quizController->getQuizzesByArticleId($historique['article_id']);

// Calculer le nombre de bonnes réponses (10 points par question)
$score = $historique['score'] ?? 0;
$totalQuestions = count($questions);
$correctAnswers = floor($score / 10); // 10 points par bonne réponse
$incorrectAnswers = $totalQuestions - $correctAnswers;

// Simulation des réponses utilisateur (basée sur le score)
$simulatedUserAnswers = [];

// Générer des réponses simulées basées sur le score
for ($i = 0; $i < $totalQuestions; $i++) {
    if ($i < $correctAnswers) {
        $simulatedUserAnswers[$i] = [
            'user_answer' => null,
            'is_correct' => true
        ];
    } else {
        $simulatedUserAnswers[$i] = [
            'user_answer' => null,
            'is_correct' => false
        ];
    }
}

// Fonction pour formater la date
function formatDate($date) {
    if (empty($date)) return 'Date non disponible';
    return date('d/m/Y à H:i', strtotime($date));
}

// Fonction pour obtenir l'image selon le titre de l'article
function getThemeImage($titre) {
    $titre = strtolower(trim($titre ?? ''));
    
    $images = [
        "sport" => "sport.png",
        "football" => "tournoi.png",
        "fifa" => "tournoi.png",
        "éducation" => "education.png",
        "education" => "education.png",
        "esport" => "valorant.png",
        "valorant" => "valorant.png",
        "minecraft" => "minecraft.png",
        "création" => "roblox.png",
        "creation" => "roblox.png",
        "prévention" => "sante.png",
        "prevention" => "sante.png",
        "coaching" => "coaching.png",
        "compétition" => "cyber.png",
        "competition" => "cyber.png",
    ];

    foreach ($images as $theme => $image) {
        if (strpos($titre, $theme) !== false) {
            return $image;
        }
    }

    return "default.png";
}

$imageUrl = "image/" . getThemeImage($historique['article_titre'] ?? '');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Détails du Quiz - Engage</title>
    <link rel="icon" href="assets/img/favicon.png" />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/all.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="general.css">
    
    <style>
        :root {
            --primary-color: #ff4a57;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --border-color: #2d3047;
            --dark-color: #15172b;
        }

        body {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--dark-color) 100%);
            color: var(--text-color);
            min-height: 100vh;
            padding-top: 80px;
        }

        .details-section {
            padding: 40px 0;
            min-height: calc(100vh - 200px);
        }

        .details-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .details-header-card {
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--secondary-color) 100%);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .article-image-large {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 15px;
            border: 4px solid var(--primary-color);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.3);
            margin-right: 25px;
            float: left;
        }

        .details-header-content h1 {
            color: var(--text-color);
            font-size: 2rem;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .article-meta {
            color: #b0b3c1;
            font-size: 0.95rem;
            margin-bottom: 15px;
        }

        .score-display {
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-block;
            margin-right: 15px;
        }

        .score-breakdown {
            background: var(--dark-color);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .stat-item {
            background: var(--accent-color);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #b0b3c1;
            font-size: 0.9rem;
        }

        .questions-section {
            background: var(--dark-color);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .question-card {
            background: var(--accent-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }

        .question-text {
            color: var(--text-color);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .answer-option {
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border-left: 4px solid transparent;
        }

        .answer-option.user-correct {
            background: rgba(40, 167, 69, 0.25);
            border-left-color: var(--success-color);
            border: 2px solid var(--success-color);
        }

        .answer-option.user-incorrect {
            background: rgba(220, 53, 69, 0.25);
            border-left-color: var(--danger-color);
            border: 2px solid var(--danger-color);
        }

        .answer-option.correct-unanswered {
            background: rgba(40, 167, 69, 0.1);
            border-left-color: var(--success-color);
            border-left-style: dashed;
        }

        .answer-status {
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .status-correct {
            color: var(--success-color);
            background: rgba(40, 167, 69, 0.2);
        }

        .status-incorrect {
            color: var(--danger-color);
            background: rgba(220, 53, 69, 0.2);
        }

        .question-points {
            background: var(--primary-color);
            color: white;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-left: 10px;
            float: right;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn-details {
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-details:hover {
            background: linear-gradient(45deg, #ff6b7a, var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-outline-details {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-details:hover {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .details-header-card {
                text-align: center;
            }
            
            .article-image-large {
                float: none;
                margin: 0 auto 20px;
                display: block;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="body_bg">
        <header class="main_menu single_page_menu">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a class="navbar-brand" href="index1.php">
                        <img src="img/logo.png" alt="logo" />
                    </a>
                    <button
                        class="navbar-toggler"
                        type="button"
                        data-toggle="collapse"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="Toggle navigation"
                    >
                        <span class="menu_icon"><i class="fas fa-bars"></i></span>
                    </button>

                    <div
                        class="collapse navbar-collapse main-menu-item"
                        id="navbarSupportedContent"
                    >
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
                    <?php
                    if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
                        ?>
                        <div class="user-menu d-none d-sm-block">
                            <div class="user-wrapper" onclick="toggleUserMenu()">
                                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                <div class="user-avatar">
                                    <?php if (isset($profile_picture) && !empty($profile_picture) && $profile_picture !== 'default_avatar.jpg'): ?>
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
                        <?php
                    } else {
                        ?>
                        <a href="connexion.php" class="btn_1 d-none d-sm-block">se connecter</a>
                        <?php
                    }
                    ?>
                </nav>
            </div>
        </div>
    </div>
</header>

        <section class="details-section">
            <div class="details-container">
                <div class="details-header-card">
                    <img src="<?= htmlspecialchars($imageUrl) ?>" 
                         alt="<?= htmlspecialchars($historique['article_titre']) ?>" 
                         class="article-image-large" 
                         onerror="this.src='image/default.png'">
                    <div class="details-header-content">
                        <h1><?= htmlspecialchars($historique['article_titre'] ?? 'Quiz') ?></h1>
                        <div class="article-meta">
                            <span class="score-display">
                                <i class="fas fa-trophy me-1"></i>
                                Score: <?= $score ?> pts (<?= $correctAnswers ?>/<?= $totalQuestions ?> réponses)
                            </span>
                            <span class="attempt-date">
                                <i class="fas fa-calendar me-1"></i>
                                Tentative du <?= formatDate($historique['date_tentative'] ?? '') ?>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="score-breakdown">
                    <h3><i class="fas fa-chart-pie me-2"></i>Résumé du Score</h3>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?= $score ?> pts</div>
                            <div class="stat-label">Score Total</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $correctAnswers ?></div>
                            <div class="stat-label">Bonnes Réponses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $incorrectAnswers ?></div>
                            <div class="stat-label">Mauvaises Réponses</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $totalQuestions ?></div>
                            <div class="stat-label">Questions Totales</div>
                        </div>
                    </div>
                </div>

                <div class="questions-section">
                    <h3><i class="fas fa-question-circle me-2"></i>Récapitulatif des Questions</h3>
                    
                    <?php if (!empty($questions)): ?>
                        <?php foreach($questions as $index => $question): ?>
                            <?php
                            $isQuestionCorrect = $index < $correctAnswers;
                            $correctAnswer = $question['correct'] ?? 1;
                            ?>
                            
                            <div class="question-card">
                                <div class="question-text">
                                    Question <?= ($index + 1) ?>: 
                                    <?= htmlspecialchars($question['question_text'] ?? $question['question'] ?? 'Question non disponible') ?>
                                    <span class="question-points">
                                        <?= $isQuestionCorrect ? '10/10 pts' : '0/10 pts' ?>
                                    </span>
                                </div>
                                
                                <!-- Options de réponse -->
                                <div class="answer-option <?= (1 == $correctAnswer) ? 'correct-answer' : '' ?>">
                                    <?= htmlspecialchars($question['reponse1'] ?? 'Option 1') ?>
                                </div>
                                
                                <div class="answer-option <?= (2 == $correctAnswer) ? 'correct-answer' : '' ?>">
                                    <?= htmlspecialchars($question['reponse2'] ?? 'Option 2') ?>
                                </div>
                                
                                <?php if(!empty($question['reponse3'])): ?>
                                <div class="answer-option <?= (3 == $correctAnswer) ? 'correct-answer' : '' ?>">
                                    <?= htmlspecialchars($question['reponse3']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #b0b3c1; text-align: center; padding: 20px;">
                            Aucune question disponible pour ce quiz.
                        </p>
                    <?php endif; ?>
                </div>

                <div class="action-buttons">
                    <a href="quiz.php?article_id=<?= $historique['article_id'] ?>" class="btn-details">
                        <i class="fas fa-redo me-1"></i>Retenter ce quiz
                    </a>
                    <a href="historiaue_user.php" class="btn-details btn-outline-details">
                        <i class="fas fa-arrow-left me-1"></i>Retour à l'historique
                    </a>
                    <a href="index1.php" class="btn-details btn-outline-details">
                        <i class="fas fa-home me-1"></i>Accueil
                    </a>
                </div>
            </div>
        </section>

        <!-- Footer identique -->
        <!-- ... -->
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
                                <input type="email" name="email" placeholder="Adresse Email" 
                                    class="placeholder hide-on-focus">
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

    <script>
        // Animation pour les cartes de questions
        document.addEventListener('DOMContentLoaded', function() {
            const questionCards = document.querySelectorAll('.question-card');
            questionCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
    <!-- Scripts JavaScript (ajoutez avant </body>) -->
<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

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
    
    // Optionnel: Fermer le menu en appuyant sur Echap
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        }
    });

    // Animation pour les cartes de questions
    document.addEventListener('DOMContentLoaded', function() {
        const questionCards = document.querySelectorAll('.question-card');
        questionCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
</body>
</html>