<?php
// $items contains wishlist items
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Ma Liste d'Envies - Engage Store</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
    <style>
        /* Wishlist Specific Styles (similar to Cart) */
        .wishlist-item-card {
            background: var(--panel);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s ease;
        }
        .wishlist-item-card:hover {
            transform: translateY(-2px);
            border-color: rgba(255, 74, 87, 0.3);
        }
        .wishlist-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 12px;
        }
        .wishlist-details {
            flex: 1;
        }
        .wishlist-title {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .wishlist-price {
            color: var(--accent);
            font-weight: 700;
            font-size: 1.1rem;
        }
        .btn-remove {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.1);
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-remove:hover {
            background: #dc3545;
            color: #fff;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .wishlist-item-card {
                flex-direction: column;
                text-align: center;
            }
            .wishlist-image {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="body_bg">
        <!-- Header -->
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
                                <img src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/logo.png" alt="logo">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>

                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav ml-auto">
                                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>view/frontoffice/index.php">Accueil</a></li>
                                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>view/frontoffice/missionlist.php">Missions</a></li>
                                    <li class="nav-item active"><a class="nav-link" href="?controller=Store&action=index">Store</a></li>
                                    <li class="nav-item"><a class="nav-link" href="?controller=Partenaire&action=index">Partenaires</a></li>
                                </ul>
                            </div>
                            <div style="display:flex; gap:10px; align-items:center;">
                                <a href="?controller=Store&action=cart" class="btn_1 btn-cart d-none d-sm-block"
                                    aria-label="Panier">
                                    (<?php $cnt = 0;
                                    if (isset($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $q) {
                                            $cnt += (int) $q;
                                        }
                                    }
                                    echo $cnt; ?>)
                                </a>
                                <a href="?controller=Store&action=wishlist" class="btn_1 btn-like d-none d-sm-block"
                                    aria-label="Liste d'envies"></a>
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="<?php echo BASE_URL; ?>view/frontoffice/index1.php"
                                        class="btn_1 d-none d-sm-block">Mon Espace</a>
                                <?php else: ?>
                                    <a href="<?php echo BASE_URL; ?>view/frontoffice/connexion.php"
                                        class="btn_1 d-none d-sm-block">Se connecter</a>
                                <?php endif; ?>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <!-- Breadcrumb/Hero -->
        <section class="breadcrumb breadcrumb_bg">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="breadcrumb_iner text-center">
                            <div class="breadcrumb_iner_item">
                                <h2>Ma Liste d'Envies</h2>
                                <p>Retrouvez tous vos jeux favoris</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Wishlist Section -->
        <section class="section_padding">
            <div class="container">
                <?php if (empty($items)): ?>
                    <div class="no-games text-center">
                        <i class="fas fa-heart" style="font-size: 80px; color: #6c757d; margin-bottom: 20px;"></i>
                        <h3 class="no-games-title">Votre liste d'envies est vide</h3>
                        <p class="no-games-text mb-4">Explorez le store et ajoutez des jeux à votre liste !</p>
                        <a href="?controller=Store&action=index" class="btn_1">Retour au Store</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-lg-10 offset-lg-1">
                            <?php foreach ($items as $it): ?>
                                <div class="wishlist-item-card">
                                    <?php if ($it['image']): ?>
                                        <img class="wishlist-image" src="<?php echo BASE_URL . htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['nom']); ?>">
                                    <?php else: ?>
                                        <img class="wishlist-image" src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/gallery/gallery_item_1.png" alt="Default">
                                    <?php endif; ?>
                                    
                                    <div class="wishlist-details">
                                        <h5 class="wishlist-title"><?php echo htmlspecialchars($it['nom']); ?></h5>
                                        <div class="text-muted mb-2">
                                            <?php if ($it['stock'] > 0): ?>
                                                <span class="text-success">En stock</span>
                                            <?php else: ?>
                                                <span class="text-danger">Rupture de stock</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="wishlist-price"><?php echo number_format($it['prix'], 2); ?> DT</div>
                                    </div>

                                    <div class="d-flex flex-column align-items-end gap-2">
                                        <?php if ($it['stock'] > 0): ?>
                                            <form method="post" action="?controller=Store&action=addToCart&id=<?= $it['id'] ?>">
                                                <button type="submit" class="btn_1 btn-sm">Ajouter au panier</button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="?controller=Store&action=toggleWishlist&id=<?php echo (int)$it['id']; ?>" 
                                           class="btn-remove">
                                            <i class="fas fa-trash"></i> Retirer
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="text-center mt-4">
                                <a href="?controller=Store&action=index" class="btn btn-outline-light">
                                    <i class="fas fa-arrow-left"></i> Continuer vos achats
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- Footer -->
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
                                <p>Email : contact@engage.tn</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Liens Importants</h4>
                                <ul class="list-unstyled">
                                    <li><a href="?controller=Store&action=index">Store</a></li>
                                    <li><a href="?controller=Partenaire&action=index">Partenaires</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Newsletter</h4>
                                <div id="mc_embed_signup">
                                    <form action="#" method="get" class="subscribe_form relative mail_part">
                                        <input type="email" name="email" placeholder="Email Address" class="placeholder hide-on-focus">
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
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery-1.12.1.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/popper.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/jquery.magnific-popup.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/owl.carousel.min.js"></script>
    <script src="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/js/custom.js"></script>
</body>
</html>
