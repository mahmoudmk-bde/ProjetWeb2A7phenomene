<?php
require_once __DIR__ . '/../../../model/evenementModel.php';
require_once __DIR__ . '/../../../model/participationModel.php';
require_once __DIR__ . '/../../../db_config.php';
require_once __DIR__ . '/../../../controller/feedbackcontroller.php';
require_once __DIR__ . '/../../../controller/EventFeedbackController.php';
require_once __DIR__ . '/../../../controller/LikeController.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$eventModel = new EvenementModel();
$participationModel = new ParticipationModel();
$feedbackcontroller = new feedbackcontroller(); // missions
$eventFeedback = new EventFeedbackController(); // events
$likeController = new LikeController();

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
function normalize_asset_path($img) {
    if (empty($img)) return 'img/favicon.png';
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
function normalize_event_image($img) {
    return normalize_asset_path($img);
}

$message = '';
$alertType = 'info';
$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Si $event n'est pas déjà défini par le contrôleur
if (!isset($event)) {
    if ($event_id <= 0) {
        $message = 'Identifiant d’événement invalide.';
    } else {
        $event = $eventModel->getById($event_id);
        if (!$event) {
            $message = 'Événement introuvable.';
        } else {
            // Increment views (standalone access)
            @$eventModel->incrementViews($event_id);
            // Refresh event data from database to get updated vues count
            $event = $eventModel->getById($event_id);
        }
    }
} elseif (isset($event)) {
    // Si l'événement est passé par le contrôleur, s'assurer que event_id est correct
    $event_id = $event['id_evenement'];
    // Increment views on first load
    @$eventModel->incrementViews($event_id);
    // Refresh to get updated count
    $event = $eventModel->getById($event_id);
}

if (isset($event)) {
    $participants = $participationModel->getEventParticipants($event_id);
    $participantCount = $eventModel->countParticipants($event_id);
    $isRegistered = isset($_SESSION['user_id']) ? $participationModel->isUserRegistered($_SESSION['user_id'], $event_id) : false;
    $price = isset($event['prix']) ? (float)$event['prix'] : 0;
    $isPaidEvent = ($event['type_evenement'] === 'payant') && $price > 0;
    
    // Feedback & Rating System (events isolated from missions)
    $feedbackStats = $eventFeedback->getFeedbackStats($event_id);
    $averageRating = $feedbackStats['avg_rating'] ? round($feedbackStats['avg_rating'], 1) : 0;
    $totalFeedbacks = $feedbackStats['total_feedbacks'] ?? 0;
    $feedbacks = $eventFeedback->getFeedbacksByEvent($event_id);
    
    // Check if user already gave feedback
    $userFeedback = null;
    if (isset($_SESSION['user_id'])) {
        $userFeedback = $eventFeedback->getUserFeedback($event_id, $_SESSION['user_id']);
    }
    
    // Like system
    $isLiked = false;
    $likeCount = $likeController->getLikeCount($event_id);
    if (isset($_SESSION['user_id'])) {
        $isLiked = $likeController->hasUserLiked($event_id, $_SESSION['user_id']);
    }
} else {
    $participants = [];
    $participantCount = 0;
    $isRegistered = false;
    $price = 0;
    $isPaidEvent = false;
    $feedbackStats = ['avg_rating' => 0, 'total_feedbacks' => 0];
    $averageRating = 0;
    $totalFeedbacks = 0;
    $feedbacks = [];
    $userFeedback = null;
        $isLiked = false;
    $likeCount = 0;
}

// User-defined limit for sold out logic
$eventLimit = 50; // You can change this value


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($event) && $event) {
    $action = $_POST['action'];

    if ($action === 'guest_participate') {
        $prenom = trim(htmlspecialchars($_POST['prenom'] ?? '', ENT_QUOTES, 'UTF-8'));
        $nom = trim(htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'));
        $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
        $phone = trim(htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'));
        $ingameName = trim(htmlspecialchars($_POST['ingame_name'] ?? '', ENT_QUOTES, 'UTF-8'));
        $age = isset($_POST['age']) ? (int)$_POST['age'] : 0;
        $team = trim(htmlspecialchars($_POST['team'] ?? '', ENT_QUOTES, 'UTF-8'));

        // Validation du prénom
        if (empty($prenom) || strlen($prenom) < 2) {
            $message = 'Le prénom doit contenir au moins 2 caractères.';
            $alertType = 'danger';
        }
        // Validation du nom
        elseif (empty($nom) || strlen($nom) < 2) {
            $message = 'Le nom doit contenir au moins 2 caractères.';
            $alertType = 'danger';
        }
        // Validation de l'email
        elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Veuillez fournir un email valide.';
            $alertType = 'danger';
        }
        // Validation du téléphone
        elseif (empty($phone) || strlen($phone) < 8) {
            $message = 'Le numéro de téléphone doit contenir au moins 8 chiffres.';
            $alertType = 'danger';
        }
        // Validation du nom in-game
        elseif (empty($ingameName) || strlen($ingameName) < 2) {
            $message = 'Le nom in-game doit contenir au moins 2 caractères.';
            $alertType = 'danger';
        }
        // Validation de l'âge
        elseif ($age < 10 || $age > 100) {
            $message = 'L\'âge doit être entre 10 et 100 ans.';
            $alertType = 'danger';
        }
        else {
        $db = config::getConnexion();
        try {
            // Store additional info in session or database comment field
            $additionalInfo = json_encode([
                'phone' => $phone,
                'ingame_name' => $ingameName,
                'age' => $age,
                'team' => $team
            ]);
            
            // If user is already logged in, use their session id and skip lookup/creation
            if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
                $user_id = (int) $_SESSION['user_id'];
            } else {
                $stmt = $db->prepare('SELECT id_util FROM utilisateur WHERE mail = :email LIMIT 1');
                $stmt->execute([':email' => $email]);
                $row = $stmt->fetch();
                if ($row && isset($row['id_util'])) {
                    $user_id = (int)$row['id_util'];
                } else {
                    $ins = $db->prepare('INSERT INTO utilisateur (nom, prenom, mail) VALUES (:nom, :prenom, :email)');
                    $ins->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email]);
                    $user_id = (int)$db->lastInsertId();
                }
                // Persist session for future actions
                $_SESSION['user_id'] = $user_id;
            }

            if ($participationModel->isUserRegistered($user_id, $event_id)) {
                $message = 'Vous êtes déjà inscrit à cet événement.';
                $alertType = 'warning';
            } else {
                $created = $participationModel->create($event_id, $user_id, date('Y-m-d'), 'en attente', 1, null, "Tél: $phone | In-game: $ingameName | Âge: $age | Équipe: " . ($team ?: 'N/A'));
                if ($created) {
                    $message = 'Votre demande a été enregistrée et est en attente de validation.';
                    $alertType = 'success';
                } else {
                    $message = 'Impossible d’enregistrer la participation.';
                    $alertType = 'danger';
                }
            }
        } catch (Exception $e) {
            $message = 'Erreur lors de l’enregistrement : ' . $e->getMessage();
            $alertType = 'danger';
        }
        }
    } elseif ($action === 'pay_and_participate') {
        if (!$isPaidEvent) {
            $message = 'Cet événement ne nécessite pas de paiement.';
            $alertType = 'warning';
        } else {
            $prenom = trim(htmlspecialchars($_POST['prenom'] ?? '', ENT_QUOTES, 'UTF-8'));
            $nom = trim(htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8'));
            $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
            $quantite = isset($_POST['quantite']) ? (int) $_POST['quantite'] : 1;
            $cardNumber = preg_replace('/\D+/', '', $_POST['card_number'] ?? '');
            $cardExp = strtoupper(trim($_POST['card_exp'] ?? ''));
            $cardCvv = preg_replace('/\D+/', '', $_POST['card_cvv'] ?? '');

            // Validation du prénom
            if (empty($prenom) || strlen($prenom) < 2) {
                $message = 'Le prénom doit contenir au moins 2 caractères.';
                $alertType = 'danger';
            }
            // Validation du nom
            elseif (empty($nom) || strlen($nom) < 2) {
                $message = 'Le nom doit contenir au moins 2 caractères.';
                $alertType = 'danger';
            }
            // Validation de l'email
            elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Veuillez fournir un email valide.';
                $alertType = 'danger';
            }
            // Validation de la quantité
            elseif ($quantite < 1 || $quantite > 100) {
                $message = 'La quantité doit être entre 1 et 100.';
                $alertType = 'danger';
            }
            // Validation du numéro de carte
            elseif (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
                $message = 'Le numéro de carte doit contenir entre 13 et 19 chiffres.';
                $alertType = 'danger';
            }
            // Validation du format de date d'expiration
            elseif (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $cardExp)) {
                $message = 'La date d\'expiration doit être au format MM/AA.';
                $alertType = 'danger';
            } else {
                // Vérifier que la carte n'est pas expirée
                $month = (int)substr($cardExp, 0, 2);
                $year = (int)substr($cardExp, 3, 2) + 2000;
                $expiryDate = DateTime::createFromFormat('Y-m', sprintf('%04d-%02d', $year, $month));
                if (!$expiryDate || $expiryDate < new DateTime('first day of this month')) {
                    $message = 'La carte est expirée.';
                    $alertType = 'danger';
                }
                // Validation du CVV
                elseif (strlen($cardCvv) < 3 || strlen($cardCvv) > 4) {
                    $message = 'Le CVV doit contenir 3 ou 4 chiffres.';
                    $alertType = 'danger';
                } else {
                    $db = config::getConnexion();
                    try {
                        $stmt = $db->prepare('SELECT id_util FROM utilisateur WHERE mail = :email LIMIT 1');
                        $stmt->execute([':email' => $email]);
                        $row = $stmt->fetch();
                        if ($row && isset($row['id_util'])) {
                            $user_id = (int)$row['id_util'];
                        } else {
                            $ins = $db->prepare('INSERT INTO utilisateur (nom, prenom, mail) VALUES (:nom, :prenom, :email)');
                            $ins->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email]);
                            $user_id = (int)$db->lastInsertId();
                        }

                        // If user is logged in, use session id, otherwise find/create by email
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id']) {
                            $user_id = (int) $_SESSION['user_id'];
                        } else {
                            $stmt = $db->prepare('SELECT id_util FROM utilisateur WHERE mail = :email LIMIT 1');
                            $stmt->execute([':email' => $email]);
                            $row = $stmt->fetch();
                            if ($row && isset($row['id_util'])) {
                                $user_id = (int)$row['id_util'];
                            } else {
                                $ins = $db->prepare('INSERT INTO utilisateur (nom, prenom, mail) VALUES (:nom, :prenom, :email)');
                                $ins->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email]);
                                $user_id = (int)$db->lastInsertId();
                            }
                            $_SESSION['user_id'] = $user_id;
                        }

                        if ($participationModel->isUserRegistered($user_id, $event_id)) {
                            $message = 'Vous êtes déjà inscrit à cet événement.';
                            $alertType = 'warning';
                        } else {
                            $reference = 'PAY-' . strtoupper(substr(md5(uniqid('', true)), 0, 8));
                            $montant_total = $quantite * $price;
                            $created = $participationModel->create(
                                $event_id,
                                $user_id,
                                date('Y-m-d'),
                                'acceptée',
                                $quantite,
                                $montant_total,
                                'Carte bancaire',
                                $reference
                            );

                            if ($created) {
                                $message = 'Paiement confirmé ! Votre place est maintenant réservée.';
                                $alertType = 'success';
                            } else {
                                $message = 'Une erreur est survenue lors du paiement.';
                                $alertType = 'danger';
                            }
                        }
                    } catch (Exception $e) {
                        $message = 'Erreur de paiement : ' . $e->getMessage();
                        $alertType = 'danger';
                    }
                }
            }
        }
    }

        $participants = $participationModel->getEventParticipants($event_id);
        $isRegistered = isset($_SESSION['user_id']) ? $participationModel->isUserRegistered($_SESSION['user_id'], $event_id) : false;
}

// Handle feedback form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    if (isset($_SESSION['user_id'])) {
        $rating = intval($_POST['rating']);
        $commentaire = trim($_POST['commentaire']);
        
        if ($rating >= 1 && $rating <= 5) {
            if ($eventFeedback->addFeedback($event_id, $_SESSION['user_id'], $rating, $commentaire)) {
                header("Location: event_details.php?id=$event_id&success=1");
                exit();
            } else {
                $error = "Erreur lors de l'ajout du feedback";
            }
        } else {
            $error = "La note doit être entre 1 et 5";
        }
    } else {
        header("Location: connexion.php?redirect=" . urlencode("events/event_details.php?id=$event_id"));
        exit();
    }
}
?>
<?php require_once 'lang/lang_config.php'; ?>
<!doctype html>
<html lang="<?= get_current_lang() ?>" dir="<?= get_dir() ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= isset($event) ? htmlspecialchars($event['titre']) : 'Event'; ?></title>
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
    <style>
        /* New Event Page Styles */
        .ticket-sidebar {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            color: #333;
        }
        .ticket-sidebar-header {
            background-color: #1a1a1a;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            border-bottom: 2px solid #ff0000;
        }
        .ticket-sidebar-body {
            padding: 0;
        }
        .ticket-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .ticket-item:last-child {
            border-bottom: none;
        }
        .ticket-row-top {
            display: flex;
            justify-content: space-between;
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        .ticket-row-top i {
            margin-right: 5px;
        }
        .ticket-row-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .ticket-name {
            font-weight: bold;
            font-size: 1rem;
        }
        .ticket-price {
            font-weight: bold;
            font-size: 1.1rem;
        }
        .ticket-price sup {
            font-size: 0.7rem;
        }
        .sold-out-badge {
            background-color: #000;
            color: #fff;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: block;
            text-align: center;
            width: 100%;
        }
        
        /* Right Content Styles */
        .event-top-info-bar {
            display: flex;
            justify-content: space-between;
            background-color: #1a1a1a;
            padding: 20px 0;
            margin-bottom: 20px;
            border-radius: 4px;
            border-bottom: 1px solid #333;
        }
        .info-col {
            text-align: center;
            flex: 1;
            position: relative;
        }
        .info-col:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background-color: #333;
        }
        .info-icon {
            font-size: 2rem;
            color: #ff0000;
            margin-bottom: 10px;
        }
        .info-text {
            font-size: 0.9rem;
            color: #fff;
        }
        .event-main-image-container {
            width: 100%;
            overflow: hidden;
            border-radius: 4px;
        }
        .user-avatar-placeholder {
            font-size: 0.8rem;
        }
        
        <?php if (get_dir() === 'rtl'): ?>
        body { text-align: right; direction: rtl; }
        .navbar-nav { margin-right: auto; margin-left: 0 !important; }
        .dropdown-menu { text-align: right; }
        .main_menu .navbar .navbar-nav .nav-item .nav-link { padding: 33px 20px; }
        .text-right { text-align: left !important; } 
        .mr-3 { margin-left: 1rem !important; margin-right: 0 !important; }
        .info-col:not(:last-child)::after { left: 0; right: auto; }
        <?php endif; ?>
        
        /* Enhanced card and feedback styles */
        .enhanced-card {
            background: rgba(255,255,255,0.05);
            padding: 40px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .btn-like {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-like:hover {
            background: rgba(255, 74, 87, 0.2);
            border-color: #ff4a57;
            transform: translateY(-2px);
        }
        
        .btn-like.liked {
            background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%);
            border-color: #ff4a57;
        }
        
        .rating-summary {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .average-rating {
            font-size: 3rem;
            font-weight: bold;
            color: #ffd700;
        }
        
        .feedback-form {
            background: rgba(255,255,255,0.05);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
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
        
        .star-rating input:checked ~ label,
        .star-rating input:checked + label {
            color: #ffd700;
        }
        
        .feedback-item {
            background: rgba(255,255,255,0.03);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border: 1px solid rgba(255,255,255,0.1);
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
            cursor: pointer;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
            color: white;
            text-decoration: none;
        }
        
        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.2); }
            50% { transform: scale(1.1); }
        }

        /* ========== MODAL DARK THEME STYLING ========== */
        .modal.fade .modal-dialog {
            transition: transform 0.3s ease !important;
        }

        .modal-backdrop {
            background-color: rgba(0, 0, 0, 0.6) !important;
        }

        .modal-content {
            background-color: #2d3142 !important;
            border: 2px solid #ff4a57 !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7) !important;
        }
        
        .modal-header {
            background-color: #1f2235 !important;
            border-bottom: 2px solid #ff4a57 !important;
            padding: 25px !important;
        }
        
        .modal-title {
            color: #ffffff !important;
            font-weight: 700 !important;
            font-size: 1.4rem !important;
        }
        
        .modal-header .close {
            color: #ff4a57 !important;
            opacity: 1 !important;
            font-size: 1.8rem !important;
            text-shadow: none !important;
            line-height: 1 !important;
        }
        
        .modal-header .close:hover,
        .modal-header .close:focus {
            color: #ff6b7a !important;
            opacity: 1 !important;
        }
        
        .modal-body {
            padding: 35px !important;
            background-color: #2d3142 !important;
        }
        
        .modal-body form {
            margin: 0 !important;
        }
        
        .modal-body .form-group {
            margin-bottom: 22px !important;
        }
        
        .modal-body .form-group label {
            color: #ffffff !important;
            font-weight: 600 !important;
            margin-bottom: 10px !important;
            display: block !important;
            font-size: 0.95rem !important;
            letter-spacing: 0.3px !important;
        }
        
        .modal-body .text-danger {
            color: #ff4a57 !important;
        }
        
        .modal-body .form-control,
        .modal-body input[type="text"],
        .modal-body input[type="email"],
        .modal-body input[type="number"],
        .modal-body textarea,
        .modal-body select {
            background-color: #1f2235 !important;
            border: 2px solid #ff4a57 !important;
            color: #ffffff !important;
            padding: 14px 16px !important;
            font-size: 0.95rem !important;
            border-radius: 6px !important;
            transition: all 0.3s ease !important;
            height: auto !important;
            box-shadow: none !important;
        }
        
        .modal-body .form-control:focus,
        .modal-body input[type="text"]:focus,
        .modal-body input[type="email"]:focus,
        .modal-body input[type="number"]:focus,
        .modal-body textarea:focus,
        .modal-body select:focus {
            background-color: #1f2235 !important;
            border-color: #ff6b7a !important;
            color: #ffffff !important;
            box-shadow: 0 0 10px rgba(255, 74, 87, 0.5) !important;
            outline: none !important;
        }
        
        .modal-body .form-control::placeholder {
            color: #b0b3c1 !important;
            opacity: 0.7 !important;
        }
        
        .modal-body .alert {
            border-radius: 8px !important;
            margin-bottom: 20px !important;
        }
        
        .modal-body .alert-danger {
            background-color: rgba(255, 74, 87, 0.2) !important;
            border: 1.5px solid #ff4a57 !important;
            color: #ffb3b8 !important;
        }
        
        .modal-body .alert-secondary {
            background-color: #1f2235 !important;
            border: 2px solid #ff4a57 !important;
            color: #ffffff !important;
            padding: 18px !important;
            font-size: 1rem !important;
        }
        
        .modal-body .alert-secondary strong {
            color: #ff4a57 !important;
            font-size: 1.15rem !important;
        }

        .modal-body .row {
            margin: 0 -5px !important;
        }

        .modal-body .col-md-6 {
            padding: 0 5px !important;
        }

        .modal-body .text-right {
            text-align: right !important;
            margin-top: 25px !important;
        }

        .modal-body .btn-buy-now {
            background-color: #ff4a57 !important;
            color: #ffffff !important;
            border: none !important;
            padding: 14px 35px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            transition: all 0.3s ease !important;
            font-size: 0.95rem !important;
            cursor: pointer !important;
            min-width: 150px !important;
        }

        .modal-body .btn-buy-now:hover {
            background-color: #ff6b7a !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4) !important;
        }
    </style>
</head>

        <?php include __DIR__ . '/../header_common.php'; ?>

        <section class="profile-header">
            <div class="container">
                <div class="row align-items-center justify-content-between">
                    <div class="col-lg-8">
                        <div class="banner_text">
                            <div class="banner_text_iner">
                                <h1><?= isset($event) ? htmlspecialchars($event['titre']) : 'Event'; ?></h1>
                                <p><?= isset($event) ? htmlspecialchars($event['lieu']) : ''; ?></p>
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
                            <div class="alert alert-<?= htmlspecialchars($alertType) ?>"><?= htmlspecialchars($message) ?></div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($event) && $event): ?>
                    <?php
                        $img = normalize_event_image($event['image'] ?? null);
                        $accepted_count = (int) $eventModel->countParticipants($event_id);
                        $heure = !empty($event['heure_evenement']) ? substr($event['heure_evenement'], 0, 5) : '--:--';
                        $duree = isset($event['duree_minutes']) ? $event['duree_minutes'] . ' min' : '2 heures';
                        $isFull = $accepted_count >= $eventLimit;
                        
                        // Determine button status
                        $buttonText = $isPaidEvent ? 'Acheter' : 'Participer';
                        $buttonClass = 'btn-buy-now';
                        $buttonDisabled = false;
                        $badgeText = '';
                        $badgeClass = '';
                        
                        if ($isFull) {
                            $buttonDisabled = true;
                            if ($isPaidEvent) {
                                $badgeText = 'Sold Out';
                                $badgeClass = 'badge-sold-out';
                            } else {
                                $badgeText = 'Complet';
                                $badgeClass = 'badge-sold-out';
                            }
                        }
                    ?>
                <div class="row">
                    <!-- Left Sidebar: Billets -->
                    <div class="col-lg-4">
                        <div class="ticket-sidebar">
                            <div class="ticket-sidebar-header">
                                - BILLETS -
                            </div>
                            <div class="ticket-sidebar-body">
                                <div class="ticket-item">
                                    <div class="ticket-row-top">
                                        <div class="ticket-date"><i class="far fa-calendar-alt"></i> <?= !empty($event['date_evenement']) ? date('d/m/Y', strtotime($event['date_evenement'])) : '--/--/----' ?></div>
                                        <div class="ticket-time"><i class="far fa-clock"></i> <?= $heure ?></div>
                                    </div>
                                    <div class="ticket-row-main">
                                        <div class="ticket-name"><?= htmlspecialchars($event['titre']) ?></div>
                                        <div class="ticket-price">
                                            <?= $isPaidEvent ? number_format($price, 0) . '<sup>TND</sup>' : 'Gratuit' ?>
                                        </div>
                                    </div>
                                    <div class="ticket-row-bottom">
                                        <?php if ($isFull): ?>
                                            <span class="sold-out-badge"><?= $badgeText ?></span>
                                        <?php else: ?>
                                            <?php if (!$isRegistered): ?>
                                                <?php if ($isPaidEvent): ?>
                                                    <button type="button" class="btn-buy-now btn-sm btn-block" data-toggle="modal" data-target="#paymentModal">
                                                        Acheter
                                                    </button>
                                                <?php else: ?>
                                                    <button type="button" class="btn-buy-now btn-sm btn-block" data-toggle="modal" data-target="#participateModal">
                                                        Participer
                                                    </button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <div class="alert alert-success py-1 mb-0 text-center" style="font-size: 0.8rem;">Inscrit</div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <!-- Example of another ticket category if needed (commented out) -->
                                <!-- 
                                <div class="ticket-item disabled">
                                    <div class="ticket-row-top">...</div>
                                    <span class="sold-out-badge">Sold Out</span>
                                </div>
                                -->
                            </div>
                        </div>

                        <!-- Participants Section in Sidebar -->
                        <div class="game-detail-card mt-4 text-center" style="background-color: #1a1a1a; border-radius: 4px; padding: 15px;">
                            <div class="card-header bg-transparent border-0 px-0">
                                <h5 class="mb-3">Participants (<?= $accepted_count ?>/<?= $eventLimit ?>)</h5>
                            </div>
                            <?php if (!empty($participants)): ?>
                                <div class="list-group list-group-flush mt-2" style="max-height: 300px; overflow-y: auto;">
                                    <?php foreach ($participants as $p): ?>
                                        <div class="list-group-item bg-transparent px-0 py-2 border-bottom-0">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <div class="mr-3">
                                                    <div class="user-avatar-placeholder rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 12px;">
                                                        <?= strtoupper(substr($p['prenom'], 0, 1) . substr($p['nom'], 0, 1)) ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold" style="font-size: 0.9rem; color: #fff;"><?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-muted small">Soyez le premier à participer !</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Content: Event Details -->
                    <div class="col-lg-8">
                        <div class="event-top-info-bar">
                            <div class="info-col">
                                <div class="info-icon"><i class="fas fa-building"></i></div>
                                <div class="info-text"><?= htmlspecialchars($event['lieu']) ?></div>
                            </div>
                            <div class="info-col">
                                <div class="info-icon"><i class="fas fa-hourglass-half"></i></div>
                                <div class="info-text"><?= $duree ?></div>
                            </div>
                            <div class="info-col">
                                <div class="info-icon"><i class="far fa-calendar-alt"></i></div>
                                <div class="info-text"><?= !empty($event['date_evenement']) ? date('d F Y', strtotime($event['date_evenement'])) : '' ?></div>
                            </div>
                            <div class="info-col">
                                <div class="info-icon"><i class="far fa-clock"></i></div>
                                <div class="info-text"><?= $heure ?></div>
                            </div>
                        </div>

                        <div class="event-main-image-container">
                            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($event['titre']) ?>" class="img-fluid w-100">
                        </div>

                        <div class="event-description mt-4">
                            <h3 class="mb-3"><?= htmlspecialchars($event['titre']) ?></h3>
                            <p class="description-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                            
                            <div class="mt-3">
                                <span class="badge badge-light">Vue(s): <?= $event['vues'] ?? 0 ?></span>
                                <span class="badge badge-light">Thème: <?= theme_label($event['id_organisation'], $themeMap) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="single_war_text text-center">
                                <h4><?= htmlspecialchars($message ?: 'Event not found'); ?></h4>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- FEEDBACK SECTION -->
        <section class="section_padding" style="background: linear-gradient(135deg, #1f2235 0%, #2d325a 100%); padding: 50px 0;">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 mx-auto">
                        <!-- Like and Share Buttons -->
                        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; margin-bottom: 40px;">
                            <button id="likeBtn" class="btn-like <?= $isLiked ? 'liked' : '' ?>" 
                                    data-event-id="<?= $event_id ?>"
                                    <?= !isset($_SESSION['user_id']) ? 'onclick="alert(\'Vous devez être connecté pour liker\')"' : '' ?>>
                                <i class="fas fa-heart"></i>
                                <span id="likeCount"><?= $likeCount ?></span>
                                <span class="like-text">J'aime</span>
                            </button>
                        </div>

                        <!-- FEEDBACK SECTION -->
                        <div class="feedback-section">
                            <h4 style="color: #ff4a57; margin-bottom: 25px; text-align: center;">
                                <i class="fas fa-star me-2"></i>Avis sur l'Événement
                            </h4>

                            <!-- Note moyenne -->
                            <div class="rating-summary">
                                <div class="row align-items-center">
                                    <div class="col-md-4 text-center">
                                        <div class="average-rating">
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
                                <div class="feedback-form">
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
                                            <label style="color: rgba(255,255,255,0.8); margin-bottom: 10px; display: block; font-weight: 600;">Votre note :</label>
                                            <div class="star-rating">
                                                <?php for ($i = 5; $i >= 1; $i--): ?>
                                                    <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" 
                                                           <?= $userFeedback && $userFeedback['rating'] == $i ? 'checked' : '' ?> >
                                                    <label for="star<?= $i ?>" title="<?= $i ?> étoiles">
                                                        <i class="fas fa-star"></i>
                                                    </label>
                                                <?php endfor; ?>
                                            </div>
                                        </div>

                                        <!-- Commentaire -->
                                        <div class="mb-3">
                                            <label for="commentaire" style="color: rgba(255,255,255,0.8); margin-bottom: 10px; display: block; font-weight: 600;">
                                                Votre commentaire (optionnel) :
                                            </label>
                                            <textarea name="commentaire" id="commentaire" rows="4" 
                                                      placeholder="Partagez votre expérience avec cet événement..."
                                                      class="form-control" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.25); color: white; resize: none; padding: 12px; font-family: inherit;"><?= $userFeedback ? htmlspecialchars($userFeedback['commentaire']) : '' ?></textarea>
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
                                    <a href="connexion.php?redirect=<?= urlencode("events/event_details.php?id=$event_id") ?>" style="color: #ff4a57; text-decoration: underline; font-weight: 600;">
                                        Connectez-vous
                                    </a> pour donner votre avis sur cet événement.
                                </div>
                            <?php endif; ?>

                            <!-- Liste des feedbacks -->
                            <?php if (!empty($feedbacks)): ?>
                                <div class="feedbacks-list" style="margin-top: 30px;">
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
                    </div>
                </div>
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
    <script src="js/participant_validate.js"></script>
    <script src="js/custom.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const quantityInput = document.getElementById('ticketQuantity');
        const quantityButtons = document.querySelectorAll('.quantity-btn');
        const totalLabel = document.getElementById('ticketTotal');
        const hiddenQuantity = document.getElementById('paymentQuantity');
        const summary = document.getElementById('paymentSummary');
        const ticketPrice = parseFloat(<?= json_encode($price) ?>) || 0;
        const paymentForm = document.getElementById('payment-form');
        const paymentErrors = document.getElementById('payment-errors');

        function updateTotals() {
            if (!quantityInput) return;
            let qty = parseInt(quantityInput.value, 10);
            if (isNaN(qty) || qty < 1) qty = 1;
            quantityInput.value = qty;
            if (hiddenQuantity) hiddenQuantity.value = qty;
            const total = (qty * ticketPrice).toFixed(2) + ' TND';
            if (totalLabel) totalLabel.textContent = total;
            if (summary) summary.innerHTML = 'Total: <strong>' + total + '</strong>';
        }

        if (quantityButtons.length && quantityInput) {
            quantityButtons.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const direction = btn.dataset.direction;
                    let value = parseInt(quantityInput.value, 10) || 1;
                    if (direction === 'up') value += 1;
                    if (direction === 'down') value = Math.max(1, value - 1);
                    quantityInput.value = value;
                    updateTotals();
                });
            });
            quantityInput.addEventListener('input', updateTotals);
            updateTotals();
        }

        $('#paymentModal').on('shown.bs.modal', function () {
            updateTotals();
            if (paymentErrors) {
                paymentErrors.classList.add('d-none');
                paymentErrors.innerHTML = '';
            }
        });

        if (paymentForm) {
            paymentForm.addEventListener('submit', function (e) {
                const errors = [];
                const prenom = paymentForm.pay_prenom.value.trim();
                const nom = paymentForm.pay_nom.value.trim();
                const email = paymentForm.pay_email.value.trim();
                const cardNumber = paymentForm.card_number.value.replace(/\D+/g, '');
                const cardExp = paymentForm.card_exp.value.trim().toUpperCase();
                const cardCvv = paymentForm.card_cvv.value.replace(/\D+/g, '');
                const qty = parseInt(hiddenQuantity ? hiddenQuantity.value : '1', 10) || 1;

                if (prenom.length < 2) errors.push('Le prénom doit contenir au moins 2 caractères.');
                if (nom.length < 2) errors.push('Le nom doit contenir au moins 2 caractères.');
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Adresse email invalide.');
                if (cardNumber.length < 13 || cardNumber.length > 19) errors.push('Le numéro de carte doit contenir entre 13 et 19 chiffres.');
                const expMatch = cardExp.match(/^(0[1-9]|1[0-2])\/(\d{2})$/);
                if (!expMatch) {
                    errors.push('La date d\'expiration doit être au format MM/AA.');
                }
                if (cardCvv.length < 3 || cardCvv.length > 4) errors.push('Le CVV doit contenir 3 ou 4 chiffres.');
                if (qty < 1) errors.push('La quantité doit être supérieure ou égale à 1.');

                if (errors.length) {
                    e.preventDefault();
                    if (paymentErrors) {
                        paymentErrors.innerHTML = errors.join('<br>');
                        paymentErrors.classList.remove('d-none');
                    } else {
                        alert(errors.join('\n'));
                    }
                } else if (paymentErrors) {
                    paymentErrors.classList.add('d-none');
                    paymentErrors.innerHTML = '';
                }
            });
        }
    });
    </script>

    <!-- Modal d'inscription gratuite -->
    <div class="modal fade" id="participateModal" tabindex="-1" role="dialog" aria-labelledby="participateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="participateModalLabel">Participer à <?= htmlspecialchars($event['titre'] ?? 'cet événement') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="participation-errors" class="alert alert-danger d-none"></div>
                    <form method="post" id="participant-modal-form" novalidate>
                        <input type="hidden" name="action" value="guest_participate">
                        <div class="form-group">
                            <label for="modal_prenom">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" id="modal_prenom" class="form-control" placeholder="Votre prénom" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_nom">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" id="modal_nom" class="form-control" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="modal_email" class="form-control" placeholder="votremail@example.com" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_phone">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="modal_phone" class="form-control" placeholder="+216 12 345 678" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_ingame_name">Nom in-game <span class="text-danger">*</span></label>
                            <input type="text" name="ingame_name" id="modal_ingame_name" class="form-control" placeholder="Votre pseudo de jeu" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_age">Âge <span class="text-danger">*</span></label>
                            <input type="number" name="age" id="modal_age" class="form-control" placeholder="Votre âge" min="10" max="100" required>
                        </div>
                        <div class="form-group">
                            <label for="modal_team">Nom d'équipe (si applicable)</label>
                            <input type="text" name="team" id="modal_team" class="form-control" placeholder="Nom de votre équipe">
                        </div>
                        <div class="text-right" style="margin-top: 20px;">
                            <button type="submit" class="btn-buy-now">Envoyer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de paiement -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Paiement - <?= htmlspecialchars($event['titre'] ?? '') ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="post" id="payment-form" novalidate>
                        <input type="hidden" name="action" value="pay_and_participate">
                        <input type="hidden" name="quantite" id="paymentQuantity" value="1">
                        <div id="payment-errors" class="alert alert-danger d-none"></div>
                        <div class="form-group">
                            <label for="pay_prenom">Prénom</label>
                            <input type="text" name="prenom" id="pay_prenom" class="form-control" placeholder="Votre prénom">
                        </div>
                        <div class="form-group">
                            <label for="pay_nom">Nom</label>
                            <input type="text" name="nom" id="pay_nom" class="form-control" placeholder="Votre nom">
                        </div>
                        <div class="form-group">
                            <label for="pay_email">Email</label>
                            <input type="email" name="email" id="pay_email" class="form-control" placeholder="votremail@example.com">
                        </div>
                        <div class="form-group">
                            <label for="card_number">Numéro de carte</label>
                            <input type="text" name="card_number" id="card_number" class="form-control" placeholder="1234 5678 9012 3456">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="card_exp">Expiration</label>
                                    <input type="text" name="card_exp" id="card_exp" class="form-control" placeholder="MM/AA">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="card_cvv">CVV</label>
                                    <input type="text" name="card_cvv" id="card_cvv" class="form-control" placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-secondary" id="paymentSummary" style="margin-top: 20px;">
                            Total: <strong><?= number_format(max(1, $price), 2) ?> TND</strong>
                        </div>
                        <div class="text-right" style="margin-top: 20px;">
                            <button type="submit" class="btn-buy-now">Confirmer le paiement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const participantForm = document.getElementById('participant-modal-form');
    const participationErrors = document.getElementById('participation-errors');

    if (!participantForm) return;

    participantForm.addEventListener('submit', function (e) {
        const prenom = (participantForm.prenom.value || '').trim();
        const nom = (participantForm.nom.value || '').trim();
        const email = (participantForm.email.value || '').trim();
        const phone = (participantForm.phone.value || '').trim();
        const ingameName = (participantForm.ingame_name.value || '').trim();
        const age = parseInt(participantForm.age.value || '0', 10);
        const errors = [];

        if (prenom.length < 2) errors.push('Le prénom doit contenir au moins 2 caractères.');
        if (nom.length < 2) errors.push('Le nom doit contenir au moins 2 caractères.');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Adresse email invalide.');
        if (phone.length < 8) errors.push('Le numéro de téléphone doit contenir au moins 8 chiffres.');
        if (ingameName.length < 2) errors.push('Le nom in-game doit contenir au moins 2 caractères.');
        if (age < 10 || age > 100) errors.push('L\'âge doit être entre 10 et 100 ans.');

        if (errors.length) {
            e.preventDefault();
            if (participationErrors) {
                participationErrors.innerHTML = errors.join('<br>');
                participationErrors.classList.remove('d-none');
            } else {
                alert(errors.join('\n'));
            }
        } else if (participationErrors) {
            participationErrors.classList.add('d-none');
            participationErrors.innerHTML = '';
        }
    });

    // Like Button Handler
    const likeBtn = document.getElementById('likeBtn');
    if (likeBtn && likeBtn.getAttribute('data-event-id')) {
        likeBtn.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            const formData = new FormData();
            formData.append('event_id', eventId);

            fetch('../../controller/LikeController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeBtn.classList.toggle('liked');
                    document.getElementById('likeCount').textContent = data.count;
                } else if (data.error === 'not_logged_in') {
                    alert('Vous devez être connecté pour liker cet événement');
                }
            })
            .catch(error => console.error('Like error:', error));
        });
    }

    // Copy to Clipboard Function
    function copyToClipboard() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const toast = document.createElement('div');
            toast.className = 'toast show';
            toast.textContent = '✅ Lien copié dans le presse-papiers!';
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }).catch(err => alert('Erreur lors de la copie du lien'));
    }
});
</script>
</body>
</html>
