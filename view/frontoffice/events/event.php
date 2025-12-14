<?php
require_once __DIR__ . '/../../../model/evenementModel.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$eventModel = new EvenementModel();
$events = $eventModel->getAllEvents();

// Debug: Check if events are empty
error_log("Events count: " . count($events));
error_log("Events data: " . print_r($events, true));

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
    return $map[$id] ?? 'Autre';
}

function normalize_event_image($img) {
    return normalize_asset_path($img);
}
?>
<!doctype html>
<?php require_once 'lang/lang_config.php'; ?>
<html lang="<?= get_current_lang() ?>" dir="<?= get_dir() ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Events - gaming</title>
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
                                        <a class="nav-link active" href="event.php"><?= __('events') ?></a>
                                    </li>
                                    <li class="nav-item">
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
                            <a href="#" class="btn_1 d-none d-sm-block"><?= __('install_now') ?></a>
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
                                <h1><?= __('all_events') ?></h1>
                                <p>Browse all events — upcoming and past — from the gaming community.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="Latest_War section_padding">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="section_tittle text-center">
                            <h2><?= __('events') ?></h2>
                        </div>
                    </div>
                </div>

                <div class="row" id="events-row">
<?php if (!empty($events)): ?>
    <?php foreach ($events as $ev): ?>
        <?php
            $title = htmlspecialchars($ev['titre']);
            $desc = htmlspecialchars($ev['description']);
            $date = !empty($ev['date_evenement']) ? date('d M Y', strtotime($ev['date_evenement'])) : '';
            $lieu = htmlspecialchars($ev['lieu']);
            $time = !empty($ev['heure_evenement']) ? substr($ev['heure_evenement'], 0, 5) : null;
            $img = !empty($ev['image']) ? $ev['image'] : 'img/default-event.jpg';
            $img = !empty($ev['image']) ? $ev['image'] : 'img/default-event.jpg';
            $img = normalize_asset_path($img);
            $participants = (int)$eventModel->countParticipants($ev['id_evenement']);
            $type = isset($ev['type_evenement']) ? $ev['type_evenement'] : 'gratuit';
            $prix = isset($ev['prix']) ? (float)$ev['prix'] : 0;
            $isPaid = ($type === 'payant') && $prix > 0;
        ?>
        <div class="col-lg-4 col-md-6 mb-4" data-theme="<?= htmlspecialchars($ev['id_organisation'] ?? '') ?>" data-type="<?= htmlspecialchars($type) ?>" data-date="<?= $ev['date_evenement'] ?? '' ?>">
            <div class="event-card">
                <div class="event-card-image">
                    <img src="<?= htmlspecialchars($img) ?>" alt="<?= $title ?>" onerror="this.src='img/default-event.jpg'">
                    <div class="event-overlay">
                        <a href="event_details.php?id=<?= $ev['id_evenement']; ?>" class="event-btn-details"><?= __('show_details') ?></a>
                    </div>
                    <div class="event-price-badge">
                        <?php if ($isPaid): ?>
                            <span class="custom-badge-price"><?= number_format($prix, 0) ?> TND</span>
                        <?php else: ?>
                            <span class="custom-badge-free">GRATUIT</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="event-card-info">
                    <h4 class="event-title"><?= $title ?></h4>
                    <div class="event-meta">
                        <div class="event-meta-item">
                            <i class="far fa-calendar-alt"></i>
                            <span><?= $date ?></span>
                            <?php if ($time): ?>
                                <span class="time-sep">•</span>
                                <i class="far fa-clock"></i>
                                <span><?= $time ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= $lieu ?></span>
                        </div>
                    </div>
                    <p class="event-description"><?= (strlen($desc) > 100) ? substr($desc,0,100).'...' : $desc; ?></p>
                    <div class="event-footer">
                        <span class="event-participants">
                            <i class="far fa-user"></i> <?= $participants ?>
                        </span>
                        <span class="event-participants ml-3" title="<?= ($ev['vues'] ?? 0) . ' vues' ?>">
                            <i class="far fa-eye"></i> <?= $ev['vues'] ?? 0 ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="col-12">
        <div class="event-card no-events">
            <h4>Aucun événement trouvé</h4>
            <p>Revenez bientôt pour découvrir nos prochains événements</p>
        </div>
    </div>
<?php endif; ?>
                </div>

                <div class="row mt-5">
                    <div class="col-12">
                        <div class="event-search-container">
                            <div class="search-wrapper">
                                <div class="search-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <input 
                                    id="event-search" 
                                    class="event-search-input" 
                                    type="text" 
                                    placeholder="Rechercher par nom ou type d'événement..." 
                                    autocomplete="off"
                                />
                                <div class="search-clear" id="search-clear" style="display:none;">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                            <div class="search-results-info" id="search-results-info" style="display:none;">
                                <span id="result-count">0</span> événement(s) trouvé(s)
                            </div>
                        </div>
                    </div>
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

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const interpretBtn = document.getElementById('smart-interpret');
        const resetBtn = document.getElementById('filter-reset');
        const smartQuery = document.getElementById('smart-query');
        const items = document.querySelectorAll('#events-row > [data-theme]');

        // Export PHP theme map to JS
        const THEME_MAP = <?= json_encode($themeMap, JSON_HEX_TAG) ?>;

        function applyFilter() {
            // no-op when called without parsed filter; keep for compatibility
        }

        function filterByParsed(parsed) {
            const theme = parsed.theme || '';
            const type = parsed.type || '';
            const date = parsed.date || '';

            items.forEach(item => {
                const itTheme = item.getAttribute('data-theme') || '';
                const itType = item.getAttribute('data-type') || '';
                const itDate = item.getAttribute('data-date') || '';
                let visible = true;

                if (theme && String(itTheme) !== String(theme)) visible = false;
                if (type && String(itType) !== String(type)) visible = false;
                if (date && String(itDate) !== String(date)) visible = false;

                item.style.display = visible ? '' : 'none';
            });
        }
        }

        // Simple natural-language parser for the smart query input
        function parseSmartQuery(query) {
            // returns { theme:'', type:'', date:'' }
            const out = { theme: '', type: '', date: '' };
            if (!query) return out;
            const q = query.toLowerCase();

            // detect type keywords
            if (q.includes('gratuit') || q.includes('free')) out.type = 'gratuit';
            if (q.includes('payant') || q.includes('pay') || q.includes('payé')) out.type = 'payant';

            // detect relative dates: aujourd'hui, demain, prochain(s)
            if (q.includes("aujourd")) {
                out.date = (new Date()).toISOString().slice(0,10);
            } else if (q.includes('demain')) {
                const d = new Date(); d.setDate(d.getDate()+1);
                out.date = d.toISOString().slice(0,10);
            } else {
                // look for explicit yyyy-mm-dd or dd-mm-yyyy
                const iso = q.match(/(20\d{2}-\d{2}-\d{2})/);
                if (iso) out.date = iso[1];
                else {
                    const dmy = q.match(/(\d{2}[-\/]\d{2}[-\/]20\d{2})/);
                    if (dmy) {
                        const parts = dmy[1].split(/[-\/]/);
                        // assume dd-mm-yyyy
                        out.date = parts[2] + '-' + parts[1] + '-' + parts[0];
                    }
                }
            }

            // detect theme by matching any theme label or id
            for (const [id, label] of Object.entries(THEME_MAP)) {
                const lab = String(label).toLowerCase();
                if (q.includes(lab)) { out.theme = String(id); break; }
            }

            // fallback: if user typed exact theme id
            const idMatch = q.match(/\b(\d+)\b/);
            if (!out.theme && idMatch) {
                const cand = idMatch[1];
                if (THEME_MAP[cand]) out.theme = cand;
            }

            return out;
        }

        function applySmartQuery(q) {
            const parsed = parseSmartQuery(q || '');
            filterByParsed(parsed);
        }

        if (interpretBtn) interpretBtn.addEventListener('click', function (e) {
            e.preventDefault();
            applySmartQuery(smartQuery.value.trim());
        });

        if (resetBtn) resetBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (smartQuery) smartQuery.value = '';
            // show all
            items.forEach(i => i.style.display = '');
        });

        // allow Enter to interpret as well
        if (smartQuery) {
            smartQuery.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    applySmartQuery(smartQuery.value.trim());
                }
            });
        }
    });
    </script>
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ===== SEARCH FUNCTIONALITY =====
        const searchInput = document.getElementById('event-search');
        const searchClear = document.getElementById('search-clear');
        const searchResultsInfo = document.getElementById('search-results-info');
        const resultCount = document.getElementById('result-count');
        const allItems = document.querySelectorAll('#events-row [data-theme]');

        // Real-time search function
        function filterEvents(searchTerm) {
            const term = searchTerm.toLowerCase().trim();
            let visibleCount = 0;

            allItems.forEach(card => {
                const title = card.querySelector('.event-title')?.textContent.toLowerCase() || '';
                const type = card.getAttribute('data-type')?.toLowerCase() || '';
                const description = card.querySelector('.event-description')?.textContent.toLowerCase() || '';
                const location = card.querySelector('.event-meta')?.textContent.toLowerCase() || '';

                // Check if search term matches title, type, description or location
                const matches = 
                    title.includes(term) || 
                    type.includes(term) || 
                    description.includes(term) || 
                    location.includes(term);

                if (term === '' || matches) {
                    card.classList.remove('hidden');
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                    card.style.display = 'none';
                }
            });

            // Update results info
            if (term !== '') {
                searchResultsInfo.style.display = 'block';
                resultCount.textContent = visibleCount;
            } else {
                searchResultsInfo.style.display = 'none';
            }
            
            // Reset carousel to first position when filtering
            currentIndex = 0;
            updateCarouselPosition();
        }

        // Event listeners
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value;
            
            // Show/hide clear button
            if (searchTerm.length > 0) {
                searchClear.style.display = 'flex';
            } else {
                searchClear.style.display = 'none';
            }

            filterEvents(searchTerm);
        });

        // Clear button functionality
        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            searchClear.style.display = 'none';
            searchResultsInfo.style.display = 'none';
            filterEvents('');
            searchInput.focus();
        });

        // Optional: Clear search on Escape key
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                searchInput.value = '';
                searchClear.style.display = 'none';
                searchResultsInfo.style.display = 'none';
                filterEvents('');
            }
        });
    });
    </script>
</body>

</html>
