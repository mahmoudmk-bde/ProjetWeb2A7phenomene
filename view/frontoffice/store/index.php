<?php
$isWishlist = isset($isWishlist) ? (bool) $isWishlist : false;
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gaming Store - Engage</title>
    <link rel="icon" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/img/favicon.png">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/bootstrap.min.css">
    <!-- animate CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/animate.css">
    <!-- owl carousel CSS -->
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/owl.carousel.min.css">
    <!-- font awesome CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/all.css">
    <!-- flaticon CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/flaticon.css">
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/themify-icons.css">
    <!-- magnific popup CSS -->
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/magnific-popup.css">
    <!-- style CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/css/style.css">
    <!-- Custom Frontoffice CSS -->
    <link rel="stylesheet"
        href="<?php echo BASE_URL; ?>view/frontoffice/storepartenaireassets/css/custom-frontoffice.css">
    <style>
        /* Styles locaux au Store pour uniformiser les cartes sans toucher au CSS global */
        .store-card {
            display: flex;
            flex-direction: column;
            border-radius: 16px;
            background: rgba(20, 20, 24, 0.6);
            overflow: hidden;
        }

        .store-card .game-card-img {
            position: relative;
            height: 280px;
            overflow: hidden;
            background: #0f0f12;
        }

        .store-card .game-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .store-card .game-card-body {
            display: flex;
            flex-direction: column;
            padding: 18px;
            gap: 10px;
        }

        .store-card .game-title {
            color: #fff;
            margin: 0;
            font-weight: 700;
            letter-spacing: .2px;
        }

        .store-card .game-partner,
        .store-card .game-platform {
            color: #9aa0a6;
            font-size: 14px;
        }

        .store-card .game-info {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .store-card .game-foot {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 8px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .store-card .game-price-inline {
            color: #fff;
            font-weight: 700;
        }

        .store-card .game-stats {
            color: #9aa0a6;
            font-size: 13px;
            display: flex;
            gap: 12px;
        }

        .store-card .btn-view-game {
            display: block;
            width: 100%;
            text-align: center;
        }

        .store-card .game-badge {
            position: absolute;
            right: 12px;
            top: 12px;
            background: #ff4d6d;
            color: #fff;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .store-card .stock-badge {
            position: absolute;
            left: 12px;
            top: 12px;
            background: #28a745;
            color: #fff;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .store-card .stock-badge.stock-low {
            background: #ffb200;
            color: #1b1b1b;
        }

        .store-card .stock-badge.stock-out {
            background: #dc3545;
        }

        /* Harmoniser les hauteurs des colonnes en MD/LG */
        #games-container .game-item {
            margin-bottom: 24px;
        }

        @media (min-width: 992px) {
            #games-container .game-item {
                display: flex;
            }

            #games-container .store-card {
                width: 100%;
            }
        }



        /* Pagination Styling */
        .pagination {
            margin-top: 40px;
        }

        .page-item .page-link {
            background-color: var(--panel);
            border-color: rgba(255, 255, 255, 0.1);
            color: var(--muted);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .page-item.active .page-link {
            background-color: var(--accent);
            border-color: var(--accent);
            color: #fff;
            box-shadow: 0 4px 10px rgba(255, 74, 87, 0.3);
        }

        .page-item .page-link:hover {
            background-color: var(--accent-light);
            border-color: var(--accent-light);
            color: #fff;
            transform: translateY(-2px);
        }

        .page-item.disabled .page-link {
            background-color: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.05);
            color: rgba(255, 255, 255, 0.2);
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="body_bg">

        <?php include 'header_common1.php'; ?>
        <!-- breadcrumb start-->
        <section class="breadcrumb_bg">
            <div class="container">
                <div class="breadcrumb_iner_item text-center">
                    <?php if ($isWishlist): ?>
                        <h1 class="design-title">❤️ Ma Liste d'Envies</h1>
                        <p class="design-subtitle">Vos coups de cœur gaming</p>
                        <div class="stats-panel" style="margin-top: 30px;">
                            <span><i class="fas fa-gamepad"></i> <strong><?= count($items) ?></strong> Jeux
                                enregistrés</span>
                        </div>
                    <?php else: ?>
                        <h1 class="design-title">🎮 Le Store</h1>
                        <p class="design-subtitle">Découvrez nos jeux vidéo exclusifs</p>
                        <div class="stats-panel" style="margin-top: 30px;">
                            <span><i class="fas fa-gamepad"></i> <strong><?= count($items) ?></strong> jeux
                                disponibles</span>
                            <span><i class="fas fa-handshake"></i>
                                <strong><?= count(array_unique(array_column($items, 'partenaire_id'))) ?></strong>
                                partenaires</span>
                            <span><i class="fas fa-star"></i> <strong>Top qualité</strong></span>
                        </div>
                    <?php endif; ?>
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
                            <?php $isWishlist = isset($isWishlist) ? (bool) $isWishlist : false; ?>
                            <?php if (!$isWishlist): ?>
                                <h4 class="filter-title">Filtrer par catégorie</h4>
                                <form method="get" action=""
                                    style="max-width:880px; margin:0 auto 15px auto; display:flex; gap:10px; align-items:center;">
                                    <input type="hidden" name="controller" value="Store">
                                    <input type="hidden" name="action" value="index">
                                    <input type="text" name="q"
                                        value="<?= htmlspecialchars(isset($_GET['q']) ? $_GET['q'] : '') ?>"
                                        class="form-control" placeholder="Rechercher par nom, plateforme..."
                                        style="flex:1;">
                                    <input type="text" name="partenaire"
                                        value="<?= htmlspecialchars(isset($_GET['partenaire']) ? $_GET['partenaire'] : '') ?>"
                                        class="form-control" placeholder="Marque/Partenaire" style="flex:0.6;">
                                    <button class="btn_1" type="submit" style="white-space:nowrap;">Chercher</button>
                                </form>
                                <div class="filter-buttons">
                                    <a class="filter-btn" href="?controller=Store&action=index" data-filter="all">
                                        <i class="fas fa-th"></i> Tous les jeux
                                    </a>
                                    <a class="filter-btn" href="?controller=Store&action=index&categorie=action"
                                        data-filter="action">Action</a>
                                    <a class="filter-btn" href="?controller=Store&action=index&categorie=aventure"
                                        data-filter="aventure">Aventure</a>
                                    <a class="filter-btn" href="?controller=Store&action=index&categorie=sport"
                                        data-filter="sport">Sport</a>
                                    <a class="filter-btn" href="?controller=Store&action=index&categorie=strategie"
                                        data-filter="strategie">Stratégie</a>
                                    <a class="filter-btn" href="?controller=Store&action=index&categorie=simulation"
                                        data-filter="simulation">Simulation</a>
                                    <a class="filter-btn" href="?controller=Store&action=index&categorie=rpg"
                                        data-filter="rpg">RPG</a>
                                    <a class="filter-btn" href="?controller=Store&action=index&categorie=educatif"
                                        data-filter="educatif">Éducatif</a>

                                    <!-- AI Recommendations Link -->
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <a class="filter-btn" href="?controller=Store&action=recommendations"
                                            style="background: linear-gradient(45deg, var(--secondary), var(--accent)); color:white; border:none;">
                                            <i class="fas fa-magic"></i> Pour Vous
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <h4 class="filter-title">Ma liste d'envies</h4>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="row mb-4">
                    <div class="col-lg-12">
                        <div class="stats-panel">
                            <span><strong><?= count($items) ?></strong> jeux disponibles</span>
                            <span><strong><?= count(array_unique(array_column($items, 'partenaire_id'))) ?></strong>
                                partenaires</span>
                            <span><strong>Top qualité</strong></span>
                        </div>
                    </div>
                </div>

                <!-- Liste des jeux -->
                <div id="ajax-games-container">
                    <?php include __DIR__ . '/items-grid.php'; ?>
                </div>
            </div>
        </section>
        <!-- Store Section End -->

        <!--::footer_part start::-->

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
        $(function () {
            // Highlight active filter
            var c = (new URLSearchParams(window.location.search).get('categorie') || 'all').toLowerCase();
            $('.filter-btn').removeClass('active');
            $('.filter-btn[data-filter="' + c + '"]').addClass('active');

            // Ajax Search & Filter
            function loadGames(url) {
                // Add ajax param
                if (url.indexOf('?') === -1) url += '?';
                else url += '&';
                url += 'ajax=1';

                // Show loading (optional)
                $('#ajax-games-container').css('opacity', '0.5');

                $.get(url, function (data) {
                    $('#ajax-games-container').html(data).css('opacity', '1');
                    // Update URL bar
                    var cleanUrl = url.replace('&ajax=1', '').replace('?ajax=1', '');
                    window.history.pushState(null, '', cleanUrl);
                });
            }

            // Filter Buttons
            $('.filter-btn').on('click', function (e) {
                e.preventDefault();
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                loadGames($(this).attr('href'));
            });

            // Search Form
            $('form[action=""]').on('submit', function (e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var url = '?' + formData;
                loadGames(url);
            });

            // Pagination Links (Delegated event for dynamic content)
            $(document).on('click', '.page-link', function (e) {
                e.preventDefault();
                loadGames($(this).attr('href'));
            });
        });
    </script>
</body>

</html>