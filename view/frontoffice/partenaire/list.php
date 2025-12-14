<?php
// La variable $partenaires contient tous les partenaires actifs
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Nos Partenaires - Engage</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/animate.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/flaticon.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/magnific-popup.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
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
                                <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/logo.png" alt="logo">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>

                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav ml-auto">
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo BASE_URL; ?>view/frontoffice/index.php">Accueil</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo BASE_URL; ?>view/frontoffice/missionlist.php">Missions</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="?controller=Store&action=index">Store</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="?controller=Partenaire&action=index">Partenaires</a>
                                    </li>
                                </ul>
                            </div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="<?php echo BASE_URL; ?>view/frontoffice/index1.php" class="btn_1 d-none d-sm-block">Mon Espace</a>
                            <?php else: ?>
                                <a href="<?php echo BASE_URL; ?>view/frontoffice/connexion.php" class="btn_1 d-none d-sm-block">Se connecter</a>
                            <?php endif; ?>
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
                                <h2>Nos Partenaires</h2>
                                <p>Découvrez les entreprises qui nous font confiance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb end-->

        <!-- Partners Section Start -->
        <section class="section_padding">
            <div class="container">
                <!-- Filtres par type -->
                <div class="filter-section">
                    <div class="row align-items-center">
                        <div class="col-lg-12 text-center">
                            <h4><i class="fas fa-filter"></i> Filtrer par type</h4>
                            <div class="filter-buttons">
                                <a class="filter-btn" href="?controller=Partenaire&action=index" data-filter="all">
                                    <i class="fas fa-th"></i> Tous
                                </a>
                                <a class="filter-btn" href="?controller=Partenaire&action=index&type=sponsor" data-filter="sponsor">
                                    <i class="fas fa-dollar-sign"></i> Sponsors
                                </a>
                                <a class="filter-btn" href="?controller=Partenaire&action=index&type=testeur" data-filter="testeur">
                                    <i class="fas fa-flask"></i> Testeurs
                                </a>
                                <a class="filter-btn" href="?controller=Partenaire&action=index&type=vendeur" data-filter="vendeur">
                                    <i class="fas fa-store"></i> Vendeurs
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="stats-panel">
                            <span>
                                <i class="fas fa-handshake"></i> 
                                <strong><?= count($partenaires) ?></strong> partenaires actifs
                            </span>
                            <span>
                                <i class="fas fa-users"></i> 
                                <strong>Des milliers</strong> de gamers connectés
                            </span>
                            <span>
                                <i class="fas fa-trophy"></i> 
                                <strong>Excellence</strong> garantie
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Liste des partenaires -->
                <?php if (empty($partenaires)): ?>
                    <div class="no-games">
                        <i class="fas fa-handshake"></i>
                        <h3 class="no-games-title">Aucun partenaire disponible</h3>
                        <p class="no-games-text">Revenez bientôt pour découvrir nos partenaires !</p>
                    </div>
                <?php else: ?>
                    <div class="row" id="partners-container">
                        <?php foreach ($partenaires as $partenaire): ?>
                            <div class="col-lg-4 col-md-6 partner-item" data-type="<?= strtolower(trim($partenaire['type'])) ?>">
                                <div class="partner-card">
                                    <!-- Logo du partenaire -->
                                    <div class="partner-logo-container">
                                        <?php if ($partenaire['logo']): ?>
                                            <img src="<?php echo BASE_URL . htmlspecialchars($partenaire['logo']); ?>" 
                                                 alt="<?= htmlspecialchars($partenaire['nom']) ?>" 
                                                 class="partner-logo">
                                        <?php else: ?>
                                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #666;">
                                                <i class="fas fa-building" style="font-size: 60px;"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Corps de la carte -->
                                    <div class="partner-body">
                                        <h3 class="partner-name"><?= htmlspecialchars($partenaire['nom']) ?></h3>

                                        <span class="partner-type">
                                            <?php
                                            $types = [
                                                'sponsor' => '<i class="fas fa-dollar-sign"></i> Sponsor',
                                                'testeur' => '<i class="fas fa-flask"></i> Testeur',
                                                'vendeur' => '<i class="fas fa-store"></i> Vendeur'
                                            ];
                                            echo $types[$partenaire['type']] ?? ucfirst($partenaire['type']);
                                            ?>
                                        </span>

                                        <p class="partner-description">
                                            <?php
                                            $desc = $partenaire['description'] ?? 'Aucune description.';
                                            echo htmlspecialchars(mb_substr($desc, 0, 100)) . (mb_strlen($desc) > 100 ? '...' : '');
                                            ?>
                                        </p>

                                        <?php if ($partenaire['email']): ?>
                                            <div class="partner-info">
                                                <i class="fas fa-envelope"></i> <?= htmlspecialchars($partenaire['email']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($partenaire['telephone']): ?>
                                            <div class="partner-info">
                                                <i class="fas fa-phone"></i> <?= htmlspecialchars($partenaire['telephone']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <a href="?controller=Partenaire&action=show&id=<?= $partenaire['id'] ?>" class="btn-view-partner">
                                            <i class="fas fa-eye"></i> Voir le profil complet
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <!-- Partners Section End -->

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
        $(function() {
            var initial = (new URLSearchParams(window.location.search).get('type')||'all').toLowerCase();
            $('.filter-btn').removeClass('active');
            $('.filter-btn[data-filter="'+initial+'"]').addClass('active');
        });
    </script>
</body>
</html>
