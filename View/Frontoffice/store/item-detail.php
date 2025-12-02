<?php
// $this->storeItem contient les données du jeu
// $autresJeux contient d'autres jeux du même partenaire
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($this->storeItem->nom); ?> - Engage Store</title>
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
    <style>
        .game-detail-section {
            padding: 80px 0;
        }
        .game-main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            border-radius: 15px;
            border: 3px solid var(--accent);
            box-shadow: 0 10px 40px rgba(255, 74, 87, 0.3);
        }
        .game-info-item {
            background: var(--card-bg);
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid var(--accent);
        }
        .game-info-label {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 5px;
        }
        .game-info-value {
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .related-games {
            margin-top: 60px;
        }
        .related-game-card {
            background: var(--panel);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .related-game-card:hover {
            transform: translateY(-5px);
            border-color: var(--accent);
        }
        .related-game-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .related-game-body {
            padding: 20px;
        }
        .badge-custom {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            margin: 5px;
            display: inline-block;
        }
        .badge-action { background: #007bff; color: white; }
        .badge-aventure { background: #28a745; color: white; }
        .badge-sport { background: #ffc107; color: #212529; }
        .badge-strategie { background: #6f42c1; color: white; }
        .badge-simulation { background: #fd7e14; color: white; }
        .badge-rpg { background: #e83e8c; color: white; }
        .partner-badge {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-top: 20px;
            border: 2px solid var(--accent);
        }
        .stock-indicator {
            padding: 10px 20px;
            border-radius: 10px;
            text-align: center;
            margin: 15px 0;
            font-weight: bold;
        }
        .stock-good {
            background: var(--success);
            color: white;
        }
        .stock-low {
            background: var(--warning);
            color: #212529;
        }
        .stock-out {
            background: #dc3545;
            color: white;
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
                                    <li class="nav-item active">
                                        <a class="nav-link" href="?controller=Store&action=index">Store</a>
                                    </li>
                                    <li class="nav-item">
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
                                <h2><?= htmlspecialchars($this->storeItem->nom) ?></h2>
                                <p>
                                    <a href="?controller=Store&action=index">Store</a> / 
                                    <?= htmlspecialchars($this->storeItem->nom) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb end-->

        <!-- Game Detail Section Start -->
        <section class="game-detail-section">
            <div class="container">
                <div class="row">
                    <!-- Image principale du jeu -->
                    <div class="col-lg-6 mb-4">
                        <?php if ($this->storeItem->image): ?>
                            <img src="<?php echo BASE_URL . htmlspecialchars($this->storeItem->image); ?>" 
                                 alt="<?= htmlspecialchars($this->storeItem->nom) ?>" 
                                 class="game-main-image">
                        <?php else: ?>
                            <div class="game-main-image" style="background: var(--card-bg); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-gamepad" style="font-size: 80px; color: var(--muted);"></i>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Informations du jeu -->
                    <div class="col-lg-6">
                        <div class="game-detail-card">
                            <!-- Titre -->
                            <h1 class="game-title-main">
                                <?= htmlspecialchars($this->storeItem->nom) ?>
                            </h1>
                            <div style="display:flex; gap:15px; align-items:center; margin-top:8px;">
                                <span style="color: var(--muted);<?= '' ?>"><?= isset($this->storeItem->views_count) ? (int)$this->storeItem->views_count : 0 ?> vues</span>
                                <span style="color: var(--muted);<?= '' ?>"><?= isset($this->storeItem->likes_count) ? (int)$this->storeItem->likes_count : 0 ?> likes</span>
                                <?php if (isset($itemRatingAvg) && $itemRatingAvg !== null): ?>
                                    <span style="color: var(--accent); font-weight:600;">
                                        <?= number_format($itemRatingAvg, 1) ?>/5
                                    </span>
                                    <span class="rating-stars" aria-label="Note moyenne">
                                        <?php 
                                        $full = (int)floor($itemRatingAvg); 
                                        $empty = 5 - $full; 
                                        for ($i=0; $i<$full; $i++) echo '★'; 
                                        for ($i=0; $i<$empty; $i++) echo '☆'; 
                                        ?>
                                        <span style="color:var(--muted); margin-left:6px; font-size:13px;">(<?= (int)$itemRatingCount ?>)</span>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Catégorie -->
                            <div class="mb-3">
                                <?php
                                $categoryBadges = [
                                    'action' => 'badge-action',
                                    'aventure' => 'badge-aventure',
                                    'sport' => 'badge-sport',
                                    'strategie' => 'badge-strategie',
                                    'simulation' => 'badge-simulation',
                                    'rpg' => 'badge-rpg'
                                ];
                                $badgeClass = $categoryBadges[$this->storeItem->categorie] ?? 'badge-secondary';
                                ?>
                                <span class="badge-custom <?= $badgeClass ?>">
                                    <?= ucfirst($this->storeItem->categorie) ?>
                                </span>
                                
                                <?php if ($this->storeItem->age_minimum): ?>
                                    <span class="badge-custom" style="background: var(--warning); color: #212529;">
                                        <?= $this->storeItem->age_minimum ?>+ ans
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Prix -->
                            <div class="game-price-large">
                                <?= number_format($this->storeItem->prix, 2) ?> DT
                            </div>

                            <!-- Indicateur de stock -->
                            <?php if ($this->storeItem->stock > 0): ?>
                                <?php if ($this->storeItem->stock < 5): ?>
                                    <div class="stock-indicator stock-low">
                                        Plus que <?= $this->storeItem->stock ?> exemplaires en stock !
                                    </div>
                                <?php else: ?>
                                    <div class="stock-indicator stock-good">
                                        En stock (<?= $this->storeItem->stock ?> disponibles)
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="stock-indicator stock-out">
                                    Rupture de stock
                                </div>
                            <?php endif; ?>

                            <!-- Informations détaillées -->
                            <div class="mt-4">
                                <!-- Plateforme -->
                                <?php if ($this->storeItem->plateforme): ?>
                                    <div class="game-info-item">
                                        <div class="game-info-label">Plateforme</div>
                                        <div class="game-info-value">
                                            <?= htmlspecialchars($this->storeItem->plateforme) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Âge minimum -->
                                <?php if ($this->storeItem->age_minimum): ?>
                                    <div class="game-info-item">
                                        <div class="game-info-label">Âge recommandé</div>
                                        <div class="game-info-value">
                                            <?= $this->storeItem->age_minimum ?> ans et plus
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Catégorie -->
                                <div class="game-info-item">
                                    <div class="game-info-label">Catégorie</div>
                                    <div class="game-info-value">
                                        <?= ucfirst($this->storeItem->categorie) ?>
                                    </div>
                                </div>

                                <!-- Date d'ajout -->
                                <div class="game-info-item">
                                    <div class="game-info-label">Ajouté le</div>
                                    <div class="game-info-value">
                                        <?= date('d/m/Y', strtotime($this->storeItem->created_at)) ?>
                                    </div>
                                </div>
                            </div>

                            <?php if ($this->storeItem->stock > 0): ?>
                                <div style="display:flex; gap:10px;">
                                    <form method="post" action="?controller=Store&action=addToCart&id=<?= $this->storeItem->id ?>">
                                        <button class="btn-buy-now">Ajouter au panier</button>
                                    </form>
                                    <a href="?controller=Store&action=toggleLike&id=<?= $this->storeItem->id ?>" class="btn-view-game" style="padding: 12px 20px;">
                                        <?php $liked = isset($_SESSION['liked_items']) && isset($_SESSION['liked_items'][$this->storeItem->id]); ?>
                                        <?= $liked ? 'Unlike' : 'Like' ?>
                                    </a>
                                </div>
                            <?php else: ?>
                                <button class="btn-buy-now" disabled>Indisponible</button>
                            <?php endif; ?>

                            <div class="mt-3">
                                <form method="post" action="?controller=Store&action=rateItem&id=<?= $this->storeItem->id ?>" style="display:flex; gap:8px; align-items:center;">
                                    <select name="score" class="form-control" style="max-width:140px;">
                                        <option value="">Note</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                    <button type="submit" class="btn-view-game" style="padding: 8px 14px;">Noter</button>
                                </form>
                            </div>

                            <!-- Badge partenaire -->
                            <div class="partner-badge">
                                <div style="color: var(--muted); font-size: 14px; margin-bottom: 10px;">Fourni par</div>
                                <div style="color: white; font-size: 20px; font-weight: bold;">
                                    <?= htmlspecialchars($this->storeItem->partenaire_nom ?? 'Partenaire') ?>
                                </div>
                                <a href="?controller=Partenaire&action=show&id=<?= $this->storeItem->partenaire_id ?>" 
                                   style="color: var(--accent); font-size: 14px; text-decoration: none; margin-top: 10px; display: inline-block;">
                                    Voir le profil du partenaire
                                    </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description complète -->
                <?php if ($this->storeItem->description): ?>
                    <div class="row mt-5">
                        <div class="col-lg-12">
                            <div class="game-description">
                                <h3 style="color: white; margin-bottom: 20px;">Description</h3>
                                <p style="white-space: pre-line; color: var(--muted);">
                                    <?= nl2br(htmlspecialchars($this->storeItem->description)) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row mt-5">
                    <div class="col-lg-12">
                        <div class="game-detail-card">
                            <h3 style="color: white; margin-bottom: 20px;">Commentaires</h3>
                            <div style="margin-bottom:20px;">
                                <?php if (!empty($comments)): ?>
                                    <?php foreach ($comments as $c): ?>
                                        <div class="related-game-card" style="padding:15px; margin-bottom:10px;">
                                            <div style="font-weight:bold; color:white;"><?= htmlspecialchars($c['author_name']) ?></div>
                                            <div style="color: var(--muted); font-size:14px;"><?= nl2br(htmlspecialchars($c['content'])) ?></div>
                                            <div style="color: var(--muted); font-size:12px; margin-top:6px;"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="no-games"><p class="no-games-text">Aucun commentaire</p></div>
                                <?php endif; ?>
                            </div>
                            <form id="commentForm" method="post" action="?controller=Store&action=addComment&id=<?= $this->storeItem->id ?>" novalidate>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <textarea name="content" class="form-control" rows="2" placeholder="Votre commentaire"></textarea>
                                    </div>
                                </div>
                                <div id="commentErrors" style="color:#dc3545; font-size:14px; margin-bottom:10px;"></div>
                                <button type="submit" class="btn_1">Publier</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Jeux similaires du même partenaire -->
                <?php if (!empty($autresJeux) && count($autresJeux) > 0): ?>
                    <div class="related-games">
                        <div class="row">
                            <div class="col-lg-12 text-center mb-4">
                                <h3 style="color: white; font-size: 32px; font-weight: bold;">Autres jeux de ce partenaire</h3>
                                <p style="color: var(--muted);">
                                    Découvrez d'autres jeux proposés par le même partenaire
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <?php foreach (array_slice($autresJeux, 0, 3) as $jeu): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="related-game-card">
                                        <!-- Image -->
                                        <?php if ($jeu['image']): ?>
                                            <img src="<?php echo BASE_URL . htmlspecialchars($jeu['image']); ?>" 
                                                 alt="<?= htmlspecialchars($jeu['nom']) ?>" 
                                                 class="related-game-img">
                                        <?php else: ?>
                                            <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/gallery/gallery_item_2.png" 
                                                 alt="<?= htmlspecialchars($jeu['nom']) ?>" 
                                                 class="related-game-img">
                                        <?php endif; ?>

                                        <!-- Body -->
                                        <div class="related-game-body">
                                            <h5 style="color: white; font-weight: bold; margin-bottom: 10px;">
                                                <?= htmlspecialchars($jeu['nom']) ?>
                                            </h5>
                                            <p style="color: var(--muted); font-size: 14px; margin-bottom: 15px;">
                                                <?= ucfirst($jeu['categorie']) ?>
                                            </p>
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <span style="color: var(--accent); font-size: 20px; font-weight: bold;">
                                                    <?= number_format($jeu['prix'], 2) ?> DT
                                                </span>
                                                <a href="?controller=Store&action=show&id=<?= $jeu['id'] ?>" 
                                                   class="btn-view-game" style="padding: 8px 15px; font-size: 14px;">
                                                    Voir
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Lien vers tous les jeux du partenaire -->
                        <div class="row mt-4">
                            <div class="col-lg-12 text-center">
                                <a href="?controller=Partenaire&action=show&id=<?= $this->storeItem->partenaire_id ?>" 
                                   class="btn_1">Voir tous les jeux de ce partenaire</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
        <!-- Game Detail Section End -->

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
        // Animation au scroll
        $(document).ready(function() {
            // Animation d'apparition progressive
            $('.game-detail-card').hide().fadeIn(1000);
            
            // Gestion du bouton d'achat
            $('.btn-buy-now').on('click', function() {
                if (!$(this).is(':disabled')) {
                    alert('Fonctionnalité d\'achat à implémenter.\n\nCe jeu : <?= addslashes($this->storeItem->nom) ?>\nPrix : <?= $this->storeItem->prix ?> DT');
                }
            });

            $('#commentForm').on('submit', function(e) {
                var content = $.trim($(this).find('textarea[name="content"]').val());
                var errors = [];
                if (content.length < 5) { errors.push('Commentaire trop court'); }
                if (content.length > 500) { errors.push('Commentaire trop long'); }
                if (/<\s*script/i.test(content)) { errors.push('Contenu invalide'); }
                if (errors.length > 0) {
                    e.preventDefault();
                    $('#commentErrors').text(errors.join(' • '));
                } else {
                    $('#commentErrors').text('');
                }
            });
        });
    </script>
</body>
</html>
