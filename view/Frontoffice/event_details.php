<?php
require_once __DIR__ . '/../../model/evenementModel.php';
require_once __DIR__ . '/../../model/participationModel.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$eventModel = new EvenementModel();
$participationModel = new ParticipationModel();

$message = '';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($event_id <= 0) {
    $message = 'Invalid event ID.';
} else {
    $event = $eventModel->getById($event_id);
    if (!$event) {
        $message = 'Event not found.';
    }
}

// Handle participation POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['participate']) && isset($event) && $event) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['user_id'] = 1; // simulate user
    }
    $user_id = $_SESSION['user_id'];

    if ($participationModel->isUserRegistered($user_id, $event_id)) {
        $message = 'You are already registered for this event.';
    } else {
        $created = $participationModel->create($event_id, $user_id, date('Y-m-d'), 'en attente');
        if ($created) {
            $message = 'Your participation was recorded and is pending approval.';
        } else {
            $message = 'Could not register for the event (maybe already registered).';
        }
    }

    // Refresh participants/isRegistered info after POST
    $isRegistered = $participationModel->isUserRegistered($user_id, $event_id);
    $participants = $participationModel->getEventParticipants($event_id);
} else {
    // initial page load
    if (isset($event)) {
        $participants = $participationModel->getEventParticipants($event_id);
        $isRegistered = isset($_SESSION['user_id']) ? $participationModel->isUserRegistered($_SESSION['user_id'], $event_id) : false;
    }
}
?>
<!doctype html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo isset($event) ? htmlspecialchars($event['titre']) : 'Event'; ?> - gaming</title>
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
</head>

<body>
    <div class="body_bg">
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="index.html"> <img src="img/logo.png" alt="logo"> </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse"
                                data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
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
                                            role="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            Blog
                                        </a>
                                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                            <a class="dropdown-item" href="blog.html"> blog</a>
                                            <a class="dropdown-item" href="single-blog.html">Single blog</a>
                                        </div>
                                    </li>
                                    <li class="nav-item dropdown">
                                        <a class="nav-link dropdown-toggle" href="blog.html" id="navbarDropdown1"
                                            role="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
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
                                </ul>
                            </div>
                            <a href="#" class="btn_1 d-none d-sm-block">Install Now</a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <section class="banner_part">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-lg-8">
                        <div class="banner_text">
                            <div class="banner_text_iner">
                                <h1><?php echo isset($event) ? htmlspecialchars($event['titre']) : 'Event'; ?></h1>
                                <p><?php echo isset($event) ? htmlspecialchars($event['lieu']) : ''; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about_us section_padding">
            <div class="container">
                <?php if ($message): ?>
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($event) && $event): ?>
                <div class="row">
                    <div class="col-md-6">
                        <?php $img = !empty($event['image']) ? $event['image'] : 'img/favicon.png'; ?>
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($event['titre']); ?>" class="img-fluid" style="max-height:400px; object-fit:cover;">
                    </div>
                    <div class="col-md-6">
                        <h2><?php echo htmlspecialchars($event['titre']); ?></h2>
                        <p class="text-muted"><?php echo !empty($event['date_evenement']) ? date('F j, Y', strtotime($event['date_evenement'])) : ''; ?> — <?php echo htmlspecialchars($event['lieu']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>

                        <?php if (isset($isRegistered) && $isRegistered): ?>
                            <div class="alert alert-success">You are registered for this event.</div>
                        <?php else: ?>
                            <form method="post" class="mb-3">
                                <button type="submit" name="participate" class="btn_1">Participate</button>
                            </form>
                        <?php endif; ?>

                        <div>
                            <strong>Participants (accepted):</strong>
                            <ul class="list-unstyled mt-2">
                                <?php if (!empty($participants)): ?>
                                    <?php foreach ($participants as $p): ?>
                                        <li><?php echo htmlspecialchars($p['prenom'] . ' ' . $p['nom']); ?> <?php if (!empty($p['gamer_tag'])) echo '(' . htmlspecialchars($p['gamer_tag']) . ')'; ?></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>No participants yet.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="single_war_text text-center">
                                <h4><?php echo htmlspecialchars($message ?: 'Event not found'); ?></h4>
                            </div>
                        </div>
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
        <!--::footer_part end::-->
    </div>


    <!-- jquery plugins here-->
    <script src="js/jquery-1.12.1.min.js"></script>
    <!-- popper js -->
    <script src="js/popper.min.js"></script>
    <!-- bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    <!-- easing js -->
    <script src="js/jquery.magnific-popup.js"></script>
    <!-- swiper js -->
    <script src="js/swiper.min.js"></script>
    <!-- swiper js -->
    <script src="js/masonry.pkgd.js"></script>
    <!-- particles js -->
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <!-- slick js -->
    <script src="js/slick.min.js"></script>
    <script src="js/jquery.counterup.min.js"></script>
    <script src="js/waypoints.min.js"></script>
    <script src="js/contact.js"></script>
    <script src="js/jquery.ajaxchimp.min.js"></script>
    <script src="js/jquery.form.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/mail-script.js"></script>
    <!-- custom js -->
    <script src="js/custom.js"></script>
</body>

</html>
