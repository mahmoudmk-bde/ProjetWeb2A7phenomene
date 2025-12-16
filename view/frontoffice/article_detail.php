<?php
session_start();

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

// Inclure les fichiers pour les articles de quiz
include_once __DIR__ . '/../../Controller/QuizController.php';
$quizController = new QuizController();

// Validation sécurisée de l'ID
$article_id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
if (!$article_id || $article_id <= 0) {
    $_SESSION['error'] = "ID d'article invalide.";
    header('Location: quiz.php');
    exit;
}

$article = $quizController->getArticleById($article_id);

if (!$article) {
    $_SESSION['error'] = "L'article demandé n'existe pas.";
    header('Location: quiz.php');
    exit;
}

$questions = $quizController->getQuizByArticle($article_id);
$hasQuiz = !empty($questions);

function getThemeImage($titre) {
    $titre = strtolower(trim($titre));
    
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

$imageUrl = "image/" . getThemeImage($article['titre'] ?? '');
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
    <title>Engage - Plateforme de Quiz Éducatifs</title>
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
    
    <style>
      /* Styles pour le menu utilisateur */
      .user-menu {
        position: relative;
        display: inline-block;
      }
      
      .user-dropdown {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        min-width: 180px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        border-radius: 5px;
        z-index: 1000;
        margin-top: 10px;
      }
      
      .user-dropdown.show {
        display: block;
      }
      
      .user-dropdown a {
        display: block;
        padding: 12px 16px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #eee;
        transition: background 0.3s;
        font-size: 14px;
      }
      
      .user-dropdown a:hover {
        background: #f8f9fa;
        color: #007bff;
      }
      
      .user-dropdown a:last-child {
        border-bottom: none;
        color: #dc3545;
      }
      
      .user-dropdown a:last-child:hover {
        background: #dc3545;
        color: white;
      }
      
      .user-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        color: white;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 25px;
        transition: all 0.3s ease;
      }
      
      .user-wrapper:hover {
        background: rgba(255,255,255,0.1);
      }
      
      .user-name {
        font-weight: bold;
        font-size: 14px;
      }
      
      .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(255,255,255,0.3);
        transition: all 0.3s ease;
        overflow: hidden;
      }
      
      .user-avatar:hover {
        border-color: rgba(255,255,255,0.6);
        transform: scale(1.05);
      }
      
      .user-avatar i {
        color: white;
        font-size: 18px;
      }
      
      .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
      }
    </style>
    <link rel="stylesheet" href="artic_detaa.css">
    <link rel="stylesheet" href="general.css">
  </head>

  <body>
    <div class="body_bg">
      <!--::header part start::-->
      <header class="main_menu single_page_menu">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-12">
              <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="index.html">
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
                      <a class="nav-link" href="index.html">Home</a>
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
                    </li>
                    <li class="nav-item">
                      <a class="nav-link" href="contact.html">Contact</a>
                    </li>
                  </ul>
                </div>
                <?php
                if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
                    // Utilisateur connecté - afficher le menu utilisateur
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
                          <i class="fas fa-history"></i> Mon Historique
                        </a>
                        <a href="logout.php">
                          <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                        </a>
                      </div>
                    </div>
                    <?php
                } else {
                    // Utilisateur non connecté - afficher le bouton de connexion
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

      <!-- Section des articles de quiz -->
      <!-- banner part start-->
      <section class="banner_part">
        <div class="container2">
          <br><br><br>
          
          <!-- Messages d'erreur -->
          <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= htmlspecialchars($_SESSION['error']); ?>
              <?php unset($_SESSION['error']); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>
          
          <main role="main">
            <article aria-labelledby="article-title">
              <div class="article-header">
                <img 
                  src="<?= $imageUrl ?>" 
                  alt="<?= htmlspecialchars($article['titre']) ?>" 
                  class="article-image"
                  loading="lazy">
                <h1 id="article-title" class="article-title"><?= htmlspecialchars($article['titre']) ?></h1>
                <div class="article-date">
                  Publié le : <?= date('d/m/Y', strtotime($article['date_creation'] ?? $article['date_publication'] ?? 'now')) ?>
                </div>
                
                <?php if($hasQuiz): ?>
                <div class="quiz-launch-section">
                  <a href="quiz_page.php?article_id=<?= $article_id ?>" class="quiz-launch-btn">
                    <span class="quiz-icon">🎯</span>Lancer le Quiz
                  </a>
                </div>
                <?php endif; ?>
              </div>
              
              <div class="article-content">
                <?= nl2br(htmlspecialchars($article['contenu'])) ?>
              </div>
            </article>
          </main>
        </div>
      </section>
      

      <!-- Footer part -->
      <footer class="footer_part">
    <div class="footer_top">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 col-lg-3">
                    <div class="single_footer_part">
                        <a href="index.html" class="footer_logo_iner">
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
      // Fonction pour afficher/masquer le menu utilisateur
      function toggleUserMenu() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
      }
      
      // Fermer le menu si on clique ailleurs
      document.addEventListener('click', function(event) {
        const userMenu = document.querySelector('.user-menu');
        const dropdown = document.getElementById('userDropdown');
        
        if (!userMenu.contains(event.target)) {
          dropdown.classList.remove('show');
        }
      });
      
      // Optionnel: Fermer le menu en appuyant sur Echap
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          const dropdown = document.getElementById('userDropdown');
          dropdown.classList.remove('show');
        }
      });

      // Animation pour les cartes d'articles
      document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.article-card');
        cards.forEach((card, index) => {
          card.style.opacity = '0';
          card.style.transform = 'translateY(30px)';
          
          setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, index * 100);
        });
      });
    </script>
    <script src="indexx.js"></script>
  </body>
</html>