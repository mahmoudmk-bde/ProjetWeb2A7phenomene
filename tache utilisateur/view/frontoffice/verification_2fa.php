<?php
session_start();

// Vérifier si l'utilisateur a le droit d'accéder à cette page
if (!isset($_SESSION['2fa_required']) || !$_SESSION['2fa_required']) {
    header('Location: connexion.php');
    exit();
}

$error = "";

if (isset($_POST['verification_code'])) {
    $entered_code = $_POST['verification_code'];
    
    // Vérifier si le code a expiré
    if (time() > $_SESSION['2fa_expires']) {
        $error = "Le code de vérification a expiré. Veuillez vous reconnecter.";
        session_destroy();
    } 
    // Vérifier le code
    else if ($entered_code === $_SESSION['2fa_code']) {
        // Code correct - compléter la connexion
        $user_id = $_SESSION['2fa_user_id'];
        
        // Récupérer les informations utilisateur
        include '../../controller/utilisateurcontroller.php';
        $utilisateurc = new utilisateurcontroller();
        $user = $utilisateurc->showUtilisateur($user_id);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id_util'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_type'] = $user['typee'];
            $_SESSION['user_email'] = $user['mail'];
            
            // Nettoyer les variables 2FA
            unset($_SESSION['2fa_required']);
            unset($_SESSION['2fa_user_id']);
            unset($_SESSION['2fa_code']);
            unset($_SESSION['2fa_expires']);
            
            // Redirection selon le type d'utilisateur
            if ($user['typee'] === 'admin') {
                header('Location: http://localhost/tache%20utilisateur/view/backoffice/admin.php');
            } else {
                header('Location: index1.php');
            }
            exit();
        } else {
            $error = "Erreur lors de la récupération des informations utilisateur";
        }
    } else {
        $error = "Code de vérification incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification à deux facteurs</title>
    <link rel="stylesheet" href="assets/css/connexion.css">
    <style>
        .verification-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .verification-code {
            font-size: 24px;
            letter-spacing: 5px;
            margin: 20px 0;
            padding: 10px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        
        .timer {
            color: #ff6b6b;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <h1>Vérification à deux facteurs</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message" style="color: red; margin-bottom: 15px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <p>Un code de vérification a été envoyé à votre adresse email.</p>
        
        <div class="timer" id="timer">
            Le code expirera dans : <span id="countdown">5:00</span>
        </div>
        
        <form method="post">
            <div class="form-group">
                <input type="text" 
                       name="verification_code" 
                       class="verification-code" 
                       placeholder="000000" 
                       maxlength="6" 
                       required
                       pattern="[0-9]{6}">
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">
                Vérifier
            </button>
        </form>
        
        <p style="margin-top: 20px;">
            <a href="connexion.php" style="color: #666;">Retour à la connexion</a>
        </p>
    </div>

    <script>
        // Compte à rebours de 5 minutes
        let timeLeft = 300; // 5 minutes en secondes
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('countdown').textContent = 
                `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            } else {
                document.getElementById('timer').innerHTML = 
                    '<span style="color: red;">Le code a expiré</span>';
            }
        }
        
        updateTimer();
    </script>
</body>
</html>