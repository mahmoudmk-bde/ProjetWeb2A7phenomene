<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
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

// Récupérer l'historique de l'utilisateur avec la méthode existante
$historique = $quizController->getHistoriqueByUser($user_id);

// Fonction pour déterminer la classe CSS du score (basée sur les points/10)
function getScoreClass($score) {
    if ($score >= 80) return 'score-high';      // 8/10 ou plus
    if ($score >= 50) return 'score-medium';    // 5/10 à 7/10
    return 'score-low';                         // Moins de 5/10
}

// Fonction pour formater la date
function formatDate($date) {
    if (empty($date)) return 'Date non disponible';
    return date('d/m/Y H:i', strtotime($date));
}

// Fonction pour afficher le score en points (score sur 10 questions * 10 points)
function formatScore($score) {
    // Le score est déjà en points (10 points par question)
    // Par exemple: 70 points = 7 bonnes réponses sur 10
    return $score . ' pts';
}

// Fonction pour afficher le score en format lisible
function displayScore($score) {
    // Calculer le nombre de questions (chaque question = 10 points)
    $totalQuestions = 10; // Par défaut
    $correctAnswers = floor($score / 10);
    
    return $correctAnswers . '/' . $totalQuestions . ' (' . $score . ' pts)';
}

// Calcul des statistiques
$totalScore = 0;
$bestScore = 0;
$totalQuizzes = count($historique);

foreach ($historique as $item) {
    $score = $item['score'] ?? 0;
    $totalScore += $score;
    if ($score > $bestScore) $bestScore = $score;
}

// Calcul du score moyen en points
$avgScore = $totalQuizzes > 0 ? round($totalScore / $totalQuizzes, 0) : 0;

// Formater les scores pour l'affichage
$totalScoreDisplay = formatScore($totalScore);
$bestScoreDisplay = formatScore($bestScore);
$lastQuizDate = !empty($historique) ? date('d/m', strtotime($historique[0]['date_tentative'])) : '--';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />
    <title>Mon Historique de Quiz - Engage</title>
    <link rel="icon" href="assets/img/favicon.png" />
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <!-- animate CSS -->
    <link rel="stylesheet" href="assets/css/animate.css" />
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="assets/css/all.css" />
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="assets/css/flaticon.css" />
    <link rel="stylesheet" href="assets/css/themify-icons.css" />
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="assets/css/magnific-popup.css" />
    <!-- swiper CSS -->
    <link rel="stylesheet" href="assets/css/slick.css" />
    <!-- style CSS -->
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="assets/css/general.css">
    
    <style>
        /* Styles spécifiques pour la page historique */
        :root {
            --primary-color: #ff4a57;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --border-color: #2d3047;
        }

        .history-section {
            padding: 100px 0;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #15172b 100%);
            flex: 1;
            display: flex;
            align-items: center;
            min-height: calc(100vh - 200px);
        }

        .history-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
            width: 100%;
        }

        .history-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .history-header h1 {
            color: var(--text-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .history-header p {
            color: #b0b3c1;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .user-id-badge {
            color: #ff6b7a;
            font-weight: 600;
            background: rgba(255, 107, 122, 0.1);
            padding: 8px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 15px;
            font-size: 0.9rem;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 25px;
            text-align: center;
            border: 1px solid var(--border-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            border-color: var(--primary-color);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 10px;
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            color: #b0b3c1;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* History Table - NOUVELLE MISE EN FORME */
        .history-table-container {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--accent-color) 100%);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            overflow: hidden;
            margin-bottom: 40px;
        }

        .table-header {
            background: linear-gradient(90deg, rgba(255, 74, 87, 0.2), rgba(255, 107, 122, 0.1));
            padding: 25px 30px;
            border-bottom: 1px solid var(--border-color);
        }

        .table-header h3 {
            margin: 0;
            color: var(--text-color);
            font-weight: 600;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-responsive {
            overflow-x: auto;
            padding: 0 10px;
        }

        .history-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 0;
        }

        .history-table th {
            background: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
            color: var(--primary-color);
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .history-table td {
            padding: 20px 15px;
            border-bottom: 1px solid rgba(45, 48, 71, 0.5);
            color: #b0b3c1;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .history-table tbody tr {
            transition: all 0.3s ease;
        }

        .history-table tbody tr:hover {
            background: rgba(255, 74, 87, 0.1);
            transform: translateX(5px);
        }

        .history-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Score Badges - MODIFIÉ POUR POINTS */
        .score-badge {
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            min-width: 120px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .score-high {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }

        .score-medium {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
        }

        .score-low {
            background: linear-gradient(45deg, #dc3545, #ff6b7a);
            color: white;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        /* Score details in badge */
        .score-details {
            font-size: 0.8rem;
            opacity: 0.9;
            margin-top: 2px;
            font-weight: 400;
        }

        /* Buttons */
        .btn-history {
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 74, 87, 0.3);
        }

        .btn-history:hover {
            background: linear-gradient(45deg, #ff6b7a, var(--primary-color));
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 74, 87, 0.4);
            color: white;
            text-decoration: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #b0b3c1;
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--accent-color);
            margin-bottom: 25px;
            opacity: 0.5;
        }

        .empty-state h3 {
            color: var(--text-color);
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .empty-state p {
            margin-bottom: 35px;
            font-size: 1.2rem;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Back Button */
        .back-home {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            border: 1px solid var(--primary-color);
            padding: 15px 35px;
            border-radius: 25px;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }

        .back-home:hover {
            background: var(--primary-color);
            color: white;
            text-decoration: none;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(255, 74, 87, 0.3);
        }

        /* User avatar for history */
        .history-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--primary-color), #ff6b7a);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 5px solid var(--accent-color);
            box-shadow: 0 10px 25px rgba(255, 74, 87, 0.3);
            font-size: 40px;
            font-weight: bold;
            color: white;
        }

        /* Article title link */
        .article-title {
            color: var(--text-color);
            font-weight: 600;
            cursor: pointer;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }

        .article-title:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        /* Table column styling */
        .table-col-article {
            width: 45%;
        }
        
        .table-col-date {
            width: 30%;
        }
        
        .table-col-score {
            width: 25%;
        }

        /* Animation for table rows */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .history-table tbody tr {
            animation: slideIn 0.5s ease-out forwards;
            opacity: 0;
        }

        .history-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .history-table tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .history-table tbody tr:nth-child(3) { animation-delay: 0.3s; }
        .history-table tbody tr:nth-child(4) { animation-delay: 0.4s; }
        .history-table tbody tr:nth-child(5) { animation-delay: 0.5s; }
        .history-table tbody tr:nth-child(6) { animation-delay: 0.6s; }
        .history-table tbody tr:nth-child(7) { animation-delay: 0.7s; }
        .history-table tbody tr:nth-child(8) { animation-delay: 0.8s; }
        .history-table tbody tr:nth-child(9) { animation-delay: 0.9s; }
        .history-table tbody tr:nth-child(10) { animation-delay: 1.0s; }

        /* Stats small text */
        .stat-small {
            font-size: 0.85rem;
            color: #8a8d9a;
            margin-top: 5px;
            display: block;
        }

        /* Pour les écrans mobiles */
        @media (max-width: 768px) {
            .history-header h1 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .history-table th,
            .history-table td {
                padding: 15px 10px;
                font-size: 0.9rem;
            }
            
            .history-avatar {
                width: 80px;
                height: 80px;
                font-size: 30px;
            }
            
            .history-section {
                padding: 80px 0;
            }
            
            .stat-value {
                font-size: 2rem;
            }
            
            .table-col-article,
            .table-col-date,
            .table-col-score {
                width: auto;
            }
        }

        @media (max-width: 576px) {
            .history-header h1 {
                font-size: 1.6rem;
            }
            
            .history-table-container {
                margin: 0 -15px;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .stat-value {
                font-size: 1.8rem;
            }
            
            .history-table th,
            .history-table td {
                padding: 12px 8px;
                font-size: 0.85rem;
            }
            
            .score-badge {
                min-width: 100px;
                padding: 6px 15px;
            }
        }

        /* Animation for stats */
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

        .stat-card {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>

<body>
    <div class="body_bg">
      <!--::header part start::-->
      <?php include 'header_common.php'; ?>

      <!-- Section historique -->
      <section class="history-section">
        <div class="history-container">
            <!-- Header -->
            <div class="history-header">
                <div class="history-avatar">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <h1><i class="fas fa-history me-2"></i>Mon Historique de Quiz</h1>
                <p>Retrouvez tous vos quiz passés et vos performances (10 points par question)</p>
                <div class="user-id-badge">ID: #<?php echo $user_id; ?></div>
            </div>
            
            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $totalQuizzes; ?></div>
                    <div class="stat-label">Quiz Réalisés</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $totalScoreDisplay; ?></div>
                    <div class="stat-label">Score Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $bestScoreDisplay; ?></div>
                    <div class="stat-label">Meilleur Score<br><small class="stat-small"><?php echo displayScore($bestScore); ?></small></div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?php echo $lastQuizDate; ?></div>
                    <div class="stat-label">Dernier Quiz</div>
                </div>
            </div>
            
            <!-- Tableau d'historique -->
            <div class="history-table-container">
                <div class="table-header">
                    <h3 class="mb-0"><i class="fas fa-list me-2"></i>Détail des Tentatives</h3>
                </div>
                
                <?php if (!empty($historique)): ?>
                    <div class="table-responsive">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th class="table-col-article">Article</th>
                                    <th class="table-col-date">Date</th>
                                    <th class="table-col-score">Score</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($historique as $item): 
                                    $itemScore = $item['score'] ?? 0;
                                    $itemScoreDisplay = formatScore($itemScore);
                                    $itemScoreDetails = displayScore($itemScore);
                                ?>
                                    <tr>
                                        <td>
                                            <div class="article-title" onclick="viewArticle(<?php echo $item['article_id'] ?? 0; ?>)">
                                                <i class="fas fa-book me-2"></i>
                                                <?php echo htmlspecialchars($item['article_titre'] ?? 'Article inconnu'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <i class="fas fa-calendar me-2"></i>
                                            <?php echo formatDate($item['date_tentative'] ?? ''); ?>
                                        </td>
                                        <td>
                                            <span class="score-badge <?php echo getScoreClass($itemScore); ?>">
                                                <div><strong><?php echo $itemScoreDisplay; ?></strong></div>
                                                <div class="score-details"><?php echo $itemScoreDetails; ?></div>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <!-- État vide -->
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <h3>Aucun historique de quiz</h3>
                        <p>Vous n'avez pas encore passé de quiz. Commencez dès maintenant !</p>
                        <a href="education.php" class="btn-history">
                            <i class="fas fa-play me-2"></i>Commencer un quiz
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Bouton de retour -->
            <div class="text-center">
                <a href="index1.php" class="back-home">
                    <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                </a>
            </div>
        </div>
      </section>

      <!-- Footer part -->
      <footer class="footer_part">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="single_footer_part">
                        <a href="index1.php" class="footer_logo_iner">
                            <img src="assets/img/logo.png" alt="logo">
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

    <!-- Scripts JavaScript -->
    <script src="assets/js/jquery-1.12.1.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.js"></script>
    <script src="assets/js/swiper.min.js"></script>
    <script src="assets/js/masonry.pkgd.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/contact.js"></script>
    <script src="assets/js/jquery.ajaxchimp.min.js"></script>
    <script src="assets/js/jquery.form.js"></script>
    <script src="assets/js/jquery.validate.min.js"></script>
    <script src="assets/js/mail-script.js"></script>
    <script src="assets/js/custom.js"></script>
    
    <script>
// View article function
function viewArticle(articleId) {
    if (articleId > 0) {
        window.location.href = 'article_detail.php?id=' + articleId;
    } else {
        alert('Article non disponible');
    }
}

// Animation on load
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.style.opacity = '1';
    });
    
    // Animate table rows
    const tableRows = document.querySelectorAll('.history-table tbody tr');
    tableRows.forEach((row, index) => {
        row.style.animationDelay = (index * 0.1 + 0.5) + 's';
        row.style.opacity = '1';
    });
    
    // Add hover effect to table rows
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
    
    // Add hover effect to score badges
    const scoreBadges = document.querySelectorAll('.score-badge');
    scoreBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 8px 20px rgba(0, 0, 0, 0.4)';
        });
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '';
        });
    });
});
</script>
  </body>
</html>
