<?php
// $this->partenaire contient les données du partenaire
// $jeux contient tous les jeux de ce partenaire
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($this->partenaire->nom); ?> - Engage</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/assets/img/favicon.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/animate.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/flaticon.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/themify-icons.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/magnific-popup.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/assets/css/custom-frontoffice.css">
    <style>
        .stats-box {
            background: var(--panel);
            padding: 30px;
            border-radius: var(--card-radius);
            text-align: center;
            border: 2px solid var(--accent);
            margin-top: 20px;
        }
        .stats-number {
            color: var(--accent);
            font-size: 48px;
            font-weight: bold;
        }
        .stats-label {
            color: var(--muted);
            font-size: 16px;
            margin-top: 10px;
        }
        .description-text {
            color: var(--muted);
            line-height: 1.8;
        }
        .game-price-badge {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background: rgba(0, 0, 0, 0.8);
            color: var(--accent);
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 18px;
            font-weight: bold;
        }
    </style>
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
                                data-target="#navbarSupportedContent">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>

                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?php echo BASE_URL; ?>">Home</a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="?controller=Partenaire&action=index">Partenaires</a>
                                    </li>
                                    <li class="nav-item">
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

        <!-- Profile Header -->
        <section class="profile-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-3 text-center">
                        <?php if ($this->partenaire->logo): ?>
                            <img src="<?php echo BASE_URL . htmlspecialchars($this->partenaire->logo); ?>" 
                                 alt="<?= htmlspecialchars($this->partenaire->nom) ?>" 
                                 class="partner-logo-large">
                        <?php else: ?>
                            <div class="partner-logo-large d-flex align-items-center justify-content-center" style="background: rgba(255,255,255,0.1);">
                                <i class="fas fa-building" style="font-size: 80px; color: #6c757d;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-9">
                        <h1 class="partner-name-large">
                            <?= htmlspecialchars($this->partenaire->nom) ?>
                        </h1>
                        <span class="partner-type-badge">
                            <?php
                            $types = [
                                'sponsor' => '<i class="fas fa-dollar-sign"></i> Sponsor Officiel',
                                'testeur' => '<i class="fas fa-flask"></i> Testeur Professionnel',
                                'vendeur' => '<i class="fas fa-store"></i> Vendeur Certifié'
                            ];
                            echo $types[$this->partenaire->type] ?? ucfirst($this->partenaire->type);
                            ?>
                        </span>
                        <div class="mt-3">
                            <a href="?controller=Partenaire&action=index" style="color: rgba(255,255,255,0.9); text-decoration: none; font-size: 16px;">
                                <i class="fas fa-arrow-left"></i> Retour à la liste des partenaires
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Profile Content -->
        <section class="section_padding">
            <div class="container">
                <div class="row">
                    <!-- Informations de contact -->
                    <div class="col-lg-4">
                        <div class="info-section">
                            <h3 style="color: white; margin-bottom: 25px;">
                                <i class="fas fa-info-circle"></i> Informations
                            </h3>

                            <?php if ($this->partenaire->email): ?>
                                <div class="info-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <strong>Email :</strong><br>
                                        <a href="mailto:<?= htmlspecialchars($this->partenaire->email) ?>" style="color: var(--accent); text-decoration: none;">
                                            <?= htmlspecialchars($this->partenaire->email) ?>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->partenaire->telephone): ?>
                                <div class="info-item">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <strong>Téléphone :</strong><br>
                                        <span style="color: var(--text-light);"><?= htmlspecialchars($this->partenaire->telephone) ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->partenaire->site_web): ?>
                                <div class="info-item">
                                    <i class="fas fa-globe"></i>
                                    <div>
                                        <strong>Site Web :</strong><br>
                                        <a href="<?= htmlspecialchars($this->partenaire->site_web) ?>" 
                                           target="_blank" 
                                           style="color: var(--accent); text-decoration: none;">
                                            Visiter le site <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="info-item">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <strong>Membre depuis :</strong><br>
                                    <span style="color: var(--text-light);"><?= date('F Y', strtotime($this->partenaire->created_at)) ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Statistiques -->
                        <div class="stats-box">
                            <div class="stats-number"><?= count($jeux) ?></div>
                            <div class="stats-label">
                                <?= count($jeux) > 1 ? 'Jeux proposés' : 'Jeu proposé' ?>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-lg-8">
                        <div class="description-section">
                            <h3 style="color: white; margin-bottom: 25px;">
                                <i class="fas fa-align-left"></i> À propos de <?= htmlspecialchars($this->partenaire->nom) ?>
                            </h3>
                            <?php if ($this->partenaire->description): ?>
                                <p class="description-text">
                                    <?= nl2br(htmlspecialchars($this->partenaire->description)) ?>
                                </p>
                            <?php else: ?>
                                <p class="description-text">
                                    <em>Aucune description disponible pour ce partenaire.</em>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Jeux du partenaire -->
                <?php if (!empty($jeux)): ?>
                    <div class="games-section">
                        <div class="row mb-4">
                            <div class="col-lg-12 text-center">
                                <h2 style="color: white; font-size: 36px; font-weight: bold;">
                                    <i class="fas fa-gamepad"></i> Jeux proposés par ce partenaire
                                </h2>
                                <p style="color: var(--muted); font-size: 16px;">
                                    Découvrez tous les jeux vidéo disponibles
                                </p>
                            </div>
                        </div>

                        <!-- Grille de jeux -->
                        <div class="row">
                            <?php foreach ($jeux as $jeu): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="game-card">
                                        <!-- Image du jeu -->
                                        <div class="game-card-img" style="position: relative;">
                                            <?php if ($jeu['image']): ?>
                                                <img src="<?php echo BASE_URL . htmlspecialchars($jeu['image']); ?>" 
                                                     alt="<?= htmlspecialchars($jeu['nom']) ?>">
                                            <?php else: ?>
                                                <img src="<?php echo BASE_URL; ?>view/frontoffice/assets/img/gallery/gallery_item_1.png" 
                                                     alt="<?= htmlspecialchars($jeu['nom']) ?>">
                                            <?php endif; ?>

                                            <!-- Prix -->
                                            <div class="game-price-badge">
                                                <?= number_format($jeu['prix'], 2) ?> DT
                                            </div>
                                        </div>

                                        <!-- Corps de la carte -->
                                        <div class="game-card-body">
                                            <!-- Titre -->
                                            <h5 class="game-title">
                                                <?= htmlspecialchars($jeu['nom']) ?>
                                            </h5>

                                            <!-- Catégorie -->
                                            <span class="game-category">
                                                <?= ucfirst($jeu['categorie']) ?>
                                            </span>

                                            <!-- Informations supplémentaires -->
                                            <div class="game-info">
                                                <?php if ($jeu['plateforme']): ?>
                                                    <span><i class="fas fa-desktop"></i> <?= htmlspecialchars($jeu['plateforme']) ?></span>
                                                <?php endif; ?>
                                                <?php if ($jeu['age_minimum']): ?>
                                                    <span class="game-age">
                                                        <i class="fas fa-child"></i> <?= $jeu['age_minimum'] ?>+
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Stock -->
                                            <?php if ($jeu['stock'] > 0): ?>
                                                <div style="color: var(--success); font-size: 13px; margin-bottom: 15px;">
                                                    <i class="fas fa-check-circle"></i> En stock (<?= $jeu['stock'] ?>)
                                                </div>
                                            <?php else: ?>
                                                <div style="color: #dc3545; font-size: 13px; margin-bottom: 15px;">
                                                    <i class="fas fa-times-circle"></i> Rupture de stock
                                                </div>
                                            <?php endif; ?>

                                            <!-- Bouton -->
                                            <a href="?controller=Store&action=show&id=<?= $jeu['id'] ?>" 
                                               class="btn-view-game">
                                                <i class="fas fa-eye"></i> Voir les détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Message si aucun jeu -->
                    <div class="no-games">
                        <i class="fas fa-gamepad"></i>
                        <h3 class="no-games-title">Aucun jeu disponible pour le moment</h3>
                        <p class="no-games-text">Ce partenaire n'a pas encore ajouté de jeux au store.</p>
                        <a href="?controller=Store&action=index" class="btn_1 mt-3">
                            <i class="fas fa-arrow-left"></i> Voir tous les jeux
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

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
</body>
</html>
