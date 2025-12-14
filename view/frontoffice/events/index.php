<?php require_once 'lang/lang_config.php'; ?>
<!doctype html>
<html lang="<?= get_current_lang() ?>" dir="<?= get_dir() ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Events</title>
    <link rel="icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/animate.css">
    <link rel="stylesheet" href="../assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/all.css">
    <link rel="stylesheet" href="../assets/css/flaticon.css">
    <link rel="stylesheet" href="../assets/css/themify-icons.css">
    <link rel="stylesheet" href="../assets/css/magnific-popup.css">
    <link rel="stylesheet" href="../assets/css/slick.css">
    <link rel="stylesheet" href="../assets/css/style.css">
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
        <?php include __DIR__ . '/../header_common.php'; ?>
        <section class="banner_part">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-lg-6 col-md-8">
                        <div class="banner_text">
                            <div class="banner_text_iner">
                                <h1>Best Highlights
                                    of the Latest</h1>
                                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                    sed do eiusmod tempor incididunt ut labore et dolore
                                    magna aliqua. Quis ipsum </p>
                                <a href="#" class="btn_1">Watch Tutorial</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="client_logo">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <div class="client_logo_slider owl-carousel d-flex justify-content-between">
                            <div class="single_client_logo">
                                <img src="img/client_logo/client_logo_1.png" alt="">
                            </div>
                            <div class="single_client_logo">
                                <img src="img/client_logo/client_logo_2.png" alt="">
                            </div>
                            <div class="single_client_logo">
                                <img src="img/client_logo/client_logo_3.png" alt="">
                            </div>
                            <div class="single_client_logo">
                                <img src="img/client_logo/client_logo_4.png" alt="">
                            </div>
                            <div class="single_client_logo">
                                <img src="img/client_logo/client_logo_5.png" alt="">
                            </div>
                            <div class="single_client_logo">
                                <img src="img/client_logo/client_logo_1.png" alt="">
                            </div>
                            <div class="single_client_logo">
                                <img src="img/client_logo/client_logo_2.png" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php
        require_once __DIR__ . '/../../../model/evenementModel.php';
        require_once __DIR__ . '/../../../model/participationModel.php';

        $eventModel = new EvenementModel();
        $participationModel = new ParticipationModel();

        function normalize_event_image($img) {
            $default = 'img/favicon.png';
            if (empty($img)) return $default;
            $img = trim($img);
            // absolute URLs
            if (stripos($img, 'http://') === 0 || stripos($img, 'https://') === 0) return $img;
            // already web-root absolute for this project
            if (strpos($img, '/gamingroom/uploads/events/') === 0) return $img;
            // server-relative uploads
            if (strpos($img, '/uploads/events/') === 0) return '/gamingroom' . $img;
            // legacy stored relative path
            if (strpos($img, 'uploads/events/') === 0) return '/gamingroom/' . $img;
            // other absolute path
            if (strpos($img, '/') === 0) return $img;
            return $img;
        }

        $events = $eventModel->getAllEvents();
        ?>

        <section class="about_us section_padding">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-5 col-lg-6 col-xl-6">
                        <div class="learning_img">
                            <img src="img/about_img.png" alt="">
                        </div>
                    </div>
                    <div class="col-md-7 col-lg-6 col-xl-5">
                        <div class="about_us_text">
                            <h2><?= __('find_out_about_us') ?></h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing
                                elit sed do eiusmod tempor incididunt ut labore et
                                dolore magna aliqua. </p>
                            <a href="#" class="btn_1"><?= __('install_now') ?></a>
                            <a href="#" class="btn_1"><?= __('watch_tutorial') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Events List -->
        <section class="upcomming_war">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section_tittle text-center">
                            <h2><?= __('all_events') ?></h2>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $ev): ?>
                            <?php $count = (int) $eventModel->countParticipants($ev['id_evenement']); ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card event-card" style="min-height:220px;">
                                    <?php
                                        $img = 'img/favicon.png';
                                        if (!empty($ev['image'])) {
                                            $img = $ev['image'];
                                            // If older records stored a relative uploads path, normalize it to web-root path
                                            if (strpos($img, 'uploads/events/') === 0) {
                                                $img = '/gamingroom/' . $img;
                                            }
                                        }
                                    ?>
                                    <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($ev['titre']) ?>" style="height:120px; object-fit:cover;">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($ev['titre']) ?></h5>
                                        <p class="card-text text-muted"><?= !empty($ev['date_evenement']) ? date('d/m/Y', strtotime($ev['date_evenement'])) : '' ?> — <?= htmlspecialchars($ev['lieu']) ?></p>
                                        <p class="card-text"><?= __('participants') ?>: <span class="badge participants-badge"><?= $count ?></span></p>
                                        <a href="event_details.php?id=<?= $ev['id_evenement'] ?>" class="btn btn-sm btn-outline-light"><?= __('show_details') ?></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-info"><?= __('no_events') ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <section class="live_stareams padding_bottom">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-between">
                    <div class="col-md-2 offset-lg-2 offset-sm-1">
                        <div class="live_stareams_text">
                            <h2>live <br> stareams</h2>
                            <div class="btn_1">install now</div>
                        </div>
                    </div>
                    <div class="col-md-7 offset-sm-1">
                        <div class="live_stareams_slide owl-carousel">
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_1.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_2.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_1.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_2.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_1.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_2.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_1.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                            <div class="live_stareams_slide_img">
                                <img src="img/live_streams_2.png" alt="">
                                <div class="extends_video">
                                    <a id="play-video_1" class="video-play-button popup-youtube"
                                        href="https://www.youtube.com/watch?v=pBFQdxA-apI">
                                        <span class="fas fa-play"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="Latest_War">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section_tittle text-center">
                            <h2><?= __('latest_war_fight') ?></h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center align-items-center">
                    <div class="col-lg-12">
                        <div class="Latest_War_text">
                            <div class="row justify-content-center align-items-center h-100">
                                <div class="col-lg-6">
                                    <div class="single_war_text text-center">
                                        <img src="img/favicon.png" alt="">
                                        <h4>Open War chalange</h4>
                                        <p>27 june , 2020</p>
                                        <a href="#">view fight</a>
                                        <div class="war_text_item d-flex justify-content-around align-items-center">
                                            <img src="img/war_logo_1.png" alt="">
                                            <h2>190<span>vs</span>189</h2>
                                            <img src="img/war_logo_2.png" alt="">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <a href="#" class="btn_2">Watch Tutorial</a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="latest_war_list">
                            <div class="single_war_text">
                                <div class="war_text_item d-flex justify-content-around align-items-center">
                                    <img src="img/war_logo_1.png" alt="">
                                    <h2>190<span>vs</span>189</h2>
                                    <img src="img/war_logo_2.png" alt="">
                                    <div class="war_date">
                                        <a href="#">27 june 2020</a>
                                        <p>Open War chalange</p>
                                    </div>
                                </div>
                            </div>
                            <div class="single_war_text">
                                <div class="war_text_item d-flex justify-content-around align-items-center">
                                    <img src="img/war_logo_1.png" alt="">
                                    <h2>190<span>vs</span>189</h2>
                                    <img src="img/war_logo_2.png" alt="">
                                    <div class="war_date">
                                        <a href="#">27 june 2020</a>
                                        <p>Open War chalange</p>
                                    </div>
                                </div>
                            </div>
                            <div class="single_war_text">
                                <div class="war_text_item d-flex justify-content-around align-items-center">
                                    <img src="img/war_logo_1.png" alt="">
                                    <h2>190<span>vs</span>189</h2>
                                    <img src="img/war_logo_2.png" alt="">
                                    <div class="war_date">
                                        <a href="#">27 june 2020</a>
                                        <p>Open War chalange</p>
                                    </div>
                                </div>
                            </div>
                            <div class="single_war_text">
                                <div class="war_text_item d-flex justify-content-around align-items-center">
                                    <img src="img/war_logo_1.png" alt="">
                                    <h2>190<span>vs</span>189</h2>
                                    <img src="img/war_logo_2.png" alt="">
                                    <div class="war_date">
                                        <a href="#">27 june 2020</a>
                                        <p>Open War chalange</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="Latest_War_text Latest_War_bg_1">
                            <div class="row justify-content-center align-items-center h-100">
                                <div class="col-lg-6">
                                    <div class="single_war_text text-center">
                                        <img src="img/favicon.png" alt="">
                                        <h4>Open War chalange</h4>
                                        <p>27 june , 2020</p>
                                        <a href="#">view fight</a>
                                        <div class="war_text_item d-flex justify-content-around align-items-center">
                                            <img src="img/war_logo_1.png" alt="">
                                            <h2>190<span>vs</span>189</h2>
                                            <img src="img/war_logo_2.png" alt="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" class="btn_2">Watch Tutorial</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="gallery_part section_padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5">
                        <div class="section_tittle text-center">
                            <h2>All Fighter</h2>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-12">
                        <div class="gallery_part_item">
                            <div class="grid">
                                <div class="grid-sizer"></div>
                                <a href="img/gallery/gallery_item_1.png"
                                    class="grid-item bg_img img-gal grid-item--height-1"
                                    style="background-image: url('img/gallery/gallery_item_1.png')">
                                    <div class="single_gallery_item">
                                        <div class="single_gallery_item_iner">
                                            <div class="gallery_item_text">
                                                <i class="ti-zoom-in"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="img/gallery/gallery_item_2.png" class="grid-item bg_img img-gal"
                                    style="background-image: url('img/gallery/gallery_item_2.png')">
                                    <div class="single_gallery_item">
                                        <div class="single_gallery_item_iner">
                                            <div class="gallery_item_text">
                                                <i class="ti-zoom-in"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="img/gallery/gallery_item_3.png" class="grid-item bg_img img-gal"
                                    style="background-image: url('img/gallery/gallery_item_3.png')">
                                    <div class="single_gallery_item">
                                        <div class="single_gallery_item_iner">
                                            <div class="gallery_item_text">
                                                <i class="ti-zoom-in"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="img/gallery/gallery_item_5.png"
                                    class="grid-item bg_img img-gal grid-item--height-2"
                                    style="background-image: url('img/gallery/gallery_item_5.png')">
                                    <div class="single_gallery_item">
                                        <div class="single_gallery_item_iner">
                                            <div class="gallery_item_text">
                                                <i class="ti-zoom-in"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="img/gallery/gallery_item_7.png"
                                    class="grid-item bg_img img-gal grid-item--height-2"
                                    style="background-image: url('img/gallery/gallery_item_7.png')">
                                    <div class="single_gallery_item">
                                        <div class="single_gallery_item_iner">
                                            <div class="gallery_item_text">
                                                <i class="ti-zoom-in"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="img/gallery/gallery_item_6.png"
                                    class="grid-item bg_img img-gal grid-item--width-1"
                                    style="background-image: url('img/gallery/gallery_item_6.png')">
                                    <div class="single_gallery_item">
                                        <div class="single_gallery_item_iner">
                                            <div class="gallery_item_text">
                                                <i class="ti-zoom-in"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <a href="img/gallery/gallery_item_4.png"
                                    class="grid-item bg_img img-gal sm_weight  grid-item--height-1"
                                    style="background-image: url('img/gallery/gallery_item_4.png')">
                                    <div class="single_gallery_item">
                                        <div class="single_gallery_item_iner">
                                            <div class="gallery_item_text">
                                                <i class="ti-zoom-in"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="upcomming_war">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="section_tittle text-center">
                            <h2><?= __('upcoming_fighter') ?></h2>
                        </div>
                    </div>
                </div>
                <div class="upcomming_war_iner">
                    <div class="row justify-content-center align-items-center h-100">
                        <div class="col-10 col-sm-5 col-lg-3">
                            <div class="upcomming_war_counter text-center">
                                <h2>Dark Dragon</h2>
                                <div id="timer" class="d-flex justify-content-between">
                                    <div id="days"></div>
                                    <div id="hours"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="pricing_part padding_top">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6">
                        <div class="section_tittle text-center">
                            <h2><?= __('pricing_plans') ?></h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-3 col-sm-6">
                        <div class="single_pricing_part">
                            <p><?= __('silver_package') ?></p>
                            <h3>$50.00</h3>
                            <ul>
                                <li>2GB Bandwidth</li>
                                <li>Two Account</li>
                                <li>15GB Storage</li>
                            </ul>
                            <a href="#" class="btn_2"><?= __('choose_plane') ?></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="single_pricing_part">
                            <p>Silver Package</p>
                            <h3>$60.00</h3>
                            <ul>
                                <li>2GB Bandwidth</li>
                                <li>Two Account</li>
                                <li>15GB Storage</li>
                            </ul>
                            <a href="#" class="btn_2">Choose Plane</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-6">
                        <div class="single_pricing_part">
                            <p>Silver Package</p>
                            <h3>$80.00</h3>
                            <ul>
                                <li>2GB Bandwidth</li>
                                <li>Two Account</li>
                                <li>15GB Storage</li>
                            </ul>
                            <a href="#" class="btn_2">Choose Plane</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- pricing part end-->

        <!--::footer_part start::-->
        <footer class="footer_part">
            <div class="footer_top">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <a href="index.html" class="footer_logo_iner"> <img src="img/logo.png" alt="#"> </a>
                                <p>Heaven fruitful doesn't over lesser days appear creeping seasons so behold bearing
                                    days
                                    open
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4><?= __('contact_info') ?></h4>
                                <p>Address : Your address goes
                                    here, your demo address.
                                    Bangladesh.</p>
                                <p>Phone : +8880 44338899</p>
                                <p>Email : info@colorlib.com</p>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4><?= __('important_link') ?></h4>
                                <ul class="list-unstyled">
                                    <li><a href=""> WHMCS-bridge</a></li>
                                    <li><a href=""><?= __('search_domain') ?></a></li>
                                    <li><a href=""><?= __('my_account') ?></a></li>
                                    <li><a href=""><?= __('shopping_cart') ?></a></li>
                                    <li><a href=""><?= __('our_shop') ?></a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="single_footer_part">
                                <h4><?= __('newsletter') ?></h4>
                                <p>Heaven fruitful doesn't over lesser in days. Appear creeping seasons deve behold
                                    bearing
                                    days
                                    open
                                </p>
                                <div id="mc_embed_signup">
                                    <form target="_blank"
                                        action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01"
                                        method="get" class="subscribe_form relative mail_part">
                                        <input type="email" name="email" id="newsletter-form-email"
                                            placeholder="Email Address" class="placeholder hide-on-focus"
                                            onfocus="this.placeholder = ''"
                                            onblur="this.placeholder = ' Email Address '">
                                        <button type="submit" name="submit" id="newsletter-submit"
                                            class="email_icon newsletter-submit button-contactForm"><i
                                                class="far fa-paper-plane"></i></button>
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
                                <P><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This template is made with <i class="ti-heart" aria-hidden="true"></i> by <a href="https://colorlib.com" target="_blank">Colorlib</a>
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></P>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="footer_icon social_icon">
                                <ul class="list-unstyled">
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-facebook-f"></i></a>
                                    </li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fas fa-globe"></i></a></li>
                                    <li><a href="#" class="single_social_icon"><i class="fab fa-behance"></i></a></li>
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