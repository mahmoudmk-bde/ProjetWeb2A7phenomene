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

// Récupérer la photo de profil depuis la base de données
$profile_picture = $current_user['img'] ?? 'default_avatar.jpg';

// Traitement de l'upload de photo de profil
if (isset($_POST['upload_picture']) && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === 0) {
    $upload_dir = __DIR__ . '/assets/uploads/profiles/';
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['profile_picture']['type'];
    $file_size = $_FILES['profile_picture']['size'];
    $file_tmp = $_FILES['profile_picture']['tmp_name'];
    $file_name = $_FILES['profile_picture']['name'];
    
    // Vérifier le type MIME réel du fichier
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $real_type = finfo_file($finfo, $file_tmp);
    finfo_close($finfo);
    
    if (in_array($real_type, $allowed_types) && $file_size <= 10 * 1024 * 1024) { // 10MB max
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        // Vérifier l'extension du fichier
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            // Générer un nom de fichier unique
            $new_filename = 'profile_' . $user_id . '_' . time() . '.' . strtolower($file_extension);
            $upload_path = $upload_dir . $new_filename;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Supprimer l'ancienne photo si elle existe et n'est pas l'avatar par défaut
                if (!empty($current_user['img']) && $current_user['img'] !== 'default_avatar.jpg') {
                    $old_file = $upload_dir . $current_user['img'];
                    if (file_exists($old_file) && is_file($old_file)) {
                        unlink($old_file);
                    }
                }
                
                try {
                    // Mettre à jour le chemin de l'image dans la base de données
                    $utilisateurController->updateProfilePicture($user_id, $new_filename);
                    
                    // Mettre à jour la session et les données locales
                    $_SESSION['profile_picture'] = $new_filename;
                    $current_user['img'] = $new_filename;
                    $profile_picture = $new_filename;
                    
                    $message = "Photo de profil mise à jour avec succès!";
                    $message_type = 'success';
                    
                    // Recharger les données utilisateur
                    $current_user = $utilisateurController->showUtilisateur($user_id);
                    
                } catch (Exception $e) {
                    $message = "Erreur lors de la mise à jour de la base de données: " . $e->getMessage();
                    $message_type = 'error';
                    
                    // Supprimer le fichier uploadé en cas d'erreur
                    if (file_exists($upload_path)) {
                        unlink($upload_path);
                    }
                }
            } else {
                $message = "Erreur lors du téléchargement de la photo. Veuillez réessayer.";
                $message_type = 'error';
            }
        } else {
            $message = "Extension de fichier non autorisée. Formats acceptés: JPG, JPEG, PNG, GIF, WebP.";
            $message_type = 'error';
        }
    } else {
        $message = "Format de fichier non supporté ou taille trop importante. Formats acceptés: JPG, PNG, GIF, WebP. Taille max: 10MB";
        $message_type = 'error';
    }
}

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
                            $current_user['rp2'],
                            $current_user['auth'],
                            $current_user['img'] // Garder l'image actuelle
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
                        $current_user['rp2'],
                        $current_user['auth'],
                        $current_user['img'] // Garder l'image actuelle
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

// Vérifier si l'image existe physiquement
$image_path = "assets/uploads/profiles/" . $profile_picture;
$full_image_path = __DIR__ . '/' . $image_path;
$image_exists = file_exists($full_image_path) && !empty($profile_picture) && $profile_picture !== 'default_avatar.jpg';

// Construire l'URL complète de l'image pour l'affichage
$profile_image_url = $image_exists ? $image_path . '?t=' . time() : 'assets/img/default_avatar.jpg';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paramètres - Engage Admin</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Styles spécifiques pour Settings (Portés du Frontoffice ou adaptés au thème dark de l'admin) */
        :root {
            --primary-color: #ff4a57;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --text-muted: #b0b3c1;
            --border-color: #2d3047;
        }
        
        body {
            background-color: #151828;
            color: var(--text-color);
        }

        .settings-section {
            background: var(--accent-color);
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 1px solid var(--border-color);
        }

        .section-title {
            font-size: 1.5rem;
            margin-bottom: 25px;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 10px;
        }

        .form-control {
            background-color: #151828;
            border: 1px solid var(--border-color);
            color: #fff;
        }
        
        .form-control:focus {
            background-color: #151828;
            border-color: var(--primary-color);
            color: #fff;
            box-shadow: none;
        }

        .nav-tabs .nav-link {
            color: var(--text-muted);
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            font-size: 1rem;
            padding: 10px 20px;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background: transparent;
        }
        
        .btn-save {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            padding: 10px 25px;
        }
        
        .btn-save:hover {
            background-color: #ff6b7a;
            border-color: #ff6b7a;
        }

        /* Profile Picture specific */
        .profile-upload-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .profile-img-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-color);
        }
        
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        
        .btn-upload {
            border: 1px solid var(--border-color);
            color: var(--text-color);
            background-color: var(--secondary-color);
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .btn-upload:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .file-upload-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        /* Alertes */
        .alert-message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: rgba(46, 204, 113, 0.2);
            color: #2ecc71;
            border: 1px solid #2ecc71;
        }
        
        .alert-error {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar simplifié pour admin -->
    <nav class="navbar navbar-expand-lg" style="background: var(--accent-color); border-bottom: 1px solid var(--border-color); padding: 15px;">
        <div class="container">
            <a class="navbar-brand" href="admin.php" style="color:#fff; font-weight:bold;">
                <img src="assets/img/logo.png" alt="logo" style="height:30px;"> Admin Panel
            </a>
            
            <div class="ml-auto d-flex align-items-center">
                 <div class="user-menu" style="position: relative; cursor: pointer;">
                     <div class="user-wrapper d-flex align-items-center" onclick="toggleUserMenu()">
                         <span class="user-name mr-2" style="color: #fff;"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                         <div class="user-avatar" style="width: 35px; height: 35px; border-radius: 50%; overflow: hidden;">
                             <?php if ($image_exists): ?>
                                 <img src="<?php echo $profile_image_url; ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                             <?php else: ?>
                                 <div style="width:100%; height:100%; background:#333; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-user" style="color:#fff;"></i>
                                 </div>
                             <?php endif; ?>
                         </div>
                     </div>
                     <div class="user-dropdown" id="userDropdown" style="display:none; position:absolute; right:0; top:45px; background:var(--accent-color); border:1px solid var(--border-color); border-radius:5px; min-width:150px; z-index:1000;">
                         <a href="settings.php" style="display:block; padding:10px; color:#fff; text-decoration:none; border-bottom:1px solid #333;"><i class="fas fa-cog"></i> Paramètres</a>
                         <a href="connexion.php?action=logout" style="display:block; padding:10px; color:#ff4a57; text-decoration:none;"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                     </div>
                 </div>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <h1 class="mb-4" style="color: #fff;">Paramètres du Compte</h1>

                <!-- Messages -->
                <?php if ($message): ?>
                    <div class="alert-message <?php echo $message_type === 'success' ? 'alert-success' : 'alert-error'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <!-- TABS -->
                <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">Général</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab">Sécurité</a>
                    </li>
                </ul>

                <div class="tab-content" id="settingsTabContent">
                    
                    <!-- ONGLET GÉNÉRAL -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        
                        <!-- PHOTO DE PROFIL UPLOAD -->
                        <div class="settings-section">
                            <h3 class="section-title">Photo de Profil</h3>
                            <form method="POST" action="" enctype="multipart/form-data" id="profilePictureForm">
                                <div class="profile-upload-container">
                                    <div class="profile-preview-wrapper">
                                        <img src="<?php echo $profile_image_url; ?>" id="imagePreview" class="profile-img-preview" alt="Aperçu">
                                    </div>
                                    
                                    <div class="upload-controls">
                                        <div class="file-upload-wrapper mb-2">
                                            <button type="button" class="btn-upload"><i class="fas fa-camera"></i> Choisir une photo</button>
                                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                                        </div>
                                        <div class="text-muted small">JPG, PNG, GIF ou WebP. Max 10MB.</div>
                                        
                                        <div id="uploadActions" style="display:none; margin-top:10px;">
                                            <button type="submit" name="upload_picture" class="btn btn-sm btn-save">Enregistrer</button>
                                            <button type="button" id="cancelUpload" class="btn btn-sm btn-secondary">Annuler</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- INFOS PERSO -->
                        <div class="settings-section">
                            <h3 class="section-title">Informations Personnelles</h3>
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Prénom *</label>
                                            <input type="text" class="form-control" name="prenom" value="<?php echo htmlspecialchars($current_user['prenom'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nom *</label>
                                            <input type="text" class="form-control" name="nom" value="<?php echo htmlspecialchars($current_user['nom'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Email *</label>
                                    <input type="email" class="form-control" name="mail" value="<?php echo htmlspecialchars($current_user['mail'] ?? ''); ?>" readonly disabled>
                                    <small class="text-muted">L'adresse email ne peut pas être modifiée.</small>
                                </div>
                                <div class="form-group">
                                    <label>Téléphone</label>
                                    <input type="tel" class="form-control" name="num" value="<?php echo htmlspecialchars($current_user['num'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Mot de passe de confirmation *</label>
                                    <input type="password" class="form-control" name="confirm_password" placeholder="Pour confirmer les changements" required>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-save">
                                    <i class="fas fa-save"></i> Enregistrer
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- ONGLET SÉCURITÉ -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="settings-section">
                            <h3 class="section-title">Changer le mot de passe</h3>
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label>Mot de passe actuel *</label>
                                    <input type="password" class="form-control" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label>Nouveau mot de passe *</label>
                                    <input type="password" class="form-control" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label>Confirmer nouveau mot de passe *</label>
                                    <input type="password" class="form-control" name="confirm_password" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-save">
                                    <i class="fas fa-key"></i> Changer le mot de passe
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
                
                <div class="text-center mt-4 mb-5">
                    <a href="admin.php" style="color: var(--text-color);"><i class="fas fa-arrow-left"></i> Retour au Dashboard</a>
                </div>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Toggle Menu
    function toggleUserMenu() {
        var menu = document.getElementById('userDropdown');
        if (menu.style.display === 'none' || menu.style.display === '') {
            menu.style.display = 'block';
        } else {
            menu.style.display = 'none';
        }
    }
    
    // Close menu on click outside
    document.addEventListener('click', function(event) {
        var wrapper = document.querySelector('.user-wrapper');
        var menu = document.getElementById('userDropdown');
        if (wrapper && !wrapper.contains(event.target) && menu) {
            menu.style.display = 'none';
        }
    });

    // Profile Picture Preview
    document.getElementById('profile_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const uploadActions = document.getElementById('uploadActions');
        const cancelBtn = document.getElementById('cancelUpload');
        
        // Save original src to restore on cancel
        if (!this.dataset.originalSrc) {
            this.dataset.originalSrc = preview.src;
        }

        if (file) {
            const maxSize = 10 * 1024 * 1024; // 10MB
            if (file.size > maxSize) {
                alert('Fichier trop volumineux (Max 10MB)');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                uploadActions.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });

    // Cancel Upload
    document.getElementById('cancelUpload').addEventListener('click', function() {
        const preview = document.getElementById('imagePreview');
        const fileInput = document.getElementById('profile_picture');
        const uploadActions = document.getElementById('uploadActions');
        
        // Restore original image
        if (fileInput.dataset.originalSrc) {
            preview.src = fileInput.dataset.originalSrc;
        }
        
        fileInput.value = '';
        uploadActions.style.display = 'none';
    });
    </script>

</body>
</html>