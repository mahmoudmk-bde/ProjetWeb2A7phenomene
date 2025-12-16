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
include_once __DIR__ . '/../../controller/QuizController.php';

try {
    $quizController = new QuizController();
    $articles = $quizController->getAllArticles();
} catch (Exception $e) {
    error_log("Erreur lors du chargement des articles: " . $e->getMessage());
    $articles = [];
}

// Fonction pour obtenir l'image selon le titre de l'article
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
        "sante" => "sante.png",
        "sante" => "sante.png",
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
    
    <!-- style CSS -->
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
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="general.css">
  </head>

  <body>
    <div class="body_bg">
      <!--::header part start::-->
      

      <!-- Section des articles de quiz -->
                <!-- banner part start-->
      <section class="banner_part">
        <div class="container1">
          <br><br>
        <h1>Choisissez un article pour commencer le quiz</h1> 
        
        <div class="grid">
    <?php if (!empty($articles)): ?>
        <?php foreach($articles as $article): ?>
            <?php 
            // Vérifier que le chemin est correct
            $imageFile = getThemeImage($article['titre'] ?? '');
            $imageUrl = "image/" . $imageFile;
            
            // Debug: afficher le chemin pour vérification
            // echo "<!-- Image path: $imageUrl -->";
            
            $articleId = htmlspecialchars($article['id_article'] ?? '');
            $articleTitle = htmlspecialchars($article['titre'] ?? 'Titre non disponible');
            ?>
            <a href="article_detail.php?id=<?= $articleId ?>" class="card-link">
                <div class="card">
                    <div class="image-container">
                        <img class="thumb" src="<?= $imageUrl ?>" alt="<?= $articleTitle ?>" 
                             onerror="this.classList.add('error'); this.src='image/default.png';"
                             onload="this.classList.add('loaded')">
                    </div>
                    <div class="title"><?= $articleTitle ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>    
        <div class="no-articles">
            <p>Aucun article disponible pour le moment.</p>
        </div>
    <?php endif; ?>
</div>
    </div>
      </section>
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