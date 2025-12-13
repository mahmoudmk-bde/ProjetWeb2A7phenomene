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
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/animate.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/flaticon.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/themify-icons.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/magnific-popup.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
    <style>
        /* Fix Header Overlap */
        .partner-hero-section {
            padding-top: 140px;
            /* Space for fixed header */
            padding-bottom: 60px;
            background: linear-gradient(180deg, rgba(20, 20, 24, 0.9) 0%, rgba(20, 20, 24, 1) 100%);
        }

        /* Hero Section Styles */
        .partner-hero-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            display: flex;
            gap: 40px;
            align-items: flex-start;
        }

        .partner-hero-logo-container {
            flex: 0 0 200px;
            height: 200px;
            background: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .partner-hero-logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .partner-hero-content {
            flex: 1;
        }

        .partner-title {
            color: white;
            font-size: 42px;
            font-weight: 800;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .partner-type-badge {
            display: inline-block;
            padding: 8px 16px;
            background: rgba(255, 74, 87, 0.15);
            color: var(--accent);
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 74, 87, 0.3);
        }

        .partner-stats-row {
            display: flex;
            gap: 30px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .stat-label {
            color: var(--muted);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Info Section Styles */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .info-card {
            background: rgba(255, 255, 255, 0.03);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 74, 87, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            font-size: 18px;
        }

        .info-content h6 {
            color: var(--muted);
            font-size: 12px;
            margin: 0;
            text-transform: uppercase;
        }

        .info-content p {
            color: white;
            margin: 0;
            font-weight: 500;
        }

        .info-content a {
            color: white;
            text-decoration: none;
            transition: color 0.2s;
        }

        .info-content a:hover {
            color: var(--accent);
        }

        /* Store Card Styles (Copied from Store) */
        .store-card {
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.05);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            height: 100%;
        }

        .store-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            border-color: var(--accent);
        }

        .store-card .game-card-img {
            position: relative;
            height: 220px;
            overflow: hidden;
            background: #0f0f12;
        }

        .store-card .game-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .store-card:hover .game-card-img img {
            transform: scale(1.05);
        }

        .store-card .game-card-body {
            display: flex;
            flex-direction: column;
            padding: 20px;
            gap: 10px;
            flex: 1;
        }

        .store-card .game-title {
            color: #fff;
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.4;
        }

        .store-card .game-category {
            color: var(--accent);
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .store-card .game-foot {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .store-card .game-price {
            color: white;
            font-weight: 700;
            font-size: 18px;
        }

        .store-card .btn-view-game {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background 0.2s;
        }

        .store-card .btn-view-game:hover {
            background: var(--accent);
        }

        .store-card .game-badge {
            position: absolute;
            right: 12px;
            top: 12px;
            background: var(--accent);
            color: #fff;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Rating Stars */
        .partner-star-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 24px;
            line-height: 1;
            transition: transform 120ms ease;
            color: #ffd700;
            padding: 0 2px;
        }

        .partner-star-btn:hover {
            transform: scale(1.2);
        }

        @media (max-width: 991px) {
            .partner-hero-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .partner-hero-logo-container {
                margin-bottom: 20px;
            }

            .partner-stats-row {
                justify-content: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="body_bg">
        <?php
        // Set header context variables
        $headerShowUserMenu = isset($_SESSION['user_id']);
        $sessionUserName = isset($_SESSION['user_name']) ? $_SESSION['user_name '] : 'Utilisateur';
        $sessionUserType = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'guest';

        // Include the common header
        include __DIR__ . '/../header_common.php';
        ?>

        <!-- Partner Hero Section -->
        <section class="partner-hero-section">
            <div class="container">
                <div class="partner-hero-card">
                    <div class="partner-hero-logo-container">
                        <?php if ($this->partenaire->logo): ?>
                            <img src="<?php echo BASE_URL . htmlspecialchars($this->partenaire->logo); ?>"
                                alt="<?= htmlspecialchars($this->partenaire->nom) ?>" class="partner-hero-logo">
                        <?php else: ?>
                            <i class="fas fa-building" style="font-size: 60px; color: #212529;"></i>
                        <?php endif; ?>
                    </div>

                    <div class="partner-hero-content">
                        <h1 class="partner-title"><?= htmlspecialchars($this->partenaire->nom) ?></h1>

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

                        <div class="description-text"
                            style="color: #cfd3d8; font-size: 16px; line-height: 1.6; max-width: 800px;">
                            <?php if ($this->partenaire->description): ?>
                                <?= nl2br(htmlspecialchars($this->partenaire->description)) ?>
                            <?php else: ?>
                                <em>Aucune description disponible pour ce partenaire.</em>
                            <?php endif; ?>
                        </div>

                        <!-- Rating & Stats -->
                        <div class="partner-stats-row">
                            <div class="stat-item">
                                <div class="stat-value" style="display:flex; align-items:center; gap:10px;">
                                    <span id="partnerRatingValue">
                                        <?= (isset($partnerRatingCount) && $partnerRatingCount > 0 && isset($partnerRatingAvg)) ? number_format($partnerRatingAvg, 1) : '0.0' ?>
                                    </span>
                                    <div style="font-size:16px;">
                                        <form id="partnerStarRateForm"
                                            data-initial="<?= (isset($partnerRatingCount) && $partnerRatingCount > 0 && isset($partnerRatingAvg)) ? (int) floor($partnerRatingAvg) : 0 ?>"
                                            method="post"
                                            action="?controller=Partenaire&action=rate&id=<?= $this->partenaire->id ?>"
                                            style="display:inline-flex;">
                                            <input type="hidden" name="score" id="partnerStarScore" value="">
                                            <button type="button" class="partner-star-btn" data-score="1">★</button>
                                            <button type="button" class="partner-star-btn" data-score="2">★</button>
                                            <button type="button" class="partner-star-btn" data-score="3">★</button>
                                            <button type="button" class="partner-star-btn" data-score="4">★</button>
                                            <button type="button" class="partner-star-btn" data-score="5">★</button>
                                        </form>
                                    </div>
                                </div>
                                <span class="stat-label">Note Partenaire</span>
                            </div>

                            <div class="stat-item">
                                <div class="stat-value"><?= count($jeux) ?></div>
                                <div class="stat-label">Jeux Publiés</div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-value">
                                    <?= isset($statsTotalViews) ? number_format($statsTotalViews) : 0 ?></div>
                                <div class="stat-label">Vues Totales</div>
                            </div>
                        </div>

                        <!-- Contact Info Grid -->
                        <div class="info-grid">
                            <?php if ($this->partenaire->email): ?>
                                <div class="info-card">
                                    <div class="info-icon"><i class="fas fa-envelope"></i></div>
                                    <div class="info-content">
                                        <h6>Email</h6>
                                        <p><a
                                                href="mailto:<?= htmlspecialchars($this->partenaire->email) ?>"><?= htmlspecialchars($this->partenaire->email) ?></a>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->partenaire->telephone): ?>
                                <div class="info-card">
                                    <div class="info-icon"><i class="fas fa-phone"></i></div>
                                    <div class="info-content">
                                        <h6>Téléphone</h6>
                                        <p><?= htmlspecialchars($this->partenaire->telephone) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($this->partenaire->site_web): ?>
                                <div class="info-card">
                                    <div class="info-icon"><i class="fas fa-globe"></i></div>
                                    <div class="info-content">
                                        <h6>Site Web</h6>
                                        <p><a href="<?= htmlspecialchars($this->partenaire->site_web) ?>"
                                                target="_blank">Visiter le site</a></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="info-card">
                                <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="info-content">
                                    <h6>Membre Depuis</h6>
                                    <p><?= date('M Y', strtotime($this->partenaire->created_at)) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Games Section -->
        <section class="section_padding" style="padding-top: 40px;">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-lg-12">
                        <h2
                            style="color: white; font-weight: 700; border-left: 5px solid var(--accent); padding-left: 20px;">
                            Catalogue de Jeux
                        </h2>
                    </div>
                </div>

                <?php if (!empty($jeux)): ?>
                    <div class="row">
                        <?php foreach ($jeux as $jeu): ?>
                            <div class="col-lg-3 col-md-6 mb-4">
                                <div class="store-card">
                                    <div class="game-card-img">
                                        <?php if ($jeu['image']): ?>
                                            <img src="<?php echo BASE_URL . htmlspecialchars($jeu['image']); ?>"
                                                alt="<?= htmlspecialchars($jeu['nom']) ?>">
                                        <?php else: ?>
                                            <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/gallery/gallery_item_1.png"
                                                alt="<?= htmlspecialchars($jeu['nom']) ?>">
                                        <?php endif; ?>
                                        <span class="game-badge"><?= ucfirst($jeu['categorie']) ?></span>
                                    </div>
                                    <div class="game-card-body">
                                        <div class="game-category"><?= htmlspecialchars($jeu['plateforme'] ?? 'PC') ?></div>
                                        <h5 class="game-title"><?= htmlspecialchars($jeu['nom']) ?></h5>

                                        <div class="game-foot">
                                            <div class="game-price"><?= number_format($jeu['prix'], 2) ?> DT</div>
                                            <a href="?controller=Store&action=show&id=<?= $jeu['id'] ?>" class="btn-view-game">
                                                Voir Détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5" style="background: rgba(255,255,255,0.05); border-radius: 15px;">
                        <i class="fas fa-gamepad" style="font-size: 50px; color: var(--muted); margin-bottom: 20px;"></i>
                        <h3 style="color: white;">Aucun jeu disponible</h3>
                        <p style="color: var(--muted);">Ce partenaire n'a pas encore publié de jeux.</p>
                    </div>
                <?php endif; ?>

                <div class="mt-5 text-center">
                    <a href="?controller=Partenaire&action=index" class="btn_1">
                        <i class="fas fa-arrow-left"></i> Retour aux Partenaires
                    </a>
                </div>
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
                                    <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/logo.png"
                                        alt="logo">
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
                                <p>©
                                    <script>document.write(new Date().getFullYear());</script> Engage. Tous droits
                                    réservés
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="footer_icon social_icon">
                                <ul class="list-unstyled">
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a>
                                    </li>
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
        (function () {
            var stars = document.querySelectorAll('.partner-star-btn');
            var scoreInput = document.getElementById('partnerStarScore');
            var form = document.getElementById('partnerStarRateForm');
            var ratingEl = document.getElementById('partnerRatingValue');
            function paint(n) {
                stars.forEach(function (s, i) {
                    s.style.color = (i < n) ? '#ffd700' : '#777';
                });
            }
            stars.forEach(function (btn) {
                btn.addEventListener('mouseover', function () {
                    var v = parseInt(btn.getAttribute('data-score'), 10) || 1;
                    paint(Math.max(1, Math.min(5, v)));
                });
                btn.addEventListener('focus', function () {
                    var v = parseInt(btn.getAttribute('data-score'), 10) || 1;
                    paint(Math.max(1, Math.min(5, v)));
                });
                btn.addEventListener('click', function () {
                    var val = parseInt(btn.getAttribute('data-score'), 10) || 1;
                    val = Math.max(1, Math.min(5, val));
                    scoreInput.value = val;
                    paint(val);
                    if (ratingEl) { ratingEl.textContent = val.toFixed(1); }
                    form.submit();
                });
                btn.addEventListener('mouseleave', function () {
                    var init = 0;
                    if (form) {
                        var a = parseInt(form.getAttribute('data-initial') || '0', 10);
                        if (!isNaN(a)) init = Math.max(0, Math.min(5, a));
                    }
                    paint(init);
                });
            });
            var init = 0; if (form) { var a = parseInt(form.getAttribute('data-initial') || '0', 10); if (!isNaN(a)) init = Math.max(0, Math.min(5, a)); }
            paint(init);
        })();
    </script>
</body>

</html>