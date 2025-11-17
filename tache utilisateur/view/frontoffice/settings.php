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
$message_type = ''; 

// Récupérer les informations actuelles de l'utilisateur
$user_id = $_SESSION['user_id'];
$current_user = $utilisateurController->showUtilisateur($user_id);

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Mise à jour du profil
        $prenom = trim($_POST['prenom']);
        $nom = trim($_POST['nom']);
        $mail = trim($_POST['mail']);
        $num = trim($_POST['num']);
        
        // Validation des données
        if (empty($prenom) || empty($nom) || empty($mail)) {
            $message = "Tous les champs obligatoires doivent être remplis.";
            $message_type = 'error';
        } else {
            // Vérifier si l'email existe déjà pour un autre utilisateur
            if ($mail !== $current_user['mail'] && $utilisateurController->emailExists($mail)) {
                $message = "Cet email est déjà utilisé par un autre utilisateur.";
                $message_type = 'error';
            } else {
                // Vérifier si le numéro existe déjà pour un autre utilisateur
                if ($num && $num != $current_user['num'] && $utilisateurController->numExists($num)) {
                    $message = "Ce numéro de téléphone est déjà utilisé par un autre utilisateur.";
                    $message_type = 'error';
                } else {
                    try {
                        // Préparer la date de naissance
                        $dt_naiss = null;
                        if ($current_user['dt_naiss']) {
                            $dt_naiss = DateTime::createFromFormat('Y-m-d', $current_user['dt_naiss']);
                        }
                        
                        // Créer l'objet Utilisateur avec les nouvelles données
                        $utilisateur = new Utilisateur(
                            $user_id,
                            $prenom,
                            $nom,
                            $dt_naiss,
                            $mail,
                            $num ? intval($num) : null,
                            $current_user['mdp'], // Garder le mot de passe actuel
                            $current_user['typee']
                        );
                        
                        // Mettre à jour l'utilisateur
                        $utilisateurController->updateUtilisateur($utilisateur, $user_id);
                        
                        // Mettre à jour la session
                        $_SESSION['user_name'] = $prenom . ' ' . $nom;
                        $_SESSION['user_email'] = $mail;
                        
                        $message = "Profil mis à jour avec succès!";
                        $message_type = 'success';
                        
                        // Recharger les données utilisateur
                        $current_user = $utilisateurController->showUtilisateur($user_id);
                        
                    } catch (Exception $e) {
                        $message = "Erreur lors de la mise à jour: " . $e->getMessage();
                        $message_type = 'error';
                    }
                }
            }
        }
    }
    
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
                        $new_password, // Nouveau mot de passe (non hashé pour rester cohérent)
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
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Engage</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/settings.js"></script>
    <link rel="stylesheet" href="assets/css/settings.css">
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
                                        <!-- Vos liens de navigation -->
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

        <!-- Settings Content -->
        <section class="settings-section" style="padding: 100px 0;">
            <div class="container">
                <div class="settings-container">
                    <div class="settings-header">
                        <h1>Paramètres du compte</h1>
                        <p class="text-muted">Personnalisez votre expérience utilisateur</p>
                    </div>

                    <!-- Affichage des messages -->
                    <?php if ($message): ?>
                        <div class="alert-message <?php echo $message_type === 'success' ? 'alert-success' : 'alert-error'; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>

                    <ul class="nav settings-tabs" id="settingsTabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#general">Général</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="securite.php">Sécurité</a>
                        </li>
                        <li class="nav-item">
                            
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- Onglet Général -->
                        <div class="tab-pane fade show active" id="general">
                            <div class="settings-section">
                                <h3 class="section-title">Informations personnelles</h3>
                                <form method="POST" action="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Prénom *</label>
                                                <input type="text" class="form-control" name="prenom" 
                                                       value="<?php echo htmlspecialchars($current_user['prenom'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Nom *</label>
                                                <input type="text" class="form-control" name="nom" 
                                                       value="<?php echo htmlspecialchars($current_user['nom'] ?? ''); ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="mail" 
                                               value="<?php echo htmlspecialchars($current_user['mail'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" name="num" 
                                               value="<?php echo htmlspecialchars($current_user['num'] ?? ''); ?>" 
                                               placeholder="Votre numéro de téléphone">
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-save">
                                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Onglet Sécurité -->
                        <div class="tab-pane fade" id="security">
                            <div class="settings-section">
                                <h3 class="section-title">Sécurité du compte</h3>
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
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirmer le nouveau mot de passe *</label>
                                        <input type="password" class="form-control" name="confirm_password" 
                                               placeholder="Confirmez votre nouveau mot de passe" required>
                                    </div>
                                    <button type="submit" name="change_password" class="btn btn-save">
                                        <i class="fas fa-key me-2"></i>Changer le mot de passe
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Onglet Notifications -->
                        <div class="tab-pane fade" id="notifications">
                            <div class="settings-section">
                                <h3 class="section-title">Préférences de notification</h3>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <label class="form-label">Notifications par email</label>
                                            <p class="text-muted mb-0">Recevoir des notifications par email</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <label class="form-label">Notifications SMS</label>
                                            <p class="text-muted mb-0">Recevoir des notifications par SMS</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <label class="form-label">Nouvelles fonctionnalités</label>
                                            <p class="text-muted mb-0">Être informé des nouvelles fonctionnalités</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-save">
                                    <i class="fas fa-bell me-2"></i>Enregistrer les préférences
                                </button>
                            </div>
                        </div>
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
</body>
</html>