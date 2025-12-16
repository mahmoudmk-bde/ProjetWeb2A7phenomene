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

// Vérifier l'état actuel de la 2FA
$two_factor_enabled = ($current_user['auth'] === 'active');

// Liste des questions de sécurité disponibles
$security_questions = [
    "Quel est le nom de votre animal de compagnie ?",
    "Quel est le nom de votre ville natale ?",
    "Quel est le nom de votre école primaire ?",
    "Quel est le métier de votre père ?",
    "Quel est votre film préféré ?",
    "Quel est le nom de votre meilleur ami d'enfance ?",
    "Quel est votre plat préféré ?",
    "Quel est votre livre préféré ?",
    "Quel est le nom de jeune fille de votre mère ?",
    "Quel est votre sport préféré ?"
];

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
                        $current_user['typee'],
                        $current_user['q1'],
                        $current_user['rp1'],
                        $current_user['q2'],
                        $current_user['rp2'],
                        $current_user['auth'] // Conserver le statut auth actuel
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
        
        // Vérifier le mot de passe pour les modifications de sécurité
        $security_password = $_POST['security_password'] ?? '';
        
        if (empty($security_password)) {
            $message = "Veuillez entrer votre mot de passe pour confirmer les modifications.";
            $message_type = 'error';
        } elseif ($security_password !== $current_user['mdp']) {
            $message = "Le mot de passe est incorrect.";
            $message_type = 'error';
        } else {
            try {
                // Mettre à jour la 2FA dans la base de données
                $auth_status = $two_factor ? 'active' : 'desactive';
                $utilisateurController->updateAuth($user_id, $auth_status);
                
                $_SESSION['security_settings'] = [
                    'two_factor' => $two_factor,
                    'session_timeout' => $session_timeout,
                    'login_alerts' => $login_alerts
                ];
                
                $message = "Paramètres de sécurité mis à jour avec succès!";
                $message_type = 'success';
                
                // Recharger les données utilisateur
                $current_user = $utilisateurController->showUtilisateur($user_id);
                $two_factor_enabled = ($current_user['auth'] === 'active');
                
            } catch (Exception $e) {
                $message = "Erreur lors de la mise à jour des paramètres: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
    
    // NOUVEAU: Vérification de l'identité pour afficher les réponses
    if (isset($_POST['verify_identity'])) {
        $verify_password = trim($_POST['verify_password']);
        
        if (empty($verify_password)) {
            $message = "Veuillez entrer votre mot de passe.";
            $message_type = 'error';
        } elseif ($verify_password !== $current_user['mdp']) {
            $message = "Le mot de passe est incorrect.";
            $message_type = 'error';
        } else {
            $_SESSION['answers_verified'] = true;
            $message = "Identité vérifiée. Vous pouvez maintenant voir et modifier vos questions de sécurité.";
            $message_type = 'success';
        }
    }
    
    if (isset($_POST['update_security_questions'])) {
        // Mise à jour des questions de sécurité
        $question1 = trim($_POST['security_question1']);
        $answer1 = trim($_POST['security_answer1']);
        $question2 = trim($_POST['security_question2']);
        $answer2 = trim($_POST['security_answer2']);
        $security_password = trim($_POST['security_password']);
        
        if (empty($question1) || empty($answer1) || empty($question2) || empty($answer2) || empty($security_password)) {
            $message = "Tous les champs doivent être remplis, y compris le mot de passe.";
            $message_type = 'error';
        } elseif ($question1 === $question2) {
            $message = "Vous ne pouvez pas choisir la même question pour les deux questions de sécurité.";
            $message_type = 'error';
        } elseif ($security_password !== $current_user['mdp']) {
            $message = "Le mot de passe est incorrect.";
            $message_type = 'error';
        } else {
            try {
                // Préparer la date de naissance
                $dt_naiss = null;
                if ($current_user['dt_naiss']) {
                    $dt_naiss = DateTime::createFromFormat('Y-m-d', $current_user['dt_naiss']);
                }
                
                // Créer l'objet Utilisateur avec les nouvelles questions de sécurité
                $utilisateur = new Utilisateur(
                    $user_id,
                    $current_user['prenom'],
                    $current_user['nom'],
                    $dt_naiss,
                    $current_user['mail'],
                    $current_user['num'],
                    $current_user['mdp'],
                    $current_user['typee'],
                    $question1,
                    $answer1,
                    $question2,
                    $answer2,
                    $current_user['auth'] // Conserver le statut auth actuel
                );
                
                // Mettre à jour l'utilisateur
                $utilisateurController->updateUtilisateur($utilisateur, $user_id);
                
                $message = "Questions de sécurité mises à jour avec succès!";
                $message_type = 'success';
                
                // Recharger les données utilisateur
                $current_user = $utilisateurController->showUtilisateur($user_id);
                
            } catch (Exception $e) {
                $message = "Erreur lors de la mise à jour des questions: " . $e->getMessage();
                $message_type = 'error';
            }
        }
    }
}

// Fonction pour générer les options des questions de sécurité
function generateQuestionOptions($available_questions, $selected_question = '', $exclude_question = '') {
    $options = '<option value="">Choisissez une question</option>';
    foreach ($available_questions as $question) {
        if ($question !== $exclude_question) {
            $selected = ($question === $selected_question) ? 'selected' : '';
            $options .= "<option value=\"$question\" $selected>$question</option>";
        }
    }
    return $options;
}

// Fonction pour afficher les réponses (masquées ou en clair)
function displayAnswer($answer, $show_answers) {
    if ($show_answers && !empty($answer)) {
        return htmlspecialchars($answer);
    } else {
        return str_repeat('•', 8); // 8 points pour masquer la réponse
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
    <style>
        .answer-masked {
            color: #666;
            font-family: monospace;
            letter-spacing: 2px;
        }
        .verification-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .answers-unlocked {
            background: #f0fff0;
            border-left: 4px solid #28a745;
        }
        .field-disabled {
            opacity: 0.6;
            pointer-events: none;
        }
        .auth-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: bold;
        }
        .auth-active {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .auth-inactive {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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
                        <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?>">
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
                                    <div class="feature-title">
                                        Authentification à deux facteurs (2FA)
                                        <span class="auth-status <?php echo $two_factor_enabled ? 'auth-active' : 'auth-inactive'; ?>">
                                            <?php echo $two_factor_enabled ? 'Activée' : 'Désactivée'; ?>
                                        </span>
                                    </div>
                                    <div class="feature-description">
                                        Ajoutez une couche de sécurité supplémentaire à votre compte. 
                                        <?php if ($two_factor_enabled): ?>
                                            <span class="text-success">La 2FA est actuellement activée pour votre compte.</span>
                                        <?php else: ?>
                                            <span class="text-muted">La 2FA est actuellement désactivée.</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="two_factor" <?php echo $two_factor_enabled ? 'checked' : ''; ?>>
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
                            <!-- Champ mot de passe pour confirmer les modifications -->
                            <div class="form-group">
                                <label class="form-label">Mot de passe de confirmation *</label>
                                <input type="password" class="form-control" name="security_password" 
                                       placeholder="Entrez votre mot de passe pour confirmer" required>
                                <small class="text-muted">Veuillez entrer votre mot de passe pour confirmer les modifications</small>
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
                        
                        <!-- Section de vérification du mot de passe -->
                        <?php if (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']): ?>
                        <div class="verification-section">
                            <h5><i class="fas fa-lock me-2"></i>Vérification requise</h5>
                            <p class="text-muted">Pour afficher et modifier vos questions de sécurité, veuillez d'abord confirmer votre identité.</p>
                            <form method="POST" action="" class="row g-3 align-items-end">
                                <div class="col-md-6">
                                    <label class="form-label">Mot de passe de confirmation *</label>
                                    <input type="password" class="form-control" name="verify_password" 
                                           placeholder="Entrez votre mot de passe" required>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" name="verify_identity" class="btn btn-primary">
                                        <i class="fas fa-check me-2"></i>Vérifier l'identité
                                    </button>
                                </div>
                            </form>
                        </div>
                        <?php else: ?>
                        <div class="verification-section answers-unlocked">
                            <h5><i class="fas fa-unlock me-2"></i>Questions de sécurité déverrouillées</h5>
                            <p class="text-success">Vous pouvez maintenant modifier vos questions de sécurité.</p>
                        </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="securityQuestionsForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Question de sécurité 1 *</label>
                                        <select class="form-select <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'field-disabled' : ''; ?>" name="security_question1" id="question1" required onchange="updateQuestion2Options()" <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'disabled' : ''; ?>>
                                            <?php echo generateQuestionOptions($security_questions, $current_user['q1'] ?? '', $current_user['q2'] ?? ''); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Réponse 1 *</label>
                                        <input type="<?php echo (isset($_SESSION['answers_verified']) && $_SESSION['answers_verified']) ? 'text' : 'password'; ?>" 
                                               class="form-control <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'answer-masked field-disabled' : ''; ?>" 
                                               name="security_answer1" 
                                               value="<?php echo displayAnswer($current_user['rp1'] ?? '', (isset($_SESSION['answers_verified']) && $_SESSION['answers_verified'])); ?>"
                                               placeholder="Votre réponse" 
                                               required 
                                               <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'disabled' : ''; ?>>
                                        <?php if ((!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) && !empty($current_user['rp1'])): ?>
                                        <small class="text-muted">Réponse actuelle masquée pour votre sécurité</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Question de sécurité 2 *</label>
                                        <select class="form-select <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'field-disabled' : ''; ?>" name="security_question2" id="question2" required onchange="updateQuestion1Options()" <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'disabled' : ''; ?>>
                                            <?php echo generateQuestionOptions($security_questions, $current_user['q2'] ?? '', $current_user['q1'] ?? ''); ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Réponse 2 *</label>
                                        <input type="<?php echo (isset($_SESSION['answers_verified']) && $_SESSION['answers_verified']) ? 'text' : 'password'; ?>" 
                                               class="form-control <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'answer-masked field-disabled' : ''; ?>" 
                                               name="security_answer2" 
                                               value="<?php echo displayAnswer($current_user['rp2'] ?? '', (isset($_SESSION['answers_verified']) && $_SESSION['answers_verified'])); ?>"
                                               placeholder="Votre réponse" 
                                               required 
                                               <?php echo (!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) ? 'disabled' : ''; ?>>
                                        <?php if ((!isset($_SESSION['answers_verified']) || !$_SESSION['answers_verified']) && !empty($current_user['rp2'])): ?>
                                        <small class="text-muted">Réponse actuelle masquée pour votre sécurité</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($_SESSION['answers_verified']) && $_SESSION['answers_verified']): ?>
                            <!-- Champ mot de passe pour confirmer les modifications -->
                            <div class="form-group">
                                <label class="form-label">Mot de passe de confirmation *</label>
                                <input type="password" class="form-control" name="security_password" 
                                       placeholder="Entrez votre mot de passe pour confirmer" required>
                                <small class="text-muted">Veuillez entrer votre mot de passe pour confirmer les modifications</small>
                            </div>
                            
                            <button type="submit" name="update_security_questions" class="btn btn-save">
                                <i class="fas fa-save me-2"></i>Enregistrer les questions
                            </button>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="text-center mt-4">
                        <a href="admin.php" class="back-home">
                            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Liste des questions de sécurité (identique à celle en PHP)
        const securityQuestions = [
            "Quel est le nom de votre animal de compagnie ?",
            "Quel est le nom de votre ville natale ?",
            "Quel est le nom de votre école primaire ?",
            "Quel est le métier de votre père ?",
            "Quel est votre film préféré ?",
            "Quel est le nom de votre meilleur ami d'enfance ?",
            "Quel est votre plat préféré ?",
            "Quel est votre livre préféré ?",
            "Quel est le nom de jeune fille de votre mère ?",
            "Quel est votre sport préféré ?"
        ];

        function updateQuestion2Options() {
            const question1 = document.getElementById('question1');
            const question2 = document.getElementById('question2');
            const selectedQuestion1 = question1.value;
            
            // Sauvegarder la sélection actuelle de la question 2
            const currentQuestion2 = question2.value;
            
            // Vider les options de la question 2
            question2.innerHTML = '<option value="">Choisissez une question</option>';
            
            // Ajouter toutes les questions sauf celle sélectionnée dans la question 1
            securityQuestions.forEach(question => {
                if (question !== selectedQuestion1) {
                    const option = document.createElement('option');
                    option.value = question;
                    option.textContent = question;
                    if (question === currentQuestion2 && question !== selectedQuestion1) {
                        option.selected = true;
                    }
                    question2.appendChild(option);
                }
            });
        }

        function updateQuestion1Options() {
            const question1 = document.getElementById('question1');
            const question2 = document.getElementById('question2');
            const selectedQuestion2 = question2.value;
            
            // Sauvegarder la sélection actuelle de la question 1
            const currentQuestion1 = question1.value;
            
            // Vider les options de la question 1
            question1.innerHTML = '<option value="">Choisissez une question</option>';
            
            // Ajouter toutes les questions sauf celle sélectionnée dans la question 2
            securityQuestions.forEach(question => {
                if (question !== selectedQuestion2) {
                    const option = document.createElement('option');
                    option.value = question;
                    option.textContent = question;
                    if (question === currentQuestion1 && question !== selectedQuestion2) {
                        option.selected = true;
                    }
                    question1.appendChild(option);
                }
            });
        }

        // Validation côté client pour empêcher la soumission si les questions sont identiques
        document.getElementById('securityQuestionsForm').addEventListener('submit', function(e) {
            const question1 = document.getElementById('question1').value;
            const question2 = document.getElementById('question2').value;
            
            if (question1 && question2 && question1 === question2) {
                e.preventDefault();
                alert('Vous ne pouvez pas choisir la même question pour les deux questions de sécurité.');
            }
        });
    </script>
    
</body>
</html>