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
// Handle guest participation POST (form with name/email)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'guest_participate' && isset($event) && $event) {
    require_once __DIR__ . '/../../config.php';
    $prenom = isset($_POST['prenom']) ? secure_data($_POST['prenom']) : '';
    $nom = isset($_POST['nom']) ? secure_data($_POST['nom']) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';

    // Basic server-side validation
    if (empty($prenom) || empty($nom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please provide a valid first name, last name and email.';
    } else {
        // Find or create user by email
        $db = (new Database())->getConnection();
        try {
            $stmt = $db->prepare('SELECT id_utilisateur FROM utilisateur WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $row = $stmt->fetch();
            if ($row && isset($row['id_utilisateur'])) {
                $user_id = (int)$row['id_utilisateur'];
            } else {
                $ins = $db->prepare('INSERT INTO utilisateur (nom, prenom, email) VALUES (:nom, :prenom, :email)');
                $ins->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email]);
                $user_id = (int)$db->lastInsertId();
            }

            // Check registration and create participation
            if ($participationModel->isUserRegistered($user_id, $event_id)) {
                $message = 'You are already registered for this event.';
            } else {
                $created = $participationModel->create($event_id, $user_id, date('Y-m-d'), 'en attente');
                if ($created) {
                    $message = 'Your participation was recorded and is pending approval.';
                    // Optionally set session user to help UX
                    $_SESSION['user_id'] = $user_id;
                } else {
                    $message = 'Could not register for the event (maybe already registered).';
                }
            }
        } catch (Exception $e) {
            $message = 'An error occurred while registering: ' . $e->getMessage();
        }
    }

    // Refresh participants/isRegistered info after POST (guest flow)
    $isRegistered = isset($user_id) ? $participationModel->isUserRegistered($user_id, $event_id) : false;
    $participants = $participationModel->getEventParticipants($event_id);

} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['participate']) && isset($event) && $event) {
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

        <section class="about_us section_padding event-page">
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
                    <?php
                        function normalize_event_image($img) {
                            $default = 'img/favicon.png';
                            if (empty($img)) return $default;
                            $img = trim($img);
                            if (stripos($img, 'http://') === 0 || stripos($img, 'https://') === 0) return $img;
                            if (strpos($img, '/gamingroom/uploads/events/') === 0) return $img;
                            if (strpos($img, '/uploads/events/') === 0) return '/gamingroom' . $img;
                            if (strpos($img, 'uploads/events/') === 0) return '/gamingroom/' . $img;
                            if (strpos($img, '/') === 0) return $img;
                            return $img;
                        }
                        $img = normalize_event_image(!empty($event['image']) ? $event['image'] : null);
                    ?>
                    <div class="col-md-6">
                        <div class="event-hero">
                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($event['titre']) ?>" class="event-hero-img">
                            <div class="event-hero-overlay">
                                <div class="event-hero-text">
                                    <h1><?= htmlspecialchars($event['titre']) ?></h1>
                                    <p class="text-light small"><?= !empty($event['date_evenement']) ? date('F j, Y', strtotime($event['date_evenement'])) : ''; ?> • <?= htmlspecialchars($event['lieu']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="event-details card event-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h3 class="mb-1"><?= htmlspecialchars($event['titre']); ?></h3>
                                        <p class="text-muted mb-0 small"><?= !empty($event['date_evenement']) ? date('F j, Y', strtotime($event['date_evenement'])) : ''; ?> — <?= htmlspecialchars($event['lieu']); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <?php $accepted_count = (int) $eventModel->countParticipants($event_id); ?>
                                        <div class="participants-big"><span class="participants-badge"><?= $accepted_count ?></span><div class="small text-muted">accepted</div></div>
                                    </div>
                                </div>
                                <?php if (isset($isRegistered) && $isRegistered): ?>
                                    <div class="alert alert-success">You are registered for this event.</div>
                                <?php else: ?>
                                    <div id="participant-errors"></div>
                                    <div class="mb-3">
                                        <button type="button" class="btn_1 register-btn" data-toggle="modal" data-target="#participateModal" style="font-size:1.05rem; padding:12px 24px;">Participate</button>
                                        <small class="text-muted d-block mt-2">You will receive a confirmation email after approval.</small>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3 description-text" style="margin-top:6px;"><?= nl2br(htmlspecialchars($event['description'])); ?></div>

                                <div class="mt-3">
                                    <strong>Participants (accepted):</strong>
                                    <?php if (!empty($participants)): ?>
                                        <div class="list-group list-group-flush mt-2">
                                            <?php foreach ($participants as $p): ?>
                                            <div class="list-group-item bg-transparent text-light px-0 py-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="font-weight-bold"><?php echo htmlspecialchars($p['prenom'] . ' ' . $p['nom']); ?></div>
                                                        <div class="small text-muted"><?php echo htmlspecialchars($p['email']); ?></div>
                                                    </div>
                                                    <div class="small text-muted"><?php echo !empty($p['gamer_tag']) ? htmlspecialchars($p['gamer_tag']) : '-'; ?></div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-2 text-muted">Aucun participant pour le moment.</div>
                                    <?php endif; ?>
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
    <script src="js/participant_validate.js"></script>
    <!-- custom js -->
    <script src="js/custom.js"></script>
        <!-- Participation Modal -->
        <div class="modal fade" id="participateModal" tabindex="-1" role="dialog" aria-labelledby="participateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="participateModalLabel">Participate to <?= htmlspecialchars($event['titre'] ?? 'this event') ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" id="participant-modal-form" novalidate>
                            <input type="hidden" name="action" value="guest_participate">
                            <div id="participant-errors-modal"></div>
                            <div class="form-group">
                                <label for="modal_prenom">Prénom</label>
                                <input type="text" name="prenom" id="modal_prenom" class="form-control" placeholder="Prénom" style="font-size:1.05rem;">
                            </div>
                            <div class="form-group">
                                <label for="modal_nom">Nom</label>
                                <input type="text" name="nom" id="modal_nom" class="form-control" placeholder="Nom" style="font-size:1.05rem;">
                            </div>
                            <div class="form-group">
                                <label for="modal_email">Email</label>
                                <input type="email" name="email" id="modal_email" class="form-control" placeholder="email@example.com" style="font-size:1.05rem;">
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn_1 register-btn">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>
