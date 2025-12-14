<?php
require_once __DIR__ . '/../../model/evenementModel.php';
require_once __DIR__ . '/../../model/participationModel.php';
require_once __DIR__ . '/../../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    // fallback demo user
    $_SESSION['user_id'] = 1;
}

$participationModel = new ParticipationModel();
$eventModel = new EvenementModel();

$themeMap = [
    1 => 'Sport',
    2 => 'Éducation',
    3 => 'Esport',
    4 => 'Création',
    5 => 'Prévention',
    6 => 'Coaching',
    7 => 'Compétition'
];

function theme_label($id, $map) {
    return $map[$id] ?? 'Thématique';
}

function normalize_event_image($img) {
    return normalize_asset_path($img);
}

$userId = (int) $_SESSION['user_id'];
$history = $participationModel->getUserParticipations($userId);
?>
<!doctype html>
<?php require_once 'lang/lang_config.php'; ?>
<html lang="<?= get_current_lang() ?>" dir="<?= get_dir() ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mes événements - gaming</title>
    <link rel="icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/event-custom.css">
    <?php if (get_dir() === 'rtl'): ?>
    <style>
        body { text-align: right; direction: rtl; }
        .navbar-nav { margin-right: auto; margin-left: 0 !important; }
        .dropdown-menu { text-align: right; }
        .main_menu .navbar .navbar-nav .nav-item .nav-link { padding: 33px 20px; }
    </style>
    <?php endif; ?>
</head>

<body>
    <div class="body_bg">
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="index.php"> <img src="img/logo.png" alt="logo"> </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>
                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.php"><?= __('home') ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="fighter.html"><?= __('fighter') ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="team.html"><?= __('team') ?></a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown"
                                            role="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <?= __('blog') ?>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="blog.html"><?= __('blog') ?></a>
                                            <a class="dropdown-item" href="single-blog.html">Single blog</a>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown1"
                                            role="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <?= __('pages') ?>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown1">
                                            <a class="dropdown-item" href="elements.html">Elements</a>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="contact.html"><?= __('contact') ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="addreclamation.php"><?= __('reclaim') ?></a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="event.php"><?= __('events') ?></a>
                                    </li>
                                    <li class="nav-item active">
                                        <a class="nav-link" href="my_events.php"><?= __('my_events') ?></a>
                                    </li>
                                    <!-- Language Selector -->
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-globe"></i> <?= strtoupper(get_current_lang()) ?>
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="langDropdown">
                                            <a class="dropdown-item" href="?lang=fr">Français</a>
                                            <a class="dropdown-item" href="?lang=en">English</a>
                                            <a class="dropdown-item" href="?lang=ar">العربية</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <a href="event.php" class="btn_1 d-none d-sm-block"><?= __('all_events') ?></a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <section class="profile-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="banner_text">
                            <div class="banner_text_iner">
                                <h1>Historique de mes événements</h1>
                                <p>Suivez tous vos billets, paiements et participations.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section_padding">
            <div class="container">
                <?php if (empty($history)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="far fa-calendar-times fa-3x mb-3"></i>
                        <h4>Aucune participation enregistrée pour le moment.</h4>
                        <p>Réservez un événement pour le voir apparaître ici.</p>
                        <a href="event.php" class="btn-view-game" style="max-width:260px;margin:0 auto;">Explorer les événements</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($history as $item): 
                            $img = normalize_event_image($item['image'] ?? null);
                            $isPaid = ($item['type_evenement'] === 'payant') && (float)$item['prix'] > 0;
                            $dateLabel = !empty($item['date_evenement']) ? date('d M Y', strtotime($item['date_evenement'])) : 'Date à venir';
                            $timeLabel = !empty($item['heure_evenement']) ? substr($item['heure_evenement'], 0, 5) : '--:--';
                            $statusClass = $item['statut'] === 'acceptée' ? 'badge-success' : ($item['statut'] === 'refusée' ? 'badge-danger' : 'badge-warning');
                            $quantity = isset($item['quantite']) ? max(1, (int)$item['quantite']) : 1;
                            $totalPaid = isset($item['montant_total']) ? number_format((float)$item['montant_total'], 2) . ' TND' : ($isPaid ? number_format($quantity * (float)$item['prix'], 2) . ' TND' : '-');
                            $paymentLabel = $isPaid ? ($item['mode_paiement'] ?? 'Carte') : 'Gratuit';
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="event-card">
                                <div class="event-card-image">
                                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($item['titre']) ?>">
                                    <div class="event-overlay">
                                        <a href="event_details.php?id=<?= $item['id_evenement'] ?>" class="event-btn-details">Voir détails</a>
                                    </div>
                                    <?php if ($isPaid): ?>
                                        <div class="event-price-badge">
                                             <span class="custom-badge-price"><?= number_format($item['prix'], 0) ?> TND</span>
                                        </div>
                                    <?php else: ?>
                                        <div class="event-price-badge">
                                            <span class="custom-badge-free">Gratuit</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="game-card-body">
                                    <h5 class="game-title"><?= htmlspecialchars($item['titre']) ?></h5>
                                    <p class="game-platform">
                                        <i class="far fa-calendar-alt mr-1"></i><?= $dateLabel ?>
                                        &nbsp;•&nbsp;
                                        <i class="far fa-clock mr-1"></i><?= $timeLabel ?>
                                    </p>
                                    <p class="game-description"><?= nl2br(htmlspecialchars(substr($item['description'], 0, 120))) ?>...</p>
                                    <div class="game-info mt-2">
                                        <span><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($item['lieu']) ?></span>
                                        <span><i class="fas fa-stream"></i><?= theme_label($item['id_organisation'], $themeMap) ?></span>
                                        <span class="ml-2" title="Vues"><i class="far fa-eye"></i> <?= $item['vues'] ?? 0 ?></span>
                                    </div>
                                    <div class="game-foot">
                                        <span class="game-price-inline"><?= $quantity ?> billet(s)</span>
                                        <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($item['statut']) ?></span>
                                    </div>
                                    <?php if ($isPaid): ?>
                                        <div class="game-stats mt-2">
                                            <span><i class="fas fa-wallet"></i><?= $totalPaid ?></span>
                                            <span><i class="fas fa-credit-card"></i><?= htmlspecialchars($paymentLabel) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <footer class="footer_part">
            <div class="footer_top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <a href="index.php" class="footer_logo_iner"> <img src="img/logo.png" alt="#"> </a>
                                <p>Heaven fruitful doesn't over lesser days appear creeping seasons so behold bearing days open</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Contact Info</h4>
                                <p>Address : Your address goes here, your demo address. Bangladesh.</p>
                                <p>Phone : +8880 44338899</p>
                                <p>Email : info@colorlib.com</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Important Link</h4>
                                <ul class="list-unstyled">
                                    <li><a href=""> WHMCS-bridge</a></li>
                                    <li><a href="">Search Domain</a></li>
                                    <li><a href="">My Account</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Newsletter</h4>
                                <p>Heaven fruitful doesn't over lesser in days. Appear creeping seasons deve behold bearing days open</p>
                                <div id="mc_embed_signup">
                                    <form target="_blank" action="#" method="get" class="subscribe_form relative mail_part">
                                        <input type="email" name="email" placeholder="Email Address" class="placeholder hide-on-focus">
                                        <button type="submit" class="email_icon newsletter-submit button-contactForm"><i class="far fa-paper-plane"></i></button>
                                        <div class="mt-10 info"></div>
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
                                <P>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a></P>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="footer_icon social_icon">
                                <ul class="list-unstyled">
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fas fa-globe"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="js/jquery-1.12.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.magnific-popup.js"></script>
    <script src="js/swiper.min.js"></script>
    <script src="js/masonry.pkgd.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/slick.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/contact.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/mail-script.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>
<?php
require_once __DIR__ . '/../../model/participationModel.php';
require_once __DIR__ . '/../../model/evenementModel.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$participationModel = new ParticipationModel();

if (!isset($_SESSION['user_id'])) {
    // Fallback demo user
    $_SESSION['user_id'] = 1;
}
$user_id = $_SESSION['user_id'];

$participations = $participationModel->getUserParticipations($user_id);

function theme_label_from_id($id)
{
    $map = [
        1 => 'Sport',
        2 => 'Éducation',
        3 => 'Esport',
        4 => 'Création',
        5 => 'Prévention',
        6 => 'Coaching',
        7 => 'Compétition'
    ];
    return $map[$id] ?? 'Thème';
}
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Mes événements - gaming</title>
    <link rel="icon" href="img/favicon.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/all.css">
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/themify-icons.css">
    <link rel="stylesheet" href="css/magnific-popup.css">
    <link rel="stylesheet" href="css/slick.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/event-custom.css">
</head>

<body>
    <div class="body_bg">
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="index.html"> <img src="img/logo.png" alt="logo"> </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="menu_icon"><i class="fas fa-bars"></i></span>
                            </button>

                            <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <a class="nav-link" href="index.html">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="fighter.html">fighter</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="team.html">team</a>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown"
                                            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Blog
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="blog.html"> blog</a>
                                            <a class="dropdown-item" href="single-blog.html">Single blog</a>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown1"
                                            role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            pages
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown1">
                                            <a class="dropdown-item" href="elements.html">Elements</a>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="contact.html">Contact</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="addreclamation.php">Réclamer</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="event.php">Evénement</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="my_events.php">Mes événements</a>
                                    </li>
                                </ul>
                            </div>
                            <a href="event.php" class="btn_1 d-none d-sm-block">Découvrir des événements</a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <section class="profile-header">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="banner_text">
                            <div class="banner_text_iner">
                                <h1>Mon historique</h1>
                                <p>Suivez toutes vos participations — paiements, statuts et informations clés.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="section_padding">
            <div class="container">
                <?php if (!empty($participations)): ?>
                    <div class="row">
                        <?php foreach ($participations as $p): 
                            $date = !empty($p['date_evenement']) ? date('d M Y', strtotime($p['date_evenement'])) : 'Date à venir';
                            $heure = !empty($p['heure_evenement']) ? substr($p['heure_evenement'], 0, 5) : '--:--';
                            $statusClass = $p['statut'] === 'acceptée' ? 'badge-success' : ($p['statut'] === 'refusée' ? 'badge-danger' : 'badge-warning');
                            $montant = $p['montant_total'] ? number_format($p['montant_total'], 2) . ' TND' : 'Gratuit';
                        ?>
                        <div class="col-lg-6 mb-4">
                            <div class="event-card">
                                <div class="event-card-image" style="height:220px;">
                                    <?php
                                        $img = !empty($p['image']) ? $p['image'] : 'img/favicon.png';
                                        $img = normalize_asset_path($img);
                                    ?>
                                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['titre']) ?>">
                                    <div class="event-overlay">
                                        <a href="event_details.php?id=<?= $p['id_evenement'] ?>" class="event-btn-details">Voir détails</a>
                                    </div>
                                    <div class="event-price-badge" style="bottom: 10px; right: 10px;">
                                        <span class="custom-badge-price"><?= $montant ?></span>
                                    </div>
                                </div>
                                <div class="game-card-body">
                                    <h5 class="game-title"><?= htmlspecialchars($p['titre']) ?></h5>
                                    <p class="game-platform">
                                        <i class="far fa-calendar-alt mr-1"></i><?= $date ?>
                                        &nbsp;•&nbsp;
                                        <i class="far fa-clock mr-1"></i><?= $heure ?>
                                    </p>
                                    <p class="game-description mb-2">
                                        <i class="fas fa-map-marker-alt mr-1"></i><?= htmlspecialchars($p['lieu']) ?>
                                    </p>
                                    <span class="badge <?= $statusClass ?>"><?= $p['statut'] ?></span>
                                    <div class="game-foot mt-3">
                                        <span class="game-price-inline"><?= $p['quantite'] ?? 1 ?> place(s)</span>
                                        <div class="game-stats">
                                            <span><i class="fas fa-tag"></i><?= theme_label_from_id($p['id_organisation'] ?? 0) ?></span>
                                            <span class="ml-3"><i class="far fa-eye"></i> <?= $p['vues'] ?? 0 ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-games">
                        <i class="far fa-calendar-times"></i>
                        <div class="no-games-title">Aucune participation pour le moment</div>
                        <div class="no-games-text">Inscrivez-vous à un événement pour voir votre historique ici.</div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <footer class="footer_part">
            <div class="footer_top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <a href="index.html" class="footer_logo_iner"> <img src="img/logo.png" alt="#"> </a>
                                <p>Heaven fruitful doesn't over lesser days appear creeping seasons so behold bearing days open</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Contact Info</h4>
                                <p>Address : Your address goes here, your demo address. Bangladesh.</p>
                                <p>Phone : +8880 44338899</p>
                                <p>Email : info@colorlib.com</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Important Link</h4>
                                <ul class="list-unstyled">
                                    <li><a href=""> WHMCS-bridge</a></li>
                                    <li><a href="">Search Domain</a></li>
                                    <li><a href="">My Account</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4>Newsletter</h4>
                                <p>Heaven fruitful doesn't over lesser in days. Appear creeping seasons deve behold bearing days open</p>
                                <div id="mc_embed_signup">
                                    <form target="_blank" action="#" method="get" class="subscribe_form relative mail_part">
                                        <input type="email" name="email" placeholder="Email Address" class="placeholder hide-on-focus">
                                        <button type="submit" class="email_icon newsletter-submit button-contactForm"><i class="far fa-paper-plane"></i></button>
                                        <div class="mt-10 info"></div>
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
                                <P>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a></P>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="footer_icon social_icon">
                                <ul class="list-unstyled">
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fas fa-globe"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <script src="js/jquery-1.12.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.magnific-popup.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>

