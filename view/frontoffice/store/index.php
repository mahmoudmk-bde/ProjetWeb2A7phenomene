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
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <!-- animate CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/owl.carousel.min.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/flaticon.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/themify-icons.css">
    <!-- magnific popup CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/magnific-popup.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <!-- Custom Frontoffice CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
</head>

<body>
    <div class="body_bg">
    <?php include 'header_mission.php'; ?>
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
                                Filtrer par catégorie
                            </h4>
                            <div class="filter-buttons">
                                <a class="filter-btn" href="?controller=Store&action=index" data-filter="all">
                                    <i class="fas fa-th"></i> Tous les jeux
                                </a>
                                <a class="filter-btn" href="?controller=Store&action=index&categorie=action" data-filter="action">Action</a>
                                <a class="filter-btn" href="?controller=Store&action=index&categorie=aventure" data-filter="aventure">Aventure</a>
                                <a class="filter-btn" href="?controller=Store&action=index&categorie=sport" data-filter="sport">Sport</a>
                                <a class="filter-btn" href="?controller=Store&action=index&categorie=strategie" data-filter="strategie">Stratégie</a>
                                <a class="filter-btn" href="?controller=Store&action=index&categorie=simulation" data-filter="simulation">Simulation</a>
                                <a class="filter-btn" href="?controller=Store&action=index&categorie=rpg" data-filter="rpg">RPG</a>
                                <a class="filter-btn" href="?controller=Store&action=index&categorie=educatif" data-filter="educatif">Éducatif</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="stats-panel">
                            <span><strong><?= count($items) ?></strong> jeux disponibles</span>
                            <span><strong><?= count(array_unique(array_column($items, 'partenaire_id'))) ?></strong> partenaires</span>
                            <span><strong>Top qualité</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Liste des jeux -->
                <?php if (empty($items)): ?>
                    <!-- Message si aucun jeu -->
                    <div class="no-games">
                        
                        <h3 class="no-games-title">Aucun jeu disponible pour le moment</h3>
                        <p class="no-games-text">Revenez bientôt pour découvrir nos nouveautés !</p>
                    </div>
                <?php else: ?>
                    <!-- Grille de jeux -->
                    <div class="row" id="games-container">
                        <?php foreach ($items as $item): ?>
                            <div class="col-lg-4 col-md-6 game-item" data-category="<?= $item['categorie'] ?>">
                                <div class="game-card store-card">
                                    <!-- Image du jeu -->
                                    <div class="game-card-img">
                                        <?php if ($item['image']): ?>
                                            <img src="<?php echo BASE_URL . htmlspecialchars($item['image']); ?>" 
                                                 alt="<?= htmlspecialchars($item['nom']) ?>">
                                        <?php else: ?>
                                            <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/gallery/gallery_item_1.png" 
                                                 alt="<?= htmlspecialchars($item['nom']) ?>">
                                        <?php endif; ?>
                                        
                                        <!-- Badge de catégorie -->
                                        <div class="game-badge">
                                            <?= ucfirst($item['categorie']) ?>
                                        </div>
                                        
                                        <!-- Badge de stock -->
                                        <?php if ($item['stock'] > 0): ?>
                                            <?php if ($item['stock'] < 5): ?>
                                                <div class="stock-badge stock-low">Stock limité</div>
                                            <?php else: ?>
                                                <div class="stock-badge">En stock</div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="stock-badge stock-out">Rupture</div>
                                        <?php endif; ?>
                                        
                                        
                                    </div>
                                    
                                    <!-- Corps de la carte -->
                                    <div class="game-card-body">
                                        <h5 class="game-title">
                                            <?= htmlspecialchars($item['nom']) ?>
                                        </h5>
                                        
                                        <div class="game-partner">Par <?= htmlspecialchars($item['partenaire_nom'] ?? 'Inconnu') ?></div>
                                        
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
                                            <div class="game-platform"><?= htmlspecialchars($item['plateforme']) ?></div>
                                        <?php endif; ?>
                                        
                                        <a href="?controller=Store&action=show&id=<?= $item['id'] ?>" 
                                           class="btn-view-game">Voir les détails</a>
                                        <div class="game-foot">
                                            <div class="game-price-inline"><?= number_format($item['prix'], 2) ?> DT</div>
                                            <div class="game-stats">
                                                <span><?= isset($item['views_count']) ? (int)$item['views_count'] : 0 ?> vues</span>
                                                <span><?= isset($item['likes_count']) ? (int)$item['likes_count'] : 0 ?> likes</span>
                                            </div>
                                        </div>
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
                                    <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/logo.png" alt="logo">
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
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery.magnific-popup.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/owl.carousel.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/custom.js"></script>

    <script>
        $(function(){
            var c = (new URLSearchParams(window.location.search).get('categorie')||'all').toLowerCase();
            $('.filter-btn').removeClass('active');
            $('.filter-btn[data-filter="'+c+'"]').addClass('active');
        });
    </script>
</body>
</html>
