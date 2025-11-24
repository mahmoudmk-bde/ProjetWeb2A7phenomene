<?php
session_start();
?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../controller/missioncontroller.php';
require_once __DIR__ . '/../../controller/feedbackcontroller.php';

$missionC = new missioncontroller();
$feedbackcontroller = new feedbackcontroller();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("<h2 style='color:white;text-align:center;margin-top:50px;'>❌ ID de mission manquant</h2>");
}

$id = intval($_GET['id']);
$mission = $missionC->getMissionById($id);

if (!$mission) {
    die("<h2 style='color:white;text-align:center;margin-top:50px;'>❌ Mission introuvable</h2>");
}

// Récupérer les statistiques de feedback
$feedbackStats = $feedbackcontroller->getFeedbackStats($id);
$averageRating = $feedbackStats['avg_rating'] ? round($feedbackStats['avg_rating'], 1) : 0;
$totalFeedbacks = $feedbackStats['total_feedbacks'] ?? 0;

// Récupérer les feedbacks
$feedbacks = $feedbackcontroller->getFeedbacksByMission($id);

// Vérifier si l'utilisateur a déjà donné son feedback
$userFeedback = null;
if (isset($_SESSION['user_id'])) {
    $userFeedback = $feedbackcontroller->getUserFeedback($id, $_SESSION['user_id']);
}

// Traitement du formulaire de feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    if (isset($_SESSION['user_id'])) {
        $rating = intval($_POST['rating']);
        $commentaire = trim($_POST['commentaire']);
        
        if ($rating >= 1 && $rating <= 5) {
            if ($feedbackcontroller->addFeedback($id, $_SESSION['user_id'], $rating, $commentaire)) {
                header("Location: missiondetails.php?id=$id&success=1");
                exit();
            } else {
                $error = "Erreur lors de l'ajout du feedback";
            }
        } else {
            $error = "La note doit être entre 1 et 5";
        }
    } else {
        header("Location: connexion.php?redirect=" . urlencode("missiondetails.php?id=$id"));
        exit();
    }
}

$theme = strtolower(trim($mission['theme']));
$images = [
    "sport" => "sport.png",
    "football" => "tournoi.png",
    "Éducation" => "education.png",
    "education" => "education.png",
    "esport" => "valorant.png",
    "valorant" => "valorant.png",
    "minecraft" => "minecraft.png",
    "Création" => "roblox.png",
    "prévention" => "sante.png",
    "prevention" => "sante.png",
    "coaching" => "coaching.png",
    "Compétition" => "cyber.png",
];
$image = $images[$theme] ?? "default.png";
$imagePath = "assets/img/" . $image;
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($mission['titre']) ?> – ENGAGE</title>

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/mission.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
    
    <style>
        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            color: #ddd;
            font-size: 1.8rem;
            padding: 5px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #ffd700;
        }

        .star-rating input:checked ~ label {
            color: #ffd700;
        }

        .star-rating input:checked + label {
            color: #ffd700;
        }

        .rating-summary .progress-bar {
            transition: width 0.5s ease-in-out;
        }

        .feedback-item {
            transition: transform 0.2s ease;
        }

        .feedback-item:hover {
            transform: translateX(5px);
            background: rgba(255,255,255,0.05) !important;
        }
        
        .btn-enhanced {
            background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            display: inline-block;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .btn-enhanced-secondary {
            background: transparent;
            border: 2px solid #ff4a57;
            color: #ff4a57;
        }
        
        .btn-enhanced-secondary:hover {
            background: #ff4a57;
            color: white;
        }
    </style>
</head>

<body>
<div class="body_bg">
    
    <!-- Chatbot -->
    <div id="chatbot-button">💬</div>
    <div id="chatbot-box">
        <div id="chatbot-header">ENGAGE Bot <span id="chatbot-close">×</span></div>
        <div id="chatbot-messages">
            <div class="chatbot-message bot-message">
                👋 Salut ! Je peux t'aider avec cette mission.
            </div>
        </div>
        <div id="chatbot-input-container">
            <input type="text" id="chatbot-input" placeholder="Écris ton message...">
            <button id="chatbot-send">➤</button>
        </div>
    </div>

    <!-- Header -->
    <header class="main_menu single_page_menu">
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/img/logo.png" alt="logo" style="height:45px;">
                </a>
                
                <button class="navbar-toggler" type="button" data-toggle="collapse" 
                        data-target="#navbarSupportedContent">
                    <span class="menu_icon"><i class="fas fa-bars"></i></span>
                </button>

                <div class="collapse navbar-collapse main-menu-item" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link active" href="missionlist.php">Missions</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Gamification</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Réclamations</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Événements</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Quizzes</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="connexion.php" class="btn_1 d-none d-sm-block">Se connecter</a>
                <?php else: ?>
                    <a href="index1.php" class="btn_1 d-none d-sm-block">Mon Espace</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Breadcrumb -->
    <section class="breadcrumb_bg">
        <div class="container">
            <div class="breadcrumb_iner_item text-center">
                <h1 class="design-title">🎯 Détails Mission</h1>
                <p class="design-subtitle">Plongez au cœur de l'aventure gaming solidaire</p>
            </div>
        </div>
    </section>

    <!-- Mission Details -->
    <section class="section_padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="enhanced-card">
                        <!-- Mission Header -->
                        <div class="text-center mb-5">
                            <div class="details-header">
                                <?php if (file_exists($imagePath)): ?>
                                    <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($mission['theme']) ?>" 
                                         style="width: 100%; height: 400px; object-fit: cover; border-radius: 15px; border: 3px solid rgba(255,255,255,0.1);">
                                <?php else: ?>
                                    <div style="background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%); height: 400px; display: flex; align-items: center; justify-content: center; border-radius: 15px;">
                                        <i class="fas fa-gamepad fa-5x" style="color: white;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h1 class="game-title-main mt-4" style="color: white; font-size: 2.5rem; font-weight: 700;"><?= htmlspecialchars($mission['titre']) ?></h1>
                            <span style="background: rgba(255,74,87,0.2); color: #ff4a57; padding: 12px 25px; border-radius: 25px; font-size: 1.1rem; border: 2px solid #ff4a57; display: inline-block; margin-top: 15px;">
                                <i class="fas fa-rocket me-2"></i>Mission Gaming
                            </span>
                        </div>
                        
                        <!-- Mission Info -->
                        <div class="info-section" style="background: rgba(255,255,255,0.05); padding: 25px; border-radius: 10px; margin-bottom: 30px;">
                            <h4 style="color: #ff4a57; margin-bottom: 25px;">
                                <i class="fas fa-info-circle me-2"></i>Informations Mission
                            </h4>
                            <div class="info-item" style="display: flex; align-items: center; margin-bottom: 15px; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                                <i class="fas fa-gamepad" style="color: #ff4a57; width: 30px; font-size: 1.2rem;"></i>
                                <strong style="color: white; width: 120px;">Jeu :</strong>
                                <span style="color: rgba(255,255,255,0.8);"><?= htmlspecialchars($mission['jeu']) ?></span>
                            </div>
                            
                            <div class="info-item" style="display: flex; align-items: center; margin-bottom: 15px; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                                <i class="fas fa-tag" style="color: #ff4a57; width: 30px; font-size: 1.2rem;"></i>
                                <strong style="color: white; width: 120px;">Thème :</strong>
                                <span style="color: rgba(255,255,255,0.8);"><?= htmlspecialchars($mission['theme']) ?></span>
                            </div>
                            
                            <div class="info-item" style="display: flex; align-items: center; margin-bottom: 15px; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                                <i class="fas fa-fire" style="color: #ff4a57; width: 30px; font-size: 1.2rem;"></i>
                                <strong style="color: white; width: 120px;">Difficulté :</strong>
                                <span style="color: rgba(255,255,255,0.8);"><?= htmlspecialchars($mission['niveau_difficulte']) ?></span>
                            </div>
                            
                            <div class="info-item" style="display: flex; align-items: center; padding: 10px; background: rgba(255,255,255,0.03); border-radius: 8px;">
                                <i class="fas fa-calendar" style="color: #ff4a57; width: 30px; font-size: 1.2rem;"></i>
                                <strong style="color: white; width: 120px;">Dates :</strong>
                                <span style="color: rgba(255,255,255,0.8);">
                                    <?= date('d/m/Y', strtotime($mission['date_debut'])) ?> - <?= date('d/m/Y', strtotime($mission['date_fin'])) ?>
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="description-section" style="background: rgba(255,255,255,0.05); padding: 25px; border-radius: 10px; margin-bottom: 30px;">
                            <h4 style="color: #ff4a57; margin-bottom: 20px;">
                                <i class="fas fa-align-left me-2"></i>Description de la Mission
                            </h4>
                            <p class="mt-3" style="font-size: 1.1rem; line-height: 1.8; color: rgba(255,255,255,0.8);">
                                <?= nl2br(htmlspecialchars($mission['description'] ?? "Aucune description disponible.")) ?>
                            </p>
                        </div>

                        <!-- SECTION FEEDBACK -->
                        <div class="feedback-section" style="margin-top: 50px;">
                            <h4 style="color: #ff4a57; margin-bottom: 25px;">
                                <i class="fas fa-star me-2"></i>Avis sur la Mission
                            </h4>

                            <!-- Note moyenne -->
                            <div class="rating-summary" style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <div class="average-rating" style="font-size: 3rem; font-weight: bold; color: #ffd700;">
                                            <?= $averageRating ?>
                                        </div>
                                        <div class="stars" style="color: #ffd700; font-size: 1.2rem;">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?= $i <= floor($averageRating) ? '' : '-half-alt' ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div style="color: rgba(255,255,255,0.8); margin-top: 10px;">
                                            <?= $totalFeedbacks ?> avis
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <!-- Barres de progression pour chaque note -->
                                        <?php for ($star = 5; $star >= 1; $star--): ?>
                                            <?php
                                            $count = $feedbackStats[$star . '_star'] ?? 0;
                                            $percentage = $totalFeedbacks > 0 ? ($count / $totalFeedbacks) * 100 : 0;
                                            ?>
                                            <div class="rating-bar mb-2">
                                                <div class="d-flex align-items-center">
                                                    <span style="color: #ffd700; width: 20px;"><?= $star ?></span>
                                                    <i class="fas fa-star" style="color: #ffd700; margin: 0 10px;"></i>
                                                    <div class="progress flex-grow-1" style="height: 8px; background: rgba(255,255,255,0.1);">
                                                        <div class="progress-bar" style="width: <?= $percentage ?>%; background: #ffd700;"></div>
                                                    </div>
                                                    <span style="color: rgba(255,255,255,0.8); margin-left: 10px; min-width: 40px;">
                                                        <?= $count ?>
                                                    </span>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulaire de feedback (seulement pour les utilisateurs connectés) -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="feedback-form" style="background: rgba(255,255,255,0.05); padding: 25px; border-radius: 10px; margin-bottom: 30px;">
                                    <h5 style="color: #ff4a57; margin-bottom: 20px;">
                                        <?= $userFeedback ? 'Modifier votre avis' : 'Donner votre avis' ?>
                                    </h5>
                                    
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger" style="background: rgba(220,53,69,0.2); border: 1px solid #dc3545; color: #dc3545; padding: 12px; border-radius: 8px;">
                                            <?= $error ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($_GET['success'])): ?>
                                        <div class="alert alert-success" style="background: rgba(40,167,69,0.2); border: 1px solid #28a745; color: #28a745; padding: 12px; border-radius: 8px;">
                                            Votre avis a été enregistré avec succès !
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form method="POST">
                                        <!-- Système de rating -->
                                        <div class="rating-input mb-3">
                                            <label style="color: rgba(255,255,255,0.8); margin-bottom: 10px; display: block;">Votre note :</label>
                                            <div class="star-rating">
                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" 
                                                           <?= $userFeedback && $userFeedback['rating'] == $i ? 'checked' : '' ?> required>
                                                    <label for="star<?= $i ?>" title="<?= $i ?> étoiles">
                                                        <i class="fas fa-star"></i>
                                                    </label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>

                                        <!-- Commentaire -->
                                        <div class="mb-3">
                                            <label for="commentaire" style="color: rgba(255,255,255,0.8); margin-bottom: 10px; display: block;">
                                                Votre commentaire (optionnel) :
                                            </label>
                                            <textarea name="commentaire" id="commentaire" rows="4" 
                                                      placeholder="Partagez votre expérience avec cette mission..."
                                                      class="form-control" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; resize: none;"><?= $userFeedback ? htmlspecialchars($userFeedback['commentaire']) : '' ?></textarea>
                                        </div>

                                        <button type="submit" name="submit_feedback" class="btn-enhanced">
                                            <i class="fas fa-paper-plane me-2"></i>
                                            <?= $userFeedback ? 'Modifier mon avis' : 'Publier mon avis' ?>
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info text-center" style="background: rgba(0,123,255,0.1); border: 1px solid rgba(0,123,255,0.3); color: #8bb9ff; padding: 15px; border-radius: 8px;">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <a href="connexion.php?redirect=<?= urlencode("missiondetails.php?id=$id") ?>" style="color: #ff4a57; text-decoration: underline; font-weight: 600;">
                                        Connectez-vous
                                    </a> pour donner votre avis sur cette mission.
                                </div>
                            <?php endif; ?>

                            <!-- Liste des feedbacks -->
                            <?php if (!empty($feedbacks)): ?>
                                <div class="feedbacks-list">
                                    <h5 style="color: #ff4a57; margin-bottom: 20px;">Avis des participants (<?= $totalFeedbacks ?>)</h5>
                                    
                                    <?php foreach ($feedbacks as $feedback): ?>
                                        <div class="feedback-item" style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 10px; margin-bottom: 15px; border: 1px solid rgba(255,255,255,0.1);">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <strong style="color: white;"><?= htmlspecialchars($feedback['prenom'] . ' ' . $feedback['nom']) ?></strong>
                                                    <div class="stars" style="color: #ffd700; margin-top: 5px;">
                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                            <i class="fas fa-star<?= $i <= $feedback['rating'] ? '' : '-o' ?>" style="font-size: 0.9rem;"></i>
                                                        <?php endfor; ?>
                                                        <span style="color: rgba(255,255,255,0.8); margin-left: 8px; font-size: 0.9rem;">
                                                            (<?= $feedback['rating'] ?>/5)
                                                        </span>
                                                    </div>
                                                </div>
                                                <small style="color: rgba(255,255,255,0.8);">
                                                    <?= date('d/m/Y à H:i', strtotime($feedback['date_feedback'])) ?>
                                                </small>
                                            </div>
                                            
                                            <?php if (!empty($feedback['commentaire'])): ?>
                                                <p style="color: rgba(255,255,255,0.8); margin: 10px 0 0 0; line-height: 1.5; font-size: 0.95rem;">
                                                    "<?= nl2br(htmlspecialchars($feedback['commentaire'])) ?>"
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php elseif ($totalFeedbacks == 0): ?>
                                <div class="text-center" style="color: rgba(255,255,255,0.8); padding: 40px;">
                                    <i class="fas fa-comments fa-3x mb-3" style="opacity: 0.5;"></i>
                                    <p>Aucun avis pour le moment. Soyez le premier à donner votre feedback !</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- CTA Button -->
                        <div style="text-align: center; margin-top: 50px; padding: 40px; background: linear-gradient(135deg, rgba(255,74,87,0.1) 0%, rgba(77,138,255,0.05) 100%); border-radius: 15px; border: 2px solid #ff4a57;">
                            <h4 style="color: #ff4a57; margin-bottom: 20px; font-size: 1.5rem;">
                                🚀 Prêt à Relever le Défi ?
                            </h4>
                            <p style="color: rgba(255,255,255,0.8); margin-bottom: 30px; font-size: 1.1rem;">
                                Rejoignez cette mission unique et transformez votre passion en impact social.
                            </p>
                            
                            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; align-items: center;">
                                <a href="addcondidature.php?mission_id=<?= $mission['id'] ?>" 
                                   class="btn-enhanced">
                                   <i class="fas fa-paper-plane me-2"></i>Postuler Maintenant
                                </a>
                                
                                <a href="missionlist.php" class="btn-enhanced btn-enhanced-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Retour aux Missions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/chatbot.js"></script>

</body>
</html>