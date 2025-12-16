<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Inclure les fichiers nécessaires
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../model/utilisateur.php'; 
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
        $confirm_password = trim($_POST['confirm_password'] ?? '');
        
        // Validation des données
        if (empty($prenom) || empty($nom) || empty($mail) || empty($confirm_password)) {
            $message = "Tous les champs obligatoires doivent être remplis, y compris le mot de passe de confirmation.";
            $message_type = 'error';
        } elseif ($confirm_password !== $current_user['mdp']) {
            $message = "Le mot de passe de confirmation est incorrect.";
            $message_type = 'error';
        } else {
            // L'email ne peut pas être modifié, on garde toujours l'email actuel
            $mail = $current_user['mail'];
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
                            $current_user['typee'],
                            $current_user['q1'],
                            $current_user['rp1'],
                            $current_user['q2'],
                            $current_user['rp2']
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
                        $current_user['typee'],
                        $current_user['q1'],
                        $current_user['rp1'],
                        $current_user['q2'],
                        $current_user['rp2']
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
    <link rel="icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/settings.css">
    <style>
        :root {
            --primary: #ff4a57;
            --primary-light: #ff6b6b;
            --dark: #1f2235;
            --dark-light: #2d325a;
            --text: #ffffff;
            --text-light: rgba(255,255,255,0.8);
        }

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
            min-width: 220px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border-radius: 12px;
            z-index: 1000;
            margin-top: 10px;
            overflow: hidden;
        }
        
        .user-dropdown.show {
            display: block;
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
            font-size: 14px;
        }
        
        .user-dropdown a:hover {
            background: #f8f9fa;
            color: var(--primary);
            transform: translateX(5px);
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
            transition: all 0.3s ease;
        }
        
        .user-avatar:hover {
            border-color: rgba(255,255,255,0.6);
            transform: scale(1.05);
        }
        
        .user-avatar i {
            color: white;
            font-size: 20px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script src="assets/js/settings.js"></script>
</head>
<body>
    <div class="body_bg" style="background: linear-gradient(135deg, #1f2235 0%, #2d325a 100%); min-height: 100vh;">

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
                            <a class="nav-link" data-bs-toggle="tab" href="securite1.php">Sécurité</a>
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
                                                       value="<?php echo htmlspecialchars($current_user['prenom'] ?? ''); ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Nom *</label>
                                                <input type="text" class="form-control" name="nom" 
                                                       value="<?php echo htmlspecialchars($current_user['nom'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" name="mail" value="<?php echo htmlspecialchars($current_user['mail'] ?? ''); ?>" readonly disabled>
                                        <small class="form-text text-muted">L'adresse email ne peut pas être modifiée</small>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" name="num" 
                                               value="<?php echo htmlspecialchars($current_user['num'] ?? ''); ?>" 
                                               placeholder="Votre numéro de téléphone">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Mot de passe de confirmation *</label>
                                        <input type="password" class="form-control" name="confirm_password" 
                                               placeholder="Entrez votre mot de passe pour confirmer les modifications">
                                        <small class="form-text text-muted">Veuillez entrer votre mot de passe pour confirmer les modifications</small>
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
                                               placeholder="Entrez votre mot de passe actuel">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Nouveau mot de passe *</label>
                                        <input type="password" class="form-control" name="new_password" 
                                               placeholder="Entrez votre nouveau mot de passe">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Confirmer le nouveau mot de passe *</label>
                                        <input type="password" class="form-control" name="confirm_password" 
                                               placeholder="Confirmez votre nouveau mot de passe">
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
                        <a href="profile1.php" class="back-home">
                            <i class="fas fa-arrow-left me-2"></i>Retour au profil
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>
</html>