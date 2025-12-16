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

// Récupérer la photo de profil depuis la base de données
$profile_picture = $current_user['img'] ?? 'default_avatar.jpg';

// Vérifier si l'image existe physiquement
$image_path = "assets/uploads/profiles/" . $profile_picture;
$full_image_path = $_SERVER['DOCUMENT_ROOT'] . '/' . $image_path;
$image_exists = file_exists($full_image_path) && !empty($profile_picture) && $profile_picture !== 'default_avatar.jpg';
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
    <script src="assets/js/profile.js"></script>
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
                                                     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                                            <?php else: ?>
                                                <i class="fas fa-user"></i>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="user-dropdown" id="userDropdown">
                                        <a href="index1.php">
                                            <i class="fas fa-user"></i>accueil
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
                        <div class="profile-avatar">
                            <?php if ($image_exists): ?>
                                <img src="<?php echo $image_path; ?>" 
                                     alt="Photo de profil" 
                                     style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <h1>Mon Profil</h1>
                        <p style="color: #b0b3c1;">Gérez vos informations personnelles</p>
                    </div>

                    <!-- ... le reste du contenu profil ... -->

                    <div class="profile-info">
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
                            <span class="info-label">Membre depuis</span>
                            <span class="info-value"><?php echo date('d/m/Y'); ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Statut</span>
                            <span class="info-value badge badge-success">Actif</span>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="settings.php" class="btn btn-edit me-3">
                            <i class="fas fa-edit me-2"></i>Modifier le profil
                        </a>
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