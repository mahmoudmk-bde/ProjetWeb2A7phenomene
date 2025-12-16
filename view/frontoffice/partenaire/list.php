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
</head>
<style>
    .partner-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        border-radius: 16px;
        overflow: hidden;
    }

    .partner-logo-container {
        height: 240px;
        aspect-ratio: 16/9;
        background: var(--card-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 16px;
    }

    .partner-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .partner-logo-container img {
        width: 100%;
        height: 100%;
        object-fit: contain !important;
    }

    .partner-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .partner-name {
        color: #fff;
        font-weight: 700;
        margin-bottom: 10px;
        line-height: 1.3;
    }

    .partner-type {
        color: var(--accent);
        margin-bottom: 12px;
        display: inline-block;
    }

    .partner-description {
        color: var(--muted);
        font-size: 13px;
        line-height: 1.5;
        flex: 1;
    }

    .btn-view-partner {
        margin-top: auto;
    }

    @media (min-width: 992px) {
        .partner-item {
            display: flex;
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

<body>
    <div class="body_bg">
        <!--::header part start::-->
        <?php include 'header_common.php'; ?>
        <!-- Header part end-->

        <!-- breadcrumb start-->
        <section class="breadcrumb_bg">
            <div class="container">
                <div class="breadcrumb_iner_item text-center">
                    <h1 class="design-title">🤝 Nos Partenaires</h1>
                    <p class="design-subtitle">Les créateurs de vos jeux préférés</p>
                    <div class="stats-panel" style="margin-top: 30px;">
                        <span><i class="fas fa-handshake"></i> <strong><?= count($partenaires) ?></strong> partenaires
                            actifs</span>
                        <span><i class="fas fa-users"></i> Communauté Engagée</span>
                        <span><i class="fas fa-trophy"></i> Excellence garantie</span>
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
                                <a class="filter-btn" href="?controller=Partenaire&action=index&type=sponsor"
                                    data-filter="sponsor">
                                    <i class="fas fa-dollar-sign"></i> Sponsors
                                </a>
                                <a class="filter-btn" href="?controller=Partenaire&action=index&type=testeur"
                                    data-filter="testeur">
                                    <i class="fas fa-flask"></i> Testeurs
                                </a>
                                <a class="filter-btn" href="?controller=Partenaire&action=index&type=vendeur"
                                    data-filter="vendeur">
                                    <i class="fas fa-store"></i> Vendeurs
                                </a>
                            </div>
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
                            <div class="col-lg-4 col-md-6 partner-item"
                                data-type="<?= strtolower(trim($partenaire['type'])) ?>">
                                <div class="partner-card">
                                    <!-- Logo du partenaire -->
                                    <div class="partner-logo-container">
                                        <?php if ($partenaire['logo']): ?>
                                            <img src="<?php echo BASE_URL . htmlspecialchars($partenaire['logo']); ?>"
                                                alt="<?= htmlspecialchars($partenaire['nom']) ?>" class="partner-logo">
                                        <?php else: ?>
                                            <div
                                                style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #666;">
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

                                        <a href="?controller=Partenaire&action=show&id=<?= $partenaire['id'] ?>"
                                            class="btn-view-partner">
                                            <i class="fas fa-eye"></i> Voir le profil complet
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($totalPages) && $totalPages > 1): ?>
                        <div class="row mt-5">
                            <div class="col-lg-12">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination justify-content-center">
                                        <?php
                                        $queryParams = $_GET;
                                        // helper function (if not already defined) or just inline logic
                                        // Since we can't function define inside loop, we'll just inline it or define it once safely.
                                        // To be safe, just inline usage.
                                        ?>

                                        <!-- Previous Button -->
                                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                            <?php $queryParams['page'] = max(1, $page - 1); ?>
                                            <a class="page-link" href="?<?= http_build_query($queryParams) ?>"
                                                aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>

                                        <!-- Page Numbers -->
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                <?php $queryParams['page'] = $i; ?>
                                                <a class="page-link" href="?<?= http_build_query($queryParams) ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- Next Button -->
                                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                            <?php $queryParams['page'] = min($totalPages, $page + 1); ?>
                                            <a class="page-link" href="?<?= http_build_query($queryParams) ?>"
                                                aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
        <!-- Partners Section End -->

        <!--::footer_part start::-->

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
            var initial = (new URLSearchParams(window.location.search).get('type') || 'all').toLowerCase();
            $('.filter-btn').removeClass('active');
            $('.filter-btn[data-filter="' + initial + '"]').addClass('active');
        });
    </script>
</body>

</html>