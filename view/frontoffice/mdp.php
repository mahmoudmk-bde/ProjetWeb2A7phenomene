<?php
session_start();
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../model/utilisateur.php';

$utilisateurController = new UtilisateurController();
$error = "";
$success = "";
$step = 1; // 1: Recherche utilisateur, 2: Questions sécurité, 3: Nouveau mot de passe
$user_data = null;
$security_questions = null;

// Traitement du formulaire de recherche d'utilisateur
if (isset($_POST['search_user'])) {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Veuillez entrer votre adresse email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Veuillez entrer une adresse email valide";
    } else {
        $user_data = $utilisateurController->getUserByEmail($email);
        
        if ($user_data) {
            $security_questions = $utilisateurController->getSecurityQuestions($user_data['id_util']);
            
            // Vérifier si les questions de sécurité sont configurées
            if (empty($security_questions['q1']) || empty($security_questions['q2'])) {
                $error = "Les questions de sécurité ne sont pas configurées pour ce compte. Veuillez contacter l'administrateur.";
            } else {
                $step = 2;
                $_SESSION['reset_user_id'] = $user_data['id_util'];
            }
        } else {
            $error = "Aucun utilisateur trouvé avec cette adresse email";
        }
    }
}

// Traitement des réponses aux questions de sécurité
if (isset($_POST['verify_answers'])) {
    $user_id = $_SESSION['reset_user_id'] ?? null;
    $answer1 = trim($_POST['answer1']);
    $answer2 = trim($_POST['answer2']);
    
    if (!$user_id) {
        $error = "Session expirée. Veuillez recommencer.";
        $step = 1;
    } elseif (empty($answer1) || empty($answer2)) {
        $error = "Veuillez répondre aux deux questions de sécurité";
        $step = 2;
        $user_data = $utilisateurController->showUtilisateur($user_id);
        $security_questions = $utilisateurController->getSecurityQuestions($user_id);
    } else {
        // Vérifier les réponses
        $is_verified = $utilisateurController->verifySecurityQuestions($user_id, $answer1, $answer2);
        
        if ($is_verified) {
            $step = 3;
            $_SESSION['answers_verified'] = true;
        } else {
            $error = "Réponses incorrectes. Veuillez réessayer.";
            $step = 2;
            $user_data = $utilisateurController->showUtilisateur($user_id);
            $security_questions = $utilisateurController->getSecurityQuestions($user_id);
        }
    }
}

// Traitement du nouveau mot de passe
if (isset($_POST['reset_password'])) {
    $user_id = $_SESSION['reset_user_id'] ?? null;
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (!$user_id) {
        $error = "Session expirée. Veuillez recommencer.";
        $step = 1;
    } elseif (empty($new_password) || empty($confirm_password)) {
        $error = "Veuillez remplir tous les champs";
        $step = 3;
    } elseif ($new_password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas";
        $step = 3;
    } elseif (strlen($new_password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères";
        $step = 3;
    } else {
        // Mettre à jour le mot de passe
        $result = $utilisateurController->updatePassword($user_id, $new_password);
        
        if ($result) {
            $success = "Mot de passe réinitialisé avec succès!";
            $step = 4;
            
            // Nettoyer la session
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['answers_verified']);
        } else {
            $error = "Erreur lors de la réinitialisation du mot de passe. Veuillez réessayer.";
            $step = 3;
        }
    }
}

// Réinitialiser si l'utilisateur veut recommencer
if (isset($_POST['start_over'])) {
    $step = 1;
    $error = "";
    $success = "";
    unset($_SESSION['reset_user_id']);
    unset($_SESSION['answers_verified']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mdp.css">
    
</head>
<body>
    <div class="reset-container">
        <div class="reset-header">
            <h1><i class="fas fa-key me-2"></i>Mot de passe oublié</h1>
            <p>Suivez les étapes pour réinitialiser votre mot de passe</p>
        </div>

        <!-- Indicateur d'étapes -->
        <div class="step-indicator">
            <div class="step <?php echo $step >= 1 ? 'completed' : ''; echo $step == 1 ? ' active' : ''; ?>">
                <div class="step-number">1</div>
                <div class="step-label">Recherche</div>
            </div>
            <div class="step <?php echo $step >= 2 ? 'completed' : ''; echo $step == 2 ? ' active' : ''; ?>">
                <div class="step-number">2</div>
                <div class="step-label">Vérification</div>
            </div>
            <div class="step <?php echo $step >= 3 ? 'completed' : ''; echo $step == 3 ? ' active' : ''; ?>">
                <div class="step-number">3</div>
                <div class="step-label">Nouveau mot de passe</div>
            </div>
        </div>

        <!-- Messages d'alerte -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <!-- Étape 1: Recherche de l'utilisateur -->
        <?php if ($step == 1): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Adresse email *</label>
                    <input type="email" class="form-control" name="email" 
                           placeholder="Entrez votre adresse email"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                <button type="submit" name="search_user" class="btn-reset">
                    <i class="fas fa-search me-2"></i>Rechercher le compte
                </button>
            </form>
        <?php endif; ?>

        <!-- Étape 2: Questions de sécurité -->
        <?php if ($step == 2 && $user_data && $security_questions): ?>
            <form method="POST" action="">
                <p class="text-muted">Veuillez répondre à vos questions de sécurité pour vérifier votre identité.</p>
                
                <div class="question-section">
                    <div class="question-text"><?php echo htmlspecialchars($security_questions['q1']); ?></div>
                    <input type="text" class="form-control" name="answer1" 
                           placeholder="Votre réponse">
                </div>
                
                <div class="question-section">
                    <div class="question-text"><?php echo htmlspecialchars($security_questions['q2']); ?></div>
                    <input type="text" class="form-control" name="answer2" 
                           placeholder="Votre réponse">
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" name="start_over" class="btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="verify_answers" class="btn-reset">
                            <i class="fas fa-check me-2"></i>Vérifier
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <!-- Étape 3: Nouveau mot de passe -->
        <?php if ($step == 3): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Nouveau mot de passe *</label>
                    <input type="password" class="form-control" name="new_password" 
                           placeholder="Entrez votre nouveau mot de passe">
                    <small class="text-muted">Le mot de passe doit contenir au moins 6 caractères</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirmer le nouveau mot de passe *</label>
                    <input type="password" class="form-control" name="confirm_password" 
                           placeholder="Confirmez votre nouveau mot de passe">
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <button type="submit" name="start_over" class="btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" name="reset_password" class="btn-reset">
                            <i class="fas fa-save me-2"></i>Réinitialiser
                        </button>
                    </div>
                </div>
            </form>
        <?php endif; ?>

        <!-- Étape 4: Succès -->
        <?php if ($step == 4): ?>
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i>
                </div>
                <h4 style="color: #28a745;">Mot de passe réinitialisé avec succès!</h4>
                <p class="text-muted">Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.</p>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="connexion.php"><i class="fas fa-arrow-left me-2"></i>Retour à la connexion</a>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>