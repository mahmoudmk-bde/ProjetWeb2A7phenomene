<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Inclure les fichiers nécessaires
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../Model/utilisateur.php'; 

$utilisateurController = new UtilisateurController();
$message = '';
$message_type = ''; // success ou error

// Récupérer les informations actuelles de l'utilisateur
$user_id = $_SESSION['user_id'];
$current_user = $utilisateurController->showUtilisateur($user_id);

// Traitement du formulaire de sécurité
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        // Changement de mot de passe
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $message = "Tous les champs du mot de passe doivent être remplis.";
            $message_type = 'error';
        } elseif ($new_password !== $confirm_password) {
            $message = "Les nouveaux mots de passe ne correspondent pas.";
            $message_type = 'error';
        } elseif (strlen($new_password) < 6) {
            $message = "Le mot de passe doit contenir au moins 6 caractères.";
            $message_type = 'error';
        } else {
            // Vérifier le mot de passe actuel (comparaison directe car non hashé dans votre BD)
            if ($current_password === $current_user['mdp']) {
                try {
                    // Préparer la date de naissance
                    $dt_naiss = null;
                    if ($current_user['dt_naiss']) {
                        $dt_naiss = DateTime::createFromFormat('Y-m-d', $current_user['dt_naiss']);
                    }
                    
                    // Créer un nouvel objet Utilisateur avec le nouveau mot de passe
                    $utilisateur = new Utilisateur(
                        $user_id,
                        $current_user['prenom'],
                        $current_user['nom'],
                        $dt_naiss,
                        $current_user['mail'],
                        $current_user['num'],
                        $new_password, // Nouveau mot de passe
                        $current_user['typee']
                    );
                    
                    // Mettre à jour l'utilisateur
                    $utilisateurController->updateUtilisateur($utilisateur, $user_id);
                    
                    $message = "Mot de passe changé avec succès!";
                    $message_type = 'success';
                    
                    // Recharger les données utilisateur
                    $current_user = $utilisateurController->showUtilisateur($user_id);
                    
                } catch (Exception $e) {
                    $message = "Erreur lors du changement de mot de passe: " . $e->getMessage();
                    $message_type = 'error';
                }
            } else {
                $message = "Le mot de passe actuel est incorrect.";
                $message_type = 'error';
            }
        }
    }
    
    if (isset($_POST['update_security_settings'])) {
        // Mise à jour des paramètres de sécurité
        $two_factor = isset($_POST['two_factor']) ? 1 : 0;
        $session_timeout = $_POST['session_timeout'];
        $login_alerts = isset($_POST['login_alerts']) ? 1 : 0;
        
        try {
            // Ici vous devriez avoir une table pour les paramètres de sécurité
            // Pour l'instant, on va stocker dans la session ou dans un champ de l'utilisateur
            $_SESSION['security_settings'] = [
                'two_factor' => $two_factor,
                'session_timeout' => $session_timeout,
                'login_alerts' => $login_alerts
            ];
            
            $message = "Paramètres de sécurité mis à jour avec succès!";
            $message_type = 'success';
            
        } catch (Exception $e) {
            $message = "Erreur lors de la mise à jour des paramètres: " . $e->getMessage();
            $message_type = 'error';
        }
    }
    
    if (isset($_POST['update_security_questions'])) {
        // Mise à jour des questions de sécurité
        $question1 = trim($_POST['security_question1']);
        $answer1 = trim($_POST['security_answer1']);
        $question2 = trim($_POST['security_question2']);
        $answer2 = trim($_POST['security_answer2']);
        
        if (empty($question1) || empty($answer1) || empty($question2) || empty($answer2)) {
            $message = "Toutes les questions de sécurité doivent être remplies.";
            $message_type = 'error';
        } else {
            try {
                // Stocker les questions de sécurité dans la session
                // En production, vous devriez les stocker dans la base de données
                $_SESSION['security_questions'] = [
                    'question1' => $question1,
                    'answer1' => $answer1,
                    'question2' => $question2,
                    'answer2' => $answer2
                ];
                
                $message = "Questions de sécurité mises à jour avec succès!";
                $message_type = 'success';
                
            } catch (Exception $e) {
                $message = "Erreur lors de la mise à jour des questions: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sécurité - Engage</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/securite.css">
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/securite.js"></script>
</head>
<body>
    <div class="body_bg" style="background: #1f2235;">
        <!-- Header -->
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                            <a class="navbar-brand" href="index1.php">
                                <img src="assets/img/logo.png" alt="logo" />
                            </a>
                            <div class="collapse navbar-collapse main-menu-item">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        <!-- Liens de navigation -->
                                    </li>
                                </ul>
                            </div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="user-menu d-none d-sm-block">
                                    <div class="user-wrapper" onclick="toggleUserMenu()">
                                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                        <div class="user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                    <div class="user-dropdown" id="userDropdown">
                                        <a href="profile.php">
                                            <i class="fas fa-user"></i>Mon Profil
                                        </a>
                                        <a href="settings.php">
                                            <i class="fas fa-cog"></i>Paramètres
                                        </a>
                                        
                                        <a href="logout.php">
                                            <i class="fas fa-sign-out-alt"></i>Déconnexion
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <!-- Security Content -->
        <section class="security-section" style="padding: 100px 0;">
            <div class="container">
                <div class="security-container">
                    <div class="security-header">
                        <h1><i class="fas fa-shield-alt me-2"></i>Sécurité du compte</h1>
                        <p class="text-muted">Protégez votre compte avec ces paramètres de sécurité</p>
                    </div>

                    <!-- Affichage des messages -->
                    <?php if ($message): ?>
                        <div class="alert-message <?php echo $message_type === 'success' ? 'alert-success' : 'alert-error'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Section Changement de mot de passe -->
                    <div class="security-section">
                        <h3 class="section-title">
                            <i class="fas fa-key"></i>Changement de mot de passe
                        </h3>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label class="form-label">Mot de passe actuel *</label>
                                <input type="password" class="form-control" name="current_password" 
                                       placeholder="Entrez votre mot de passe actuel" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nouveau mot de passe *</label>
                                <input type="password" class="form-control" name="new_password" 
                                       placeholder="Entrez votre nouveau mot de passe" required>
                                <small class="text-muted">Le mot de passe doit contenir au moins 6 caractères</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirmer le nouveau mot de passe *</label>
                                <input type="password" class="form-control" name="confirm_password" 
                                       placeholder="Confirmez votre nouveau mot de passe" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Changer le mot de passe
                            </button>
                        </form>
                    </div>

                    <!-- Section Paramètres de sécurité -->
                    <div class="security-section">
                        <h3 class="section-title">
                            <i class="fas fa-cog"></i>Paramètres de sécurité
                        </h3>
                        <form method="POST" action="">
                            <div class="security-feature">
                                <div class="feature-info">
                                    <div class="feature-title">Authentification à deux facteurs</div>
                                    <div class="feature-description">
                                        Ajoutez une couche de sécurité supplémentaire à votre compte
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="two_factor" <?php echo isset($_SESSION['security_settings']['two_factor']) && $_SESSION['security_settings']['two_factor'] ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div class="security-feature">
                                <div class="feature-info">
                                    <div class="feature-title">Déconnexion automatique</div>
                                    <div class="feature-description">
                                        Déconnectez-vous automatiquement après une période d'inactivité
                                    </div>
                                </div>
                                <select class="form-select" name="session_timeout" style="width: 150px;">
                                    <option value="30" <?php echo (isset($_SESSION['security_settings']['session_timeout']) && $_SESSION['security_settings']['session_timeout'] == '30') ? 'selected' : ''; ?>>30 minutes</option>
                                    <option value="60" <?php echo (isset($_SESSION['security_settings']['session_timeout']) && $_SESSION['security_settings']['session_timeout'] == '60') ? 'selected' : ''; ?>>1 heure</option>
                                    <option value="120" <?php echo (isset($_SESSION['security_settings']['session_timeout']) && $_SESSION['security_settings']['session_timeout'] == '120') ? 'selected' : ''; ?>>2 heures</option>
                                    <option value="240" <?php echo (isset($_SESSION['security_settings']['session_timeout']) && $_SESSION['security_settings']['session_timeout'] == '240') ? 'selected' : ''; ?>>4 heures</option>
                                </select>
                            </div>

                            <div class="security-feature">
                                <div class="feature-info">
                                    <div class="feature-title">Alertes de connexion</div>
                                    <div class="feature-description">
                                        Recevez des notifications pour les nouvelles connexions
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="login_alerts" <?php echo isset($_SESSION['security_settings']['login_alerts']) && $_SESSION['security_settings']['login_alerts'] ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <button type="submit" name="update_security_settings" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Enregistrer les paramètres
                            </button>
                        </form>
                    </div>

                    <!-- Section Questions de sécurité -->
                    <div class="security-section">
                        <h3 class="section-title">
                            <i class="fas fa-question-circle"></i>Questions de sécurité
                        </h3>
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Question de sécurité 1 *</label>
                                        <select class="form-select" name="security_question1" required>
                                            <option value="">Choisissez une question</option>
                                            <option value="Quel est le nom de votre animal de compagnie ?">Quel est le nom de votre animal de compagnie ?</option>
                                            <option value="Quel est le nom de votre ville natale ?">Quel est le nom de votre ville natale ?</option>
                                            <option value="Quel est le nom de votre école primaire ?">Quel est le nom de votre école primaire ?</option>
                                            <option value="Quel est le métier de votre père ?">Quel est le métier de votre père ?</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Réponse 1 *</label>
                                        <input type="text" class="form-control" name="security_answer1" 
                                               placeholder="Votre réponse" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Question de sécurité 2 *</label>
                                        <select class="form-select" name="security_question2" required>
                                            <option value="">Choisissez une question</option>
                                            <option value="Quel est votre film préféré ?">Quel est votre film préféré ?</option>
                                            <option value="Quel est le nom de votre meilleur ami d'enfance ?">Quel est le nom de votre meilleur ami d'enfance ?</option>
                                            <option value="Quel est votre plat préféré ?">Quel est votre plat préféré ?</option>
                                            <option value="Quel est votre livre préféré ?">Quel est votre livre préféré ?</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Réponse 2 *</label>
                                        <input type="text" class="form-control" name="security_answer2" 
                                               placeholder="Votre réponse" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" name="update_security_questions" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Enregistrer les questions
                            </button>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <a href="index1.php" class="back-home">
                            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>