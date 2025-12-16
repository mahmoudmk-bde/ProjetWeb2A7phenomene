<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Inclure les fichiers nécessaires pour récupérer les données utilisateur
include '../../controller/utilisateurcontroller.php';
$utilisateurController = new UtilisateurController();
$user_id = $_SESSION['user_id'];
$current_user = $utilisateurController->showUtilisateur($user_id);

// Récupérer la photo de profil depuis la session ou la base de données
$profile_picture = $_SESSION['profile_picture'] ?? $current_user['img'] ?? 'default_avatar.jpg';

// Vérifier si l'image existe physiquement
$image_path = "assets/uploads/profiles/" . $profile_picture;
$full_image_path = $_SERVER['DOCUMENT_ROOT'] . '/ProjetWeb2A7phenomene/view/frontoffice/' . $image_path;
$image_exists = file_exists($full_image_path) && !empty($profile_picture) && $profile_picture !== 'default_avatar.jpg';

// Si l'image n'existe pas, utiliser l'avatar par défaut
if (!$image_exists) {
    $profile_picture = 'default_avatar.jpg';
    $image_path = "assets/img/" . $profile_picture;
    $image_exists = false; // No actual profile picture
}
// Traitement de l'upload d'image
$upload_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB limit
                $fileNameNew = "profile_" . $user_id . "_" . time() . "." . $fileExt;
                $fileDestination = 'assets/uploads/profiles/' . $fileNameNew;
                
                // Ensure directory exists (just in case)
                if (!file_exists(dirname($fileDestination))) {
                    mkdir(dirname($fileDestination), 0777, true);
                }

                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    // Update database
                    if ($utilisateurController->updateImage($user_id, $fileNameNew)) {
                        $_SESSION['profile_picture'] = $fileNameNew;
                        // Refresh to see changes
                        header("Location: profile.php?upload=success");
                        exit();
                    } else {
                        $upload_message = "Erreur lors de la mise à jour de la base de données.";
                    }
                } else {
                    $upload_message = "Erreur lors du téléchargement du fichier.";
                }
            } else {
                $upload_message = "Le fichier est trop volumineux.";
            }
        } else {
            $upload_message = "Une erreur est survenue lors du téléchargement.";
        }
    } else {
        $upload_message = "Type de fichier non autorisé (autorisés: jpg, jpeg, png, gif).";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Engage</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/profile.css">
    <style>
        :root {
            --primary-color: #ff4a57;
            --secondary-color: #1f2235;
            --accent-color: #24263b;
            --text-color: #ffffff;
            --border-color: #2d3047;
        }
        
        /* Profile Picture specific */
        .profile-upload-container {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            background: var(--accent-color);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
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
            color: #fff;
        }
        
        .file-upload-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        .btn-save {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
            padding: 5px 15px;
            font-size: 0.9rem;
            border-radius: 5px;
        }
        
        .btn-save:hover {
            background-color: #ff6b7a;
            border-color: #ff6b7a;
            color: #fff;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
            padding: 5px 15px;
            font-size: 0.9rem;
            border-radius: 5px;
        }
    </style></head>
<body>
    <div class="body_bg" style="background: #1f2235;">
        <!-- Header -->
        <header class="main_menu single_page_menu">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-12">
                        <nav class="navbar navbar-expand-lg navbar-light">
                           
                            <div class="collapse navbar-collapse main-menu-item">
                                <ul class="navbar-nav">
                                    <li class="nav-item">
                                        
                                    </li>
                                </ul>
                            </div>
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <div class="user-menu d-none d-sm-block">
                                    <div class="user-wrapper" onclick="toggleUserMenu()">
                                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                                        <div class="user-avatar">
                                            <?php if ($image_exists): ?>
                                                <img src="<?php echo $image_path; ?>" 
                                                     alt="Photo de profil" 
                                                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fas fa-user" style="font-size: 20px;"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="user-dropdown" id="userDropdown">
                                        <a href="index.php">
                                            <i class="fas fa-user"></i>Accueil
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

        <!-- Profile Content -->
        <section class="profile-section" style="padding: 100px 0;">
            <div class="container">
                <div class="profile-container">
                    <div class="profile-header">
                        <!-- Avatar Removed (Moved Upload Form Here) -->
                        <h1 class="mt-3">Mon Profil</h1>
                        <p style="color: #b0b3c1;">Gérez vos informations personnelles</p>
                        
                        <!-- Formulaire d'upload de photo NOOUVEAU DESIGN (Moved to Top) -->
                        <div class="mt-4 text-left">
                            <h3 style="color: #fff; margin-bottom: 20px; font-size: 1.5rem;">Changer ma photo de profil</h3>
                            <?php if(isset($_GET['upload']) && $_GET['upload'] == 'success'): ?>
                                <div class="alert alert-success">Photo de profil mise à jour avec succès!</div>
                            <?php endif; ?>
                            <?php if(!empty($upload_message)): ?>
                                <div class="alert alert-danger"><?php echo $upload_message; ?></div>
                            <?php endif; ?>
                            
                            <form action="profile.php" method="POST" enctype="multipart/form-data" id="profilePictureForm">
                                <div class="profile-upload-container">
                                    <div class="profile-preview-wrapper">
                                        <?php if ($image_exists): ?>
                                             <img src="<?php echo $image_path; ?>" id="imagePreview" class="profile-img-preview" alt="Aperçu">
                                         <?php else: ?>
                                             <div id="imagePlaceholder" style="width:100px; height:100px; background:#333; display:flex; align-items:center; justify-content:center; border-radius:50%; border:3px solid var(--primary-color);">
                                                <i class="fas fa-user fa-2x" style="color:#fff;"></i>
                                             </div>
                                             <img src="" id="imagePreview" class="profile-img-preview" alt="Aperçu" style="display:none;">
                                         <?php endif; ?>
                                    </div>
                                    
                                    <div class="upload-controls">
                                        <div class="file-upload-wrapper mb-2">
                                            <button type="button" class="btn-upload"><i class="fas fa-camera"></i> Choisir une photo</button>
                                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*" data-original-src="<?php echo $image_exists ? $image_path : ''; ?>">
                                        </div>
                                        <div class="text-muted small" style="color: #b0b3c1 !important;">JPG, PNG, GIF. Max 5Mo.</div>
                                        
                                        <div id="uploadActions" style="display:none; margin-top:10px;">
                                            <button type="submit" class="btn btn-save">Enregistrer</button>
                                            <button type="button" id="cancelUpload" class="btn btn-secondary">Annuler</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="profile-info mt-4">
                        <div class="info-item">
                            <span class="info-label">Nom complet</span>
                            <span class="info-value"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Type d'utilisateur</span>
                            <span class="info-value"><?php echo htmlspecialchars($_SESSION['user_type'] ?? 'Membre'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Téléphone</span>
                            <span class="info-value"><?php echo htmlspecialchars($current_user['num'] ?? 'Non renseigné'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Statut</span>
                            <span class="info-value badge bg-success">Actif</span>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="settings.php" class="btn btn-primary me-3">
                            <i class="fas fa-edit me-2"></i>Modifier le profil
                        </a>
                        <a href="index1.php" class="btn btn-outline-light">
                            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                        </a>
                        </a>
                    </div>
                    
                    <!-- Formulaire d'upload de photo -->
                    <!-- Old Upload Form Location (Deleted) -->
                </div>
            </div>
        </section>
    </div>

    <script>
        // Profile Picture Preview
        document.getElementById('profile_picture').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const uploadActions = document.getElementById('uploadActions');
            
            // Save original src if not already saved (via PHP data attribute above is better but fallback here)
            if (!this.dataset.originalSrc && preview.src) {
                this.dataset.originalSrc = preview.src;
            }
    
            if (file) {
                const maxSize = 5 * 1024 * 1024; // 5MB
                if (file.size > maxSize) {
                    alert('Fichier trop volumineux (Max 5Mo)');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    if(placeholder) placeholder.style.display = 'none';
                    uploadActions.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    
        // Cancel Upload
        document.getElementById('cancelUpload').addEventListener('click', function() {
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            const fileInput = document.getElementById('profile_picture');
            const uploadActions = document.getElementById('uploadActions');
            
            // Restore original image
            if (fileInput.dataset.originalSrc) {
                preview.src = fileInput.dataset.originalSrc;
                preview.style.display = 'block';
                if(placeholder) placeholder.style.display = 'none';
            } else {
                // No original image (was text/placeholder)
                preview.src = '';
                preview.style.display = 'none';
                if(placeholder) placeholder.style.display = 'flex';
            }
            
            fileInput.value = '';
            uploadActions.style.display = 'none';
        });

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
    </script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>