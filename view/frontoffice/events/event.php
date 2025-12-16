<?php
session_start();
require_once __DIR__ . '/../../../model/evenementModel.php';
require_once __DIR__ . '/../../../db_config.php';

$eventModel = new EvenementModel();
$events = $eventModel->getAllEvents();

// Extract unique themes for the filter
// Static themes as requested
$uniqueThemes = ['Sport', 'Education', 'Esport', 'Creation', 'Prévention', 'Coaching', 'Compétition'];

// Robust mapping for themes using tokens
mb_internal_encoding('UTF-8');

function get_theme_token($str) {
    if (empty($str)) return 'evenement';
    
    // Use multi-byte safe case-insensitive search
    if (mb_stripos($str, 'esport') !== false) return 'esport';
    if (mb_stripos($str, 'sport') !== false) return 'sport';
    // 'ducat' matches 'education', 'éducation'
    if (mb_stripos($str, 'ducat') !== false) return 'education'; 
    if (mb_stripos($str, 'création') !== false || mb_stripos($str, 'creation') !== false) return 'creation';
    if (mb_stripos($str, 'prévention') !== false || mb_stripos($str, 'prevention') !== false) return 'prevention';
    if (mb_stripos($str, 'coaching') !== false) return 'coaching';
    if (mb_stripos($str, 'compétition') !== false || mb_stripos($str, 'competition') !== false) return 'competition';
    
    return 'evenement';
}

function normalize_asset_path($img) {
    if (empty($img)) return '../assets/img/default-event.jpg';
    $img = trim($img);
    if (strpos($img, 'http') === 0) return $img;
    if (strpos($img, '/') === 0) return $img;
    if (strpos($img, 'assets/') === 0) {
        // Map backoffice stored path to a reachable URL from frontoffice/events/
        return '../../backoffice/events/' . $img; // => ../../backoffice/events/assets/<file>
    }
    // Legacy fallback
    return 'events/' . $img;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Événements – ENGAGE</title>
    <link rel="icon" href="../assets/img/favicon.png">
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/all.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="css/event-custom.css">
    <link rel="stylesheet" href="../assets/css/custom-frontoffice.css">
    <style>
        :root {
            --primary: #ff4a57;
            --primary-light: #ff6b6b;
            --dark: #1f2235;
            --dark-light: #2d325a;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.8);
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }

        .body_bg {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            min-height: 100vh;
        }

        /* Event Card Styles */
        .event-card-enhanced {
            background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            cursor: pointer;
        }

        .event-card-enhanced:hover {
            transform: translateY(-8px);
            border-color: rgba(255,74,87,0.3);
            box-shadow: 0 15px 40px rgba(255,74,87,0.15);
        }

        .event-card-image {
            position: relative;
            width: 100%;
            height: 200px;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(255,74,87,0.2), rgba(255,107,107,0.1));
        }

        .event-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .event-card-enhanced:hover .event-card-image img {
            transform: scale(1.05);
        }

        .event-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(31, 34, 53, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .event-card-enhanced:hover .event-overlay {
            opacity: 1;
        }

        .event-btn-details {
            background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .event-btn-details:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(255,74,87,0.3);
            color: white;
        }

        .event-price-badge {
            position: absolute;
            top: 12px;
            right: 12px;
        }

        .custom-badge-price,
        .custom-badge-free {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.85rem;
        }

        .custom-badge-price {
            background: linear-gradient(135deg, rgba(255,74,87,0.9) 0%, rgba(255,107,107,0.9) 100%);
            color: white;
        }

        .custom-badge-free {
            background: linear-gradient(135deg, rgba(40,167,69,0.9) 0%, rgba(34,197,94,0.9) 100%);
            color: white;
        }

        .event-card-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .event-title {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.15rem;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 12px;
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
        }

        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .event-meta-item i {
            color: #ff4a57;
            min-width: 16px;
        }

        .time-sep {
            margin: 0 4px;
        }

        .event-description {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            margin-bottom: 12px;
            flex-grow: 1;
            line-height: 1.5;
        }

        .event-footer {
            display: flex;
            align-items: center;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
        }

        .event-participants {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .event-participants i {
            color: #ff4a57;
        }

        .no-events {
            text-align: center;
            padding: 40px 20px;
        }

        .no-events h4 {
            color: #ffffff;
            margin-bottom: 10px;
        }

        .no-events p {
            color: rgba(255,255,255,0.6);
        }

        /* Search Styles */
        .event-search-container {
            margin-top: 40px;
        }

        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            padding: 0 16px;
            transition: all 0.3s ease;
        }

        .search-wrapper:focus-within {
            background: rgba(255,255,255,0.1);
            border-color: rgba(255,74,87,0.3);
            box-shadow: 0 8px 20px rgba(255,74,87,0.1);
        }

        .search-icon {
            color: rgba(255,255,255,0.5);
            margin-right: 12px;
            font-size: 1.1rem;
        }

        .event-search-input {
            flex: 1;
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1rem;
            padding: 14px 0;
            outline: none;
        }

        .event-search-input::placeholder {
            color: rgba(255,255,255,0.4);
        }

        .search-clear {
            cursor: pointer;
            color: rgba(255,255,255,0.5);
            transition: color 0.3s ease;
            padding: 4px 8px;
        }

        .search-clear:hover {
            color: rgba(255,255,255,0.8);
        }

        .search-results-info {
            margin-top: 12px;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            text-align: center;
        }

        @media (max-width: 768px) {
            .event-card-image {
                height: 150px;
            }

            .event-title {
                font-size: 1rem;
            }

            .event-search-input {
                font-size: 0.9rem;
            }
        }

        /* Ensure buttons are clickable and no overlay blocks clicks */
        .mission-card-enhanced, .game-card-body { position: relative; }
        .mission-card-enhanced::after { pointer-events: none; }
        .event-overlay { pointer-events: none; }
        .event-card-enhanced .event-overlay { pointer-events: none; }
        .btn.btn-primary { position: relative; z-index: 2; }

        /* Theme Filter Styles */
        /* Theme Filter Styles */
        .filter-section-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
        }

        .filter-box {
            background: #25283d; /* Darker box background similar to image */
            border-radius: 16px;
            padding: 24px 40px;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            border: 1px solid rgba(255,255,255,0.05);
        }

        .filter-title {
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: none; /* Keep natural case */
        }

        .theme-filter-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .filter-btn {
            background: rgba(255,255,255,0.05); /* Very subtle background */
            border: none;
            color: rgba(255,255,255,0.6);
            padding: 8px 20px;
            border-radius: 20px; /* Pill shape */
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .filter-btn:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }

        .filter-btn.active {
            background: rgba(255,255,255,0.15); /* Slightly lighter for active */
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="body_bg">
    <?php include __DIR__ . '/../header_common.php'; ?>

    <!-- BREADCRUMB + HERO -->
    <section class="breadcrumb_bg">
        <div class="container">
            <div class="breadcrumb_iner_item text-center">
                <h1 class="design-title">📅 Tous Les Événements</h1>
                <p class="design-subtitle">Découvrez et participez aux événements de notre communauté</p>

                <!-- Stats -->
                <div class="stats-panel" style="margin-top: 30px;">
                    <span><i class="fas fa-calendar-alt"></i> <strong><?= count($events) ?></strong> Événements</span>
                    <span><i class="fas fa-users"></i> Communauté Engagée</span>
                    <span><i class="fas fa-globe"></i> Multi-Thématiques</span>
                    <span><i class="fas fa-heart"></i> Impact Social</span>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION EVENTS -->
    <section class="section_padding">
        <div class="container">
            
            <!-- Theme Filters -->
            <!-- Theme Filters -->
            <div class="filter-section-wrapper">
                <div class="filter-box">
                    <h4 class="filter-title">Filtrer par thème</h4>
                    <div class="theme-filter-container">
                        <button class="filter-btn active" data-filter="all">Tous</button>
                        <?php foreach ($uniqueThemes as $thm): ?>
                            <button class="filter-btn" data-filter="<?= htmlspecialchars(get_theme_token($thm)) ?>">
                                 <?= htmlspecialchars($thm) ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="row" id="events-row">
            <?php if (!empty($events)): ?>
                <?php foreach ($events as $ev): ?>
                    <?php
                        $title = htmlspecialchars($ev['titre'] ?? 'Événement');
                        $desc = htmlspecialchars($ev['description'] ?? '');
                        $date = !empty($ev['date_evenement']) ? date('d/m/Y', strtotime($ev['date_evenement'])) : '';
                        $rawDate = $ev['date_evenement'] ?? '';
                        $lieu = htmlspecialchars($ev['lieu'] ?? 'Lieu non spécifié');
                        $time = !empty($ev['heure_evenement']) ? substr($ev['heure_evenement'], 0, 5) : null;
                        $duration = isset($ev['duree_minutes']) ? (int)$ev['duree_minutes'] : 0;
                        $img = normalize_asset_path($ev['image'] ?? '');
                        $participants = (int)$eventModel->countParticipants($ev['id_evenement'] ?? 0);
                        $type = isset($ev['type_evenement']) ? $ev['type_evenement'] : 'gratuit';
                        $prix = isset($ev['prix']) ? (float)$ev['prix'] : 0;
                        $isPaid = ($type === 'payant') && $prix > 0;
                        // Concatenate fields to auto-detect theme if explicit theme is missing
                        $themeSource = ($ev['theme'] ?? '') . ' ' . ($ev['titre'] ?? '') . ' ' . ($ev['description'] ?? '');
                        $theme = get_theme_token($themeSource);
                        
                        // Debug: Uncomment to see what token is assigned if needed
                        // echo "<!-- DEBUG: " . htmlspecialchars($themeSource) . " -> $theme -->";

                        $images = [
                            "sport" => "sport.png",
                            "education" => "education.png",
                            "esport" => "valorant.png",
                            "valorant" => "valorant.png",
                            "minecraft" => "minecraft.png",
                            "creation" => "roblox.png",
                            "roblox" => "roblox.png",
                            "prevention" => "sante.png",
                            "sante" => "sante.png",
                            "coaching" => "coaching.png",
                            "competition" => "cyber.png",
                            "cyber" => "cyber.png",
                            "evenement" => "default.png",
                        ];
                        $image = $images[$theme] ?? "default.png";
                        $imagePath = "../assets/img/" . $image;
                        $cardImg = !empty($ev['image']) ? $img : $imagePath;
                        $eventId = $ev['id_evenement'] ?? 0;
                    ?>
                    <div class="col-lg-4 col-md-6 event-item" 
                         data-theme="<?= htmlspecialchars($theme) ?>" 
                         data-type="<?= htmlspecialchars($type) ?>" 
                         data-date="<?= $rawDate ?>"
                         data-time="<?= $time ?? '' ?>"
                         data-duration="<?= $duration ?>"
                         data-title="<?= $title ?>"
                         data-location="<?= $lieu ?>"
                         data-description="<?= $desc ?>">
                        <div class="game-card store-card">
                            <!-- Image de l'événement -->
                            <div class="game-card-img">
                                <img src="<?= htmlspecialchars($cardImg) ?>" alt="<?= htmlspecialchars($theme) ?>">
                                
                                <!-- Badge de prix/gratuit -->
                                <?php if ($isPaid): ?>
                                    <div class="stock-badge">Payant</div>
                                <?php else: ?>
                                    <div class="stock-badge">Gratuit</div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Corps de la carte -->
                            <div class="game-card-body">
                                <h5 class="game-title">
                                    <?= $title ?>
                                </h5>
                                
                                <div class="game-partner">
                                    <i class="fas fa-map-marker-alt"></i> <?= $lieu ?>
                                </div>
                                
                                <div class="game-info">
                                    <span class="game-category">
                                        <i class="far fa-calendar"></i> <?= $date ?>
                                    </span>
                                    <?php if ($time): ?>
                                        <span class="game-age">
                                            <i class="fas fa-clock"></i> <?= $time ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="game-platform" style="display: flex; gap: 8px; margin: 8px 0;">
                                    <?php if ($isPaid): ?>
                                        <span style="background: #ff4a57; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                            <?= number_format($prix, 0) ?> TND
                                        </span>
                                    <?php else: ?>
                                        <span style="background: #28a745; color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                                            Gratuit
                                        </span>
                                    <?php endif; ?>
                                    <span style="background: rgba(255,255,255,0.1); color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.85rem;">
                                        <i class="fas fa-users"></i> <?= $participants ?> participants
                                    </span>
                                </div>
                                
                                <a href="event_details.php?id=<?= $eventId ?>" class="btn-view-game">
                                    <i class="far fa-eye"></i> Voir l'Événement
                                </a>
                                
                                <div class="game-foot">
                                    <div class="game-price-inline">
                                        <?php if ($isPaid): ?>
                                            <?= number_format($prix, 0) ?> TND
                                        <?php else: ?>
                                            Gratuit
                                        <?php endif; ?>
                                    </div>
                                    <div class="game-stats">
                                        <span><?= isset($ev['vues']) ? (int)$ev['vues'] : 0 ?> vues</span>
                                        <span>0 likes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="event-card-enhanced no-events">
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
                                placeholder="Rechercher par date, heure, durée, thème, type..." 
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

</div>

<!-- jquery plugins -->
<script src="../assets/js/jquery-1.12.1.min.js"></script>
<script src="../assets/js/popper.min.js"></script>
<script src="../assets/js/bootstrap.min.js"></script>
<script src="../assets/js/jquery.magnific-popup.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('event-search');
    const searchClear = document.getElementById('search-clear');
    const searchResultsInfo = document.getElementById('search-results-info');
    const resultCount = document.getElementById('result-count');
    // Important: we select items that have data attributes we just added
    const allItems = document.querySelectorAll('.event-item');

    // State
    let currentTheme = 'all';
    let currentSearchTerm = '';

    const filterBtns = document.querySelectorAll('.filter-btn');

    function applyFilters() {
        const term = currentSearchTerm.toLowerCase().trim();
        let visibleCount = 0;

        allItems.forEach(card => {
            // Data attributes
            const theme = (card.dataset.theme || '').toLowerCase();
            
            // Searchable fields
            const title = (card.dataset.title || '').toLowerCase();
            const location = (card.dataset.location || '').toLowerCase();
            const description = (card.dataset.description || '').toLowerCase();
            const date = (card.dataset.date || '').toLowerCase();
            const time = (card.dataset.time || '').toLowerCase();
            const type = (card.dataset.type || '').toLowerCase();

            // 1. Check Theme
            const matchesTheme = (currentTheme === 'all' || theme === currentTheme);

            // 2. Check Search Term
            const matchesSearch = 
                term === '' ||
                title.includes(term) || 
                location.includes(term) || 
                description.includes(term) ||
                date.includes(term) ||
                time.includes(term) ||
                theme.includes(term) ||
                type.includes(term);

            if (matchesTheme && matchesSearch) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Update search info visibility
        if (term !== '' || currentTheme !== 'all') {
            // Show count if we are filtering in any way (optional preference)
            // But usually the user wants to know how many results if they typed something.
            // Let's keep original behavior: only show specific "results info" if typing.
            // OR we can always show it if it helps. 
            // For now, let's Stick to the "search-results-info" being mostly about the text search
            // unless we want to show "5 filtered events". 
            
            // Let's just follow the text search visibility rule for the info box to avoid clutter,
            // or update it if text is present.
            if (term !== '') {
                searchResultsInfo.style.display = 'block';
                resultCount.textContent = visibleCount;
            } else {
                searchResultsInfo.style.display = 'none';
            }
        } else {
            searchResultsInfo.style.display = 'none';
        }
    }

    // --- Theme Filter Events ---
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all
            filterBtns.forEach(b => b.classList.remove('active'));
            // Add to clicked
            btn.classList.add('active');
            
            // Update state
            currentTheme = btn.getAttribute('data-filter');
            applyFilters();
        });
    });

    // --- Search Events ---
    searchInput.addEventListener('input', function(e) {
        currentSearchTerm = e.target.value;
        
        if (currentSearchTerm.length > 0) {
            searchClear.style.display = 'flex';
        } else {
            searchClear.style.display = 'none';
        }

        applyFilters();
    });

    searchClear.addEventListener('click', function() {
        searchInput.value = '';
        currentSearchTerm = '';
        searchClear.style.display = 'none';
        searchResultsInfo.style.display = 'none';
        applyFilters();
        searchInput.focus();
    });

    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            searchInput.value = '';
            currentSearchTerm = '';
            searchClear.style.display = 'none';
            searchResultsInfo.style.display = 'none';
            applyFilters();
        }
    });
});
</script>

</body>
</html>
