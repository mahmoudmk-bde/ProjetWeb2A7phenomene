<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ENGAGE – Volontariat par le jeu vidéo</title>

    <!-- Favicon -->
    <link rel="icon" href="../img/logo.png">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
</head>

<body>

<!-- WRAPPER -->
<div class="body_bg">

    <!-- HEADER PUBLIC -->
    <header class="main_menu single_page_menu">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">

                <!-- LOGO -->
                <a class="navbar-brand" href="index.php">
                    <img src="../img/client_logo/logo.png" alt="logo" style="height:45px;">
                </a>

                <!-- TOGGLER (mobile menu) -->
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                    data-target="#navbarSupportedContent">
                    <span class="menu_icon"><i class="fas fa-bars"></i></span>
                </button>

                <!-- NAVIGATION -->
                <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="missionlist.php">Missions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Gamification</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Réclamations</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Événements</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Quizzes</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <!-- BOUTONS CONNEXION/INSCRIPTION (visible seulement si non connecté) -->
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <div class="d-none d-sm-block">
                        <a href="connexion.php" class="btn_1">Se connecter</a>
                        <a href="inscription.php" class="btn_1" style="margin-left: 10px;">S'inscrire</a>
                    </div>
                <?php else: ?>
                    <!-- Si connecté, lien vers l'espace membre -->
                    <a href="index1.php" class="btn_1 d-none d-sm-block">Mon Espace</a>
                <?php endif; ?>

            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="banner_part">
        <div class="container">
            <div class="row align-items-center justify-content-between">

                <div class="col-lg-6 col-md-8">
                    <div class="banner_text">
                        <div class="banner_text_iner">
                            <h1>ENGAGE</h1>
                            <h3>Le volontariat par le jeu vidéo</h3>

                            <p>
                                Transformez votre passion pour les jeux vidéo en missions solidaires.
                                Les associations postent des missions, les gamers les accomplissent.
                            </p>

                            <a href="missionlist.php" class="btn_1 mt-3">Voir les missions</a>
                            
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <a href="inscription.php" class="btn_1 mt-3" style="margin-left: 10px;">Créer un compte</a>
                            <?php else: ?>
                                <a href="index1.php" class="btn_1 mt-3" style="margin-left: 10px;">Mon Espace</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <img src="../img/ph1.png" alt="gaming" class="img-fluid rounded shadow">
                </div>

            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="about_part section_padding">
        <div class="container">

            <div class="row align-items-center justify-content-between">

                <div class="col-lg-5">
                    <img src="../img/ph2.png" alt="about" class="img-fluid rounded shadow">
                </div>

                <div class="col-lg-6">
                    <div class="section_title">
                        <h2>Pourquoi ENGAGE ?</h2>
                    </div>

                    <p>
                        ENGAGE connecte les joueurs passionnés et les organisations qui ont besoin
                        d'un soutien ludique : mentorat, coaching, rééducation, sensibilisation,
                        tout ça via le jeu vidéo !
                    </p>

                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="inscription.php" class="btn_1 mt-4">Commencer maintenant</a>
                    <?php else: ?>
                        <a href="missionlist.php" class="btn_1 mt-4">Voir les missions</a>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- FEATURES / SERVICES SECTION -->
    <section class="feature_part section_padding">
        <div class="container">

            <div class="section_title text-center">
                <h2>Ce que vous pouvez faire avec ENGAGE</h2>
            </div>

            <div class="row mt-5">

                <div class="col-lg-4 col-sm-6">
                    <div class="single_feature">
                        <i class="fas fa-gamepad fa-3x mb-3" style="color:#ff0066;"></i>
                        <h4>Coaching Gaming</h4>
                        <p>Aidez un enfant ou un ado à progresser dans son jeu préféré.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6">
                    <div class="single_feature">
                        <i class="fas fa-hands-helping fa-3x mb-3" style="color:#ff0066;"></i>
                        <h4>Soutien associatif</h4>
                        <p>Accomplissez des missions pour des écoles, hôpitaux ou associations.</p>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6">
                    <div class="single_feature">
                        <i class="fas fa-chart-line fa-3x mb-3" style="color:#ff0066;"></i>
                        <h4>Gagnez des badges</h4>
                        <p>Un système de gamification unique pour booster votre engagement.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- STATISTIQUES -->
    <section class="counter_part" style="background: #1f2235; padding: 80px 0;">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="single_counter text-center">
                        <h3 class="counter">150</h3>
                        <p>Missions accomplies</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single_counter text-center">
                        <h3 class="counter">500</h3>
                        <p>Volontaires actifs</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single_counter text-center">
                        <h3 class="counter">50</h3>
                        <p>Associations partenaires</p>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="single_counter text-center">
                        <h3 class="counter">1000</h3>
                        <p>Heures de bénévolat</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer-area">
        <div class="container text-center text-white">
            <p style="margin:0; padding:20px 0;">
                © 2025 ENGAGE Platform – Développé par les phenomenes
            </p>
        </div>
    </footer>

</div>

<!-- JS -->
<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/owl.carousel.min.js"></script>
<script src="assets/js/jquery.magnific-popup.js"></script>
<script src="assets/js/slick.min.js"></script>
<script src="assets/js/custom.js"></script>

</body>
</html>