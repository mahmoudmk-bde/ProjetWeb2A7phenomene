<?php
// La variable $items contient tous les jeux du store
// Passée par le StoreController
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gaming Store - Engage</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/assets/img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/bootstrap.min.css">
    <!-- animate CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/owl.carousel.min.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/all.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/flaticon.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/themify-icons.css">
    <!-- magnific popup CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/magnific-popup.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/style.css">
    <!-- Custom Frontoffice CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/custom-frontoffice.css">
</head>

<body>
    <div class="body_bg">
        <!--::header part start::-->
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                                <img src="<?php echo BASE_URL; ?>view/frontoffice/assets/img/logo.png" alt="logo">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>

                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo BASE_URL; ?>">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="?controller=Partenaire&action=index">Partenaires</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="?controller=Store&action=index">Store</a>
                                    </li>
                                </ul>
                            </div>
                            <a href="#" class="btn_1 d-none d-sm-block">Rejoindre</a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header part end-->

        <!-- breadcrumb start-->
        <section class="breadcrumb breadcrumb_bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="breadcrumb_iner text-center">
                            <div class="breadcrumb_iner_item">
                                <h2>Gaming Store</h2>
                                <p>Découvrez nos jeux vidéo exclusifs</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb end-->

        <!-- Store Section Start -->
        <section class="section_padding">
            <div class="container">
                <!-- Filtres de catégorie -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-lg-12 text-center">
                            <h4 class="filter-title">
                                <i class="fas fa-filter"></i> Filtrer par catégorie
                            </h4>
                            <div class="filter-buttons">
                                <button class="filter-btn active" data-filter="all">
                                    <i class="fas fa-th"></i> Tous les jeux
                                </button>
                                <button class="filter-btn" data-filter="action">
                                    <i class="fas fa-crosshairs"></i> Action
                                </button>
                                <button class="filter-btn" data-filter="aventure">
                                    <i class="fas fa-map"></i> Aventure
                                </button>
                                <button class="filter-btn" data-filter="sport">
                                    <i class="fas fa-football-ball"></i> Sport
                                </button>
                                <button class="filter-btn" data-filter="strategie">
                                    <i class="fas fa-chess"></i> Stratégie
                                </button>
                                <button class="filter-btn" data-filter="simulation">
                                    <i class="fas fa-plane"></i> Simulation
                                </button>
                                <button class="filter-btn" data-filter="rpg">
                                    <i class="fas fa-dragon"></i> RPG
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="stats-panel">
                            <span>
                                <i class="fas fa-gamepad"></i> 
                                <strong><?= count($items) ?></strong> jeux disponibles
                            </span>
                            <span>
                                <i class="fas fa-users"></i> 
                                <strong><?= count(array_unique(array_column($items, 'partenaire_id'))) ?></strong> partenaires
                            </span>
                            <span>
                                <i class="fas fa-star"></i> 
                                <strong>Top qualité</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Liste des jeux -->
                <?php if (empty($items)): ?>
                    <!-- Message si aucun jeu -->
                    <div class="no-games">
                        <i class="fas fa-gamepad"></i>
                        <h3 class="no-games-title">Aucun jeu disponible pour le moment</h3>
                        <p class="no-games-text">Revenez bientôt pour découvrir nos nouveautés !</p>
                    </div>
                <?php else: ?>
                    <!-- Grille de jeux -->
                    <div class="row" id="games-container">
                        <?php foreach ($items as $item): ?>
                            <div class="col-lg-4 col-md-6 game-item" data-category="<?= $item['categorie'] ?>">
                                <div class="game-card">
                                    <!-- Image du jeu -->
                                    <div class="game-card-img">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo BASE_URL . htmlspecialchars($item['image']); ?>" 
                                                 alt="<?= htmlspecialchars($item['nom']) ?>">
                                        <?php else: ?>
                                            <img src="<?php echo BASE_URL; ?>view/frontoffice/assets/img/gallery/gallery_item_1.png" 
                                                 alt="<?= htmlspecialchars($item['nom']) ?>">
                                        <?php endif; ?>
                                        
                                        <!-- Badge de catégorie -->
                                        <div class="game-badge">
                                            <?= ucfirst($item['categorie']) ?>
                                        </div>
                                        
                                        <!-- Badge de stock -->
                                        <?php if ($item['stock'] > 0): ?>
                                            <?php if ($item['stock'] < 5): ?>
                                                <div class="stock-badge stock-low">
                                                    <i class="fas fa-exclamation-triangle"></i> Stock limité
                                                </div>
                                            <?php else: ?>
                                                <div class="stock-badge">
                                                    <i class="fas fa-check"></i> En stock
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="stock-badge stock-out">
                                                <i class="fas fa-times"></i> Rupture
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Prix -->
                                        <div class="game-price">
                                            <?= number_format($item['prix'], 2) ?> DT
                                        </div>
                                    </div>
                                    
                                    <!-- Corps de la carte -->
                                    <div class="game-card-body">
                                        <h5 class="game-title">
                                            <?= htmlspecialchars($item['nom']) ?>
                                        </h5>
                                        
                                        <div class="game-partner">
                                            <i class="fas fa-handshake"></i> 
                                            Par <?= htmlspecialchars($item['partenaire_nom'] ?? 'Inconnu') ?>
                                        </div>
                                        
                                        <div class="game-info">
                                            <span class="game-category">
                                                <?= ucfirst($item['categorie']) ?>
                                            </span>
                                            <?php if ($item['age_minimum']): ?>
                                                <span class="game-age">
                                                    <i class="fas fa-child"></i> <?= $item['age_minimum'] ?>+
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($item['plateforme']): ?>
                                            <div class="game-platform">
                                                <i class="fas fa-desktop"></i> 
                                                <?= htmlspecialchars($item['plateforme']) ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <a href="?controller=Store&action=show&id=<?= $item['id'] ?>" 
                                           class="btn-view-game">
                                            <i class="fas fa-eye"></i> Voir les détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <!-- Store Section End -->

        <!--::footer_part start::-->
        <footer class="footer_part">
            <div class="footer_top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <a href="<?php echo BASE_URL; ?>" class="footer_logo_iner">
                                    <img src="<?php echo BASE_URL; ?>view/frontoffice/assets/img/logo.png" alt="logo">
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
                                    <li><a href="?controller=Store&action=index">Store</a></li>
                                    <li><a href="?controller=Partenaire&action=index">Partenaires</a></li>
                                    <li><a href="#">Missions</a></li>
                                    <li><a href="#">Événements</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Newsletter</h4>
                                <p>Inscrivez-vous pour recevoir nos nouveautés</p>
                                <div id="mc_embed_signup">
                                    <form action="#" method="get" class="subscribe_form relative mail_part">
                                        <input type="email" name="email" placeholder="Email Address" 
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
        <!--::footer_part end::-->
    </div>

    <!-- jquery plugins -->
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/jquery.magnific-popup.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/owl.carousel.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/assets/js/custom.js"></script>

    <script>
        $(document).ready(function() {
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                
                var filterValue = $(this).attr('data-filter');
                
                if (filterValue === 'all') {
                    $('.game-item').fadeIn(400);
                } else {
                    $('.game-item').fadeOut(200);
                    setTimeout(function() {
                        $('.game-item[data-category="' + filterValue + '"]').fadeIn(400);
                    }, 200);
                }
            });
        });
    </script>
</body>
</html>
