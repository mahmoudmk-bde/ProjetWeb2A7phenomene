<?php
session_start();
require_once '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../Model/utilisateur.php';
require_once '../../controller/condidaturecontroller.php';
require_once __DIR__ . '/../../Model/condidature.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Vérifier si l'utilisateur est un volontaire (pas admin)
$userType = strtolower(trim($_SESSION['user_type'] ?? ''));
if ($userType === 'admin') {
    header('Location: missionlist.php?error=Les administrateurs ne peuvent pas postuler aux missions');
// Vérifier si l'utilisateur est un volontaire
if ($_SESSION['user_type'] !== 'volontaire') {
    header('Location: missionlist.php?error=Seuls les volontaires peuvent postuler aux missions');
    exit();
}

$mission_id = $_GET['mission_id'] ?? null;
if (!$mission_id) {
    header('Location: missionlist.php');
    exit();
}

$utilisateurController = new UtilisateurController();
$condidatureController = new CondidatureController();

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$current_user = $utilisateurController->showUtilisateur($user_id);

$message = '';
$message_type = '';

// Traitement du formulaire de candidature
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo_gaming = trim($_POST['pseudo_gaming']);
    $niveau_experience = trim($_POST['niveau_experience']);
    $disponibilites = trim($_POST['disponibilites']);
    $email = trim($_POST['email']);
    
    // Validation des données
    if (empty($pseudo_gaming) || empty($niveau_experience) || empty($disponibilites) || empty($email)) {
        $message = "Veuillez remplir tous les champs obligatoires.";
        $message_type = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Veuillez entrer une adresse email valide.";
        $message_type = 'error';
    } else {
        try {
            // Vérifier si l'utilisateur a déjà postulé à cette mission
            $existingApplication = $condidatureController->checkExistingApplication($user_id, $mission_id);
            
            if ($existingApplication) {
                $message = "Vous avez déjà postulé à cette mission.";
                $message_type = 'error';
            } else {
                // CORRECTION : S'assurer que l'ID utilisateur est bien passé
                $candidatureData = [
<<<<<<< HEAD
                    'id_util' => $user_id, // CORRECTION ICI
                    'id_mission' => $mission_id,
=======
                    'utilisateur_id' => $user_id, // Utiliser utilisateur_id pour correspondre au controller
                    'mission_id' => $mission_id,
>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151
                    'pseudo_gaming' => $pseudo_gaming,
                    'niveau_experience' => $niveau_experience,
                    'disponibilites' => $disponibilites,
                    'email' => $email,
                    
                ];

                // DEBUG : Afficher les données pour vérification
                error_log("Données candidature: " . print_r($candidatureData, true));

                // Utiliser directement la méthode addCondidature du controller
                $result = $condidatureController->addCondidature($candidatureData);

                if ($result) {
                    $message = "Votre candidature a été envoyée avec succès!";
                    $message_type = 'success';
                    
                    // Réinitialiser le formulaire
                    $_POST = array();
                } else {
                    $message = "Erreur lors de l'envoi de la candidature. Veuillez réessayer.";
                    $message_type = 'error';
                }
            }
        } catch (Exception $e) {
            $message = "Erreur: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<<<<<<< HEAD

<head>
    <meta charset="UTF-8">
    <title>Postuler – ENGAGE</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/mission.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
    <style>
=======
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postuler – ENGAGE</title>
    <link rel="icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mission.css">
    <link rel="stylesheet" href="assets/css/custom-frontoffice.css">
    <style>
        :root {
            --primary: #ff4a57;
            --primary-light: #ff6b6b;
            --dark: #1f2235;
            --dark-light: #2d325a;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.8);
        }

        .body_bg {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            min-height: 100vh;
        }

>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151
        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
<<<<<<< HEAD
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 5px;
            z-index: 1000;
            margin-top: 10px;
=======
            min-width: 220px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 12px;
            z-index: 1000;
            margin-top: 10px;
            overflow: hidden;
>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151
        }
        
        .user-dropdown.show {
            display: block;
<<<<<<< HEAD
        }
        
        .user-dropdown a {
            display: block;
            padding: 12px 16px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
=======
            animation: fadeIn 0.3s ease;
        }
        
        .user-dropdown a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151
            font-size: 14px;
        }
        
        .user-dropdown a:hover {
            background: #f8f9fa;
<<<<<<< HEAD
            color: #007bff;
=======
            color: var(--primary);
            transform: translateX(5px);
>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151
        }
        
        .user-dropdown a:last-child {
            border-bottom: none;
            color: #dc3545;
        }
        
        .user-dropdown a:last-child:hover {
            background: #dc3545;
            color: white;
        }
        
        .user-wrapper {
            display: flex;
            align-items: center;
<<<<<<< HEAD
            gap: 10px;
            color: white;
            cursor: pointer;
        }
        
        .user-name {
            font-weight: bold;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255,255,255,0.3);
=======
            gap: 12px;
            color: white;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .user-wrapper:hover {
            background: rgba(255,255,255,0.1);
        }
        
        .user-name {
            font-weight: 600;
            font-size: 14px;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid rgba(255,255,255,0.3);
>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            border-color: rgba(255,255,255,0.6);
            transform: scale(1.05);
        }
        
        .user-avatar i {
            color: white;
<<<<<<< HEAD
            font-size: 18px;
=======
            font-size: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151
        }
        
        .apply-form-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            color: #fff;
            font-weight: 600;
            margin-bottom: 10px;
            display: block;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #ff4a57;
            box-shadow: 0 0 0 0.2rem rgba(255, 74, 87, 0.25);
            color: #fff;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .alert-message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid #28a745;
            color: #28a745;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        
        .btn-enhanced {
            background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
            color: white;
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
        
        .user-info-badge {
            background: rgba(255, 74, 87, 0.2);
            border: 1px solid #ff4a57;
            color: #ff4a57;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
<div class="body_bg">
    
<<<<<<< HEAD
    <!-- Header avec menu utilisateur -->
    <header class="main_menu single_page_menu">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12">
                    <nav class="navbar navbar-expand-lg navbar-light">
                        <a class="navbar-brand" href="index.php">
                            <img src="assets/img/logo.png" alt="logo" />
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

                        <!-- Menu utilisateur -->
                        <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])): ?>
                            <div class="user-menu d-none d-sm-block">
                                <div class="user-wrapper" onclick="toggleUserMenu()">
                                    <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                    <div class="user-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                                <div class="user-dropdown" id="userDropdown">
                                    <a href="profile.php">
                                        <i class="fas fa-user me-2"></i>Mon Profil
                                    </a>
                                    <a href="settings.php">
                                        <i class="fas fa-cog me-2"></i>Paramètres
                                    </a>
                                    <a href="securite.php">
                                        <i class="fas fa-shield-alt me-2"></i>Sécurité
                                    </a>
                                    <a href="logout.php">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="connexion.php" class="btn_1 d-none d-sm-block">Se connecter</a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
        </div>
    </header>
=======
    <?php include 'header_mission.php'; ?>
>>>>>>> f017e9e428e3b9cf04b0e7fbea8e2094a445a151

    <!-- Breadcrumb -->
    <section class="breadcrumb_bg">
        <div class="container">
            <div class="breadcrumb_iner_item text-center">
                <h1 class="design-title">📝 Postuler à la Mission</h1>
                <p class="design-subtitle">Rejoignez l'aventure et faites la différence</p>
            </div>
        </div>
    </section>

    <!-- Application Form -->
    <section class="section_padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="apply-form-container">
                        
                        <!-- Info utilisateur connecté -->
                        <div class="user-info-badge">
                            <i class="fas fa-user me-2"></i>
                            Connecté en tant : <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>
                            (<?php echo htmlspecialchars($_SESSION['user_type']); ?>)
                        </div>

                        <!-- Messages d'alerte -->
                        <?php if ($message): ?>
                            <div class="alert-message <?php echo $message_type === 'success' ? 'alert-success' : 'alert-error'; ?>">
                                <i class="fas <?php echo $message_type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-2"></i>
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>

                        <div class="text-center mb-5">
                            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                <i class="fas fa-file-alt fa-2x" style="color: white;"></i>
                            </div>
                            <h3 class="game-title-main">🎮 Candidature</h3>
                            <p class="game-partner" style="font-size: 1.1rem; color: rgba(255,255,255,0.8);">
                                Remplissez ce formulaire pour rejoindre l'aventure ENGAGE
                            </p>
                        </div>

                        <form id="condidature-form" method="POST" action="">
                            <input type="hidden" name="mission_id" value="<?= htmlspecialchars($mission_id) ?>">

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user me-2"></i>Pseudo Gaming *
                                </label>
                                <input type="text" name="pseudo_gaming" class="form-control" required 
                                       placeholder="Votre pseudo gaming préféré"
                                       value="<?= isset($_POST['pseudo_gaming']) ? htmlspecialchars($_POST['pseudo_gaming']) : '' ?>">
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-chart-line me-2"></i>Niveau d'expérience *
                                </label>
                                <select name="niveau_experience" class="form-control" required>
                                    <option value="" disabled selected>Choisissez votre niveau</option>
                                    <option value="débutant" <?= (isset($_POST['niveau_experience']) && $_POST['niveau_experience'] === 'débutant') ? 'selected' : '' ?>>🎮 Débutant - Je découvre le jeu</option>
                                    <option value="intermédiaire" <?= (isset($_POST['niveau_experience']) && $_POST['niveau_experience'] === 'intermédiaire') ? 'selected' : '' ?>>⚡ Intermédiaire - Je me débrouille bien</option>
                                    <option value="avancé" <?= (isset($_POST['niveau_experience']) && $_POST['niveau_experience'] === 'avancé') ? 'selected' : '' ?>>🔥 Avancé - Je maîtrise le jeu</option>
                                    <option value="expert" <?= (isset($_POST['niveau_experience']) && $_POST['niveau_experience'] === 'expert') ? 'selected' : '' ?>>🏆 Expert - Je suis parmi les meilleurs</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-2"></i>Disponibilités *
                                </label>
                                <input type="text" name="disponibilites" class="form-control" required 
                                       placeholder="ex: 3 soirs par semaine, weekends, vacances..."
                                       value="<?= isset($_POST['disponibilites']) ? htmlspecialchars($_POST['disponibilites']) : '' ?>">
                                <small class="form-text" style="color: rgba(255,255,255,0.6);">Indiquez vos créneaux de disponibilité pour cette mission</small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-envelope me-2"></i>Email *
                                </label>
                                <input type="email" name="email" class="form-control" required 
                                       placeholder="votre.email@exemple.com"
                                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : (isset($current_user['mail']) ? htmlspecialchars($current_user['mail']) : '') ?>">
                            </div>

                            

                            <div class="text-center mt-5">
                                <button type="submit" class="btn-enhanced" style="width: 100%; max-width: 400px;">
                                    <i class="fas fa-paper-plane me-2"></i>Envoyer ma Candidature
                                </button>
                                <a href="missionlist.php" class="btn-enhanced btn-enhanced-secondary mt-3" style="display: inline-block;">
                                    <i class="fas fa-arrow-left me-2"></i>Retour aux Missions
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<script src="assets/js/jquery-1.12.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script>
    // Fonction pour le menu utilisateur
    function toggleUserMenu() {
        const dropdown = document.getElementById('userDropdown');
        dropdown.classList.toggle('show');
    }
    
    document.addEventListener('click', function(event) {
        const userMenu = document.querySelector('.user-menu');
        const dropdown = document.getElementById('userDropdown');
        
        if (!userMenu.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    // Validation du formulaire
    document.getElementById('condidature-form').addEventListener('submit', function(e) {
        const pseudo = document.querySelector('input[name="pseudo_gaming"]').value.trim();
        const niveau = document.querySelector('select[name="niveau_experience"]').value;
        const disponibilites = document.querySelector('input[name="disponibilites"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        
        if (!pseudo || !niveau || !disponibilites || !email) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
        
        if (!validateEmail(email)) {
            e.preventDefault();
            alert('Veuillez entrer une adresse email valide.');
            return false;
        }
        
        const btn = document.querySelector('button[type="submit"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
        btn.disabled = true;
    });
    
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
</script>

</body>
</html>