<?php
session_start();
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../Model/utilisateur.php';

$utilisateurc = new utilisateurcontroller();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["mail"]) && isset($_POST["mdp"])) {
        if (!empty($_POST["mail"]) && !empty($_POST["mdp"])) {
            
            $mail = trim($_POST['mail']);
            $mdp = trim($_POST['mdp']);
            
            // Vérifier d'abord si l'utilisateur existe avec cet email
            if ($utilisateurc->emailExists($mail)) {
                // Tentative de connexion
                $user = $utilisateurc->login($mail, $mdp);
                
                if ($user) {
                    // Connexion réussie
                    $_SESSION['user_id'] = $user['id_util'];
                    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                    $_SESSION['user_email'] = $user['mail'];
                    $_SESSION['user_type'] = $user['typee'];
                    
                    // Redirection
                    if (isset($_GET['redirect'])) {
                        header('Location: ' . urldecode($_GET['redirect']));
                    } else {
                        header('Location: index.php');
                    }
                    exit();
                } else {
                    $error = "Mot de passe incorrect";
                }
            } else {
                $error = "Aucun compte trouvé avec cet email";
            }
        } else {
            $error = "Veuillez remplir tous les champs";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ENGAGE</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/all.css">
    <style>
        .particles-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #1f2235 0%, #2d325a 100%);
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .login-container h1 {
            color: #fff;
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            width: 100%;
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
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
        }
        
        .btn-connexion {
            background: linear-gradient(135deg, #ff4a57 0%, #ff6b6b 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        
        .btn-connexion:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 74, 87, 0.4);
        }
        
        .btn-inscription {
            background: transparent;
            border: 2px solid #ff4a57;
            color: #ff4a57;
            padding: 12px 30px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        
        .btn-inscription:hover {
            background: #ff4a57;
            color: white;
            text-decoration: none;
        }
        
        .signup {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            margin: 20px 0;
        }
        
        .error-message {
            color: #ff6b6b;
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }
        
        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid #dc3545;
            color: #dc3545;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        
        .forgot-password a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: #ff4a57;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="particles-container" id="particles"></div>
    
    <div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
        <div class="login-container">
            <h1>Connexion</h1>

            <!-- Afficher le message d'erreur -->
            <?php if (!empty($error)): ?>
                <div class="alert-error">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post" id="connexionForm">
                <div class="form-group">
                    <input type="email" name="mail" id="mail" placeholder="Adresse email" required
                           value="<?php echo isset($_POST['mail']) ? htmlspecialchars($_POST['mail']) : ''; ?>">
                    <div class="error-message" id="error-mail"></div>
                </div>

                <div class="form-group">
                    <div class="password-container">
                        <input type="password" name="mdp" id="mdp" placeholder="Mot de passe" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('mdp')">
                            <i class="far fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-message" id="error-mdp"></div>
                </div>

                <button type="submit" class="btn-connexion">
                    SE CONNECTER
                </button>
            </form>

            <div class="forgot-password">
                <a href="mdp.php">Mot de passe oublié ?</a>
            </div>

            <p class="signup">Vous n'avez pas de compte ?</p>
            <a href="inscription.php" class="btn-inscription">S'INSCRIRE</a>
        </div>
    </div>

    <script>
        // Fonction pour afficher/masquer le mot de passe
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.parentNode.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Validation du formulaire
        document.getElementById('connexionForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Validation email
            const email = document.getElementById('mail');
            const emailError = document.getElementById('error-mail');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email.value.trim()) {
                emailError.textContent = 'Veuillez entrer votre adresse email';
                emailError.style.display = 'block';
                isValid = false;
            } else if (!emailRegex.test(email.value)) {
                emailError.textContent = 'Veuillez entrer une adresse email valide';
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }
            
            // Validation mot de passe
            const password = document.getElementById('mdp');
            const passwordError = document.getElementById('error-mdp');
            
            if (!password.value.trim()) {
                passwordError.textContent = 'Veuillez entrer votre mot de passe';
                passwordError.style.display = 'block';
                isValid = false;
            } else {
                passwordError.style.display = 'none';
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Effacer les erreurs quand l'utilisateur commence à taper
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', function() {
                const errorElement = document.getElementById('error-' + this.id);
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            });
        });

        // Animation des particules (optionnelle)
        function createParticles() {
            const container = document.getElementById('particles');
            const particleCount = 50;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.style.position = 'absolute';
                particle.style.width = Math.random() * 3 + 1 + 'px';
                particle.style.height = particle.style.width;
                particle.style.background = 'rgba(255, 255, 255, ' + (Math.random() * 0.3 + 0.1) + ')';
                particle.style.borderRadius = '50%';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                particle.style.animation = `float ${Math.random() * 10 + 10}s linear infinite`;
                container.appendChild(particle);
            }
        }

        // Démarrer l'animation des particules
        createParticles();
    </script>

    <style>
        @keyframes float {
            0% {
                transform: translateY(0) translateX(0);
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(calc(-50vw + 100px * var(--random)));
                opacity: 0;
            }
        }
    </style>
</body>
</html>