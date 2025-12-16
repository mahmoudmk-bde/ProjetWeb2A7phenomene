<?php
session_start();
include '../../controller/utilisateurcontroller.php';
require_once __DIR__ . '/../../model/utilisateur.php';

$error = "";
$utilisateurc = new utilisateurcontroller();

// AJOUT: Gérer le retour à la connexion depuis le lien
if (isset($_GET['retour']) && $_GET['retour'] == 1) {
    session_destroy();
    header('Location: connexion.php');
    exit();
}

// AJOUT: Traitement de la connexion par reconnaissance faciale
if (isset($_POST['face_login']) && isset($_POST['descriptor'])) {
    header('Content-Type: application/json');
    $incomingDescriptor = json_decode($_POST['descriptor']);
    
    if (!$incomingDescriptor) {
        echo json_encode(['success' => false, 'message' => 'Descripteur invalide']);
        exit;
    }

    $allUsers = $utilisateurc->getAllFaces(); 
    $matchFound = false;
    $matchedUserId = null;

    foreach ($allUsers as $user) {
        $storedDescriptor = json_decode($user['face']); 
        if (!$storedDescriptor) continue;

        $distance = 0;
        for ($i = 0; $i < count($incomingDescriptor); $i++) {
            $diff = $incomingDescriptor[$i] - $storedDescriptor[$i];
            $distance += $diff * $diff;
        }
        $distance = sqrt($distance);

        if ($distance < 0.5) { 
            $matchFound = true;
            $matchedUserId = $user['id_util'];
            break;
        }
    }

    if ($matchFound) {
        $user = $utilisateurc->showUtilisateur($matchedUserId);
        if ($user) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id_util'];
            $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
            $_SESSION['user_type'] = $user['typee'];
            $_SESSION['user_email'] = $user['mail'];
            $_SESSION['profile_picture'] = $user['img'];

            $redirect = ($user['typee'] === 'admin') 
                ? '../backoffice/dashboard.php' 
                : 'index1.php';
            
            echo json_encode(['success' => true, 'redirect' => $redirect]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Visage non reconnu']);
    }
    exit;
}

// AJOUT: Vérifier si c'est une tentative de vérification 2FA
if (isset($_POST["verify_2fa_code"])) {
    $entered_code = $_POST["verification_code"];
    $user_id = $_SESSION['temp_user_id'] ?? null;
    
    if ($user_id && isset($_SESSION['2fa_code']) && isset($_SESSION['2fa_expires'])) {
        // Vérifier l'expiration
        if (time() > $_SESSION['2fa_expires']) {
            $error = "Le code a expiré. Veuillez vous reconnecter.";
            session_destroy();
        } 
        // Vérifier le code
        else if ($entered_code === $_SESSION['2fa_code']) {
            // Code correct - compléter la connexion
            $_SESSION['user_id'] = $_SESSION['temp_user_id'];
            $_SESSION['user_name'] = $_SESSION['temp_user_name'];
            $_SESSION['user_type'] = $_SESSION['temp_user_type'];
            $_SESSION['user_email'] = $_SESSION['temp_user_email'];
            
            // STOCKER L'IMAGE DANS LA SESSION
            $_SESSION['profile_picture'] = $_SESSION['temp_profile_picture'];
            
            // Nettoyer les variables temporaires
            unset($_SESSION['2fa_required'], $_SESSION['2fa_user_id'], $_SESSION['2fa_code'], 
                  $_SESSION['2fa_expires'], $_SESSION['temp_user_id'], $_SESSION['temp_user_name'],
                  $_SESSION['temp_user_email'], $_SESSION['temp_user_type'], $_SESSION['temp_profile_picture']);
            
            // ouvrir index.html
            // ouvrir index.html
            if ($_SESSION['user_type'] === 'admin') {
                header('Location: ../backoffice/dashboard.php');
            } else {
                header('Location: index1.php');
            }
            exit();
        } else {
            $error = "Code de vérification incorrect";
            $show_2fa_form = true;
        }
    } else {
        $error = "Session invalide. Veuillez vous reconnecter.";
        session_destroy();
    }
}

// AJOUT: Afficher le formulaire 2FA si nécessaire
if (isset($_SESSION['2fa_required']) && $_SESSION['2fa_required']) {
    $show_2fa_form = true;
}

// Traitement normal de la connexion
if (isset($_POST["username"]) && isset($_POST["password"]) && !isset($show_2fa_form)) {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
        
        $username = $_POST["username"];
        $mdp = $_POST["password"];
        $sql = "";
        
        // Vérifier si c'est un email
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            if ($utilisateurc->emailExists($username)) {
                $sql = "SELECT * FROM utilisateur WHERE mail = :identifiant";
            } else {
                $error = "Email non trouvé";
            }
        } 
        // Vérifier si c'est un numéro
        else if (is_numeric($username)) {
            if ($utilisateurc->numExists($username)) {
                $sql = "SELECT * FROM utilisateur WHERE num = :identifiant";
            } else {
                $error = "Numéro de téléphone non trouvé";
            }
        } else {
            $error = "Veuillez entrer un email ou un numéro de téléphone valide";
        }
        
        // verification s'il nya pas d'erreur
        if (!empty($sql) && empty($error)) {
            $db = config::getConnexion();
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':identifiant' => $username
            ]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && (password_verify($mdp, $user['mdp']) || $mdp === $user['mdp'])) {
                // AJOUT: Vérifier si la 2FA est activée
                if (isset($user['auth']) && trim(strtolower($user['auth'])) === 'active') {
                    // Générer un code de vérification à 6 chiffres
                    $verification_code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    // Stocker les informations de vérification dans la session
                    $_SESSION['2fa_required'] = true;
                    $_SESSION['2fa_user_id'] = $user['id_util'];
                    $_SESSION['2fa_code'] = $verification_code;
                    $_SESSION['2fa_expires'] = time() + 300; // Expire dans 5 minutes
                    
                    // Pour la démonstration, stocker le code dans la session (à retirer en production)
                    $_SESSION['debug_2fa_code'] = $verification_code;
                    
                    // Stocker temporairement les infos utilisateur
                    $_SESSION['temp_user_id'] = $user['id_util'];
                    $_SESSION['temp_user_name'] = $user['prenom'] . ' ' . $user['nom'];
                    $_SESSION['temp_user_type'] = $user['typee'];
                    $_SESSION['temp_user_email'] = $user['mail'];
                    $_SESSION['temp_profile_picture'] = $user['img'];
                    
                    $show_2fa_form = true;
                    
                    // AJOUT: Simuler l'envoi d'email (à remplacer par un vrai envoi)
                    // $utilisateurc->send2FACode($user['mail'], $verification_code);
                    
                } else {
                    // Connexion réussie sans 2FA
                    $_SESSION['user_id'] = $user['id_util'];
                    $_SESSION['user_name'] = $user['prenom'] . ' ' . $user['nom'];
                    $_SESSION['user_type'] = $user['typee'];
                    $_SESSION['user_email'] = $user['mail'];
                    
                    // STOCKER L'IMAGE DANS LA SESSION
                    $_SESSION['profile_picture'] = $user['img'];
                    
                    // ouvrir index.html
                    if ($user['typee'] === 'admin') {
                        header('Location: ../backoffice/dashboard.php');
                    } else {
                        header('Location: index1.php');
                    }
                    exit();
                }
            } else {
                $error = "Mot de passe incorrect";
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="assets/css/connexion.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    /* AJOUT: Styles pour la 2FA */
    .two-factor-container {
  margin-top: 30px;
  animation: fadeIn 0.8s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.two-factor-box {
  background: rgba(255, 255, 255, 0.08);
  padding: 25px;
  border-radius: 12px;
  border-left: 4px solid var(--engage-red);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
}

.two-factor-box::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, transparent, rgba(231, 76, 60, 0.05), transparent);
  z-index: -1;
}

.verification-code-input {
  font-size: 28px;
  letter-spacing: 12px;
  text-align: center;
  padding: 18px;
  margin: 20px 0;
  background: rgba(26, 26, 46, 0.6);
  border: 2px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: var(--engage-light);
  width: 100%;
  box-sizing: border-box;
  font-weight: 600;
  transition: all 0.3s ease;
  outline: none;
}

.verification-code-input:focus {
  border-color: var(--engage-red);
  box-shadow: 0 0 0 4px rgba(231, 76, 60, 0.2);
  background: rgba(26, 26, 46, 0.8);
  transform: translateY(-2px);
}

.verification-code-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
  letter-spacing: normal;
  font-size: 16px;
}

.timer {
  color: var(--engage-red);
  font-weight: 700;
  margin: 15px 0;
  font-size: 1.1rem;
  text-align: center;
  text-shadow: 0 0 10px rgba(231, 76, 60, 0.3);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 0.8;
  }
  50% {
    opacity: 1;
  }
}

.debug-code {
  background: rgba(46, 204, 113, 0.1);
  padding: 15px;
  border-radius: 12px;
  margin: 20px 0;
  font-family: 'Courier New', monospace;
  font-size: 20px;
  text-align: center;
  color: #2ecc71;
  border: 1px solid rgba(46, 204, 113, 0.3);
  letter-spacing: 8px;
  font-weight: bold;
  position: relative;
  overflow: hidden;
}

.debug-code::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(46, 204, 113, 0.1), transparent);
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  to {
    left: 100%;
  }
}

.resend-link {
  color: var(--engage-blue);
  cursor: pointer;
  text-decoration: none;
  font-size: 0.95rem;
  font-weight: 600;
  transition: all 0.3s ease;
  display: inline-block;
  position: relative;
  padding: 5px 0;
}

.resend-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--engage-blue);
  transition: width 0.3s ease;
}

.resend-link:hover {
  color: #5dade2;
  transform: translateX(5px);
}

.resend-link:hover::after {
  width: 100%;
}

.resend-link:disabled {
  color: rgba(255, 255, 255, 0.3);
  cursor: not-allowed;
  transform: none;
}

.resend-link:disabled::after {
  display: none;
}

/* Style pour le message d'instructions */
.verification-instruction {
  color: #bdc3c7;
  text-align: center;
  margin-bottom: 20px;
  font-size: 0.95rem;
  line-height: 1.5;
}

/* Style pour le lien Retour à la connexion */
.retour-link {
  color: #666;
  text-decoration: none;
  display: inline-block;
  padding: 10px 20px;
  border: 1px solid #666;
  border-radius: 5px;
  margin-top: 15px;
  transition: all 0.3s ease;
}

.retour-link:hover {
  background: #666;
  color: white;
}

/* Responsive */
@media (max-width: 480px) {
  .two-factor-box {
    padding: 20px;
    margin: 0 10px;
  }
  
  .verification-code-input {
    font-size: 22px;
    letter-spacing: 8px;
    padding: 15px;
  }
  
  .debug-code {
    font-size: 18px;
    letter-spacing: 6px;
    padding: 12px;
  }
  
  .retour-link {
    padding: 8px 16px;
    font-size: 0.9rem;
  }
}
  </style>
</head>
<body>
  <div class="login-container">
    <h1>Se connecter</h1>

    <?php if (!empty($error)): ?>
        <div class="error-message" style="color: red; margin-bottom: 15px; text-align: center;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($show_2fa_form) && $show_2fa_form): ?>
        <!-- AJOUT: Formulaire de vérification 2FA -->
        <div class="two-factor-container">
            <div class="two-factor-box">
                <h3>Vérification en deux étapes</h3>
                <p>Un code de vérification a été envoyé à votre adresse email.</p>
                
                <?php if (isset($_SESSION['debug_2fa_code'])): ?>
                    <div class="debug-code">
                        Code de test : <?php echo $_SESSION['debug_2fa_code']; ?>
                    </div>
                <?php endif; ?>
                
                <div class="timer" id="timer">
                    Le code expirera dans : <span id="countdown">5:00</span>
                </div>
                
                <form method="post">
                    <input type="text" name="verification_code" 
                           class="verification-code-input" 
                           placeholder="000000" 
                           maxlength="6" 
                           autocomplete="off">
                    
                    <input type="submit" name="verify_2fa_code" value="Vérifier" class="btn" style="width: 100%;">
                    
                    <div style="text-align: center; margin-top: 10px;">
                        <span class="resend-link" onclick="resendCode()">Renvoyer le code</span>
                    </div>
                </form>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="connexion.php?retour=1" class="retour-link">Retour à la connexion</a>
            </div>
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
            
            function resendCode() {
                // En production, vous enverriez une requête AJAX pour regénérer un code
                alert('Fonctionnalité de renvoi à implémenter');
            }
            
            // Auto-focus sur le champ code
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelector('.verification-code-input').focus();
            });
        </script>
        
    <?php else: ?>
        <!-- Formulaire de connexion original -->
        <form method="post">
            <input type="text" name="username" id="nom" placeholder="Adresse e-mail ou numéro tél "  
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">

            <input type="password" name="password" placeholder="Mot de passe" >

            <a href="mdp.php" class="forgot">Mot de passe oubliée?</a>

            <input type="submit" value="Se connecter" class="btn">
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <button type="button" id="face-login-btn" class="btn" style="background-color: #2c3e50; width: auto; padding: 10px 20px;">
                <i class="fas fa-camera"></i> Se connecter avec reconnaissance faciale
            </button>
        </div>
        <!-- Social Login -->
        <div class="social-login" style="margin-top: 20px; text-align: center;">
            <p style="color: #fff; margin-bottom: 10px; font-size: 0.9rem;">Ou se connecter avec</p>
            <div style="display: flex; justify-content: center; gap: 15px;">
                <a href="../../controller/social_auth.php?provider=google" title="Google" style="background: #db4437; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;"><i class="fab fa-google"></i></a>
                <a href="../../controller/social_auth.php?provider=facebook" title="Facebook" style="background: #4267B2; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;"><i class="fab fa-facebook-f"></i></a>
                <a href="../../controller/social_auth.php?provider=twitter" title="Twitter" style="background: #1DA1F2; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.2s;"><i class="fab fa-twitter"></i></a>
            </div>
            <style>
                .social-login a:hover { transform: scale(1.1); }
            </style>
        </div>
      
        <p class="signup">INSCRIVEZ-VOUS</p>
        <a href="inscription.php" class="signup-link">S'INSCRIRE</a>
        
        <!-- Face Login Button -->
        
    <?php endif; ?>
  </div>

    <!-- Face Login Modal -->
    <div id="face-login-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center; flex-direction: column;">
        <div style="background: white; padding: 20px; border-radius: 10px; text-align: center; position: relative; max-width: 90%; width: 500px;">
            <h2 style="color: #333; margin-bottom: 20px;">Reconnaissance Faciale</h2>
            <div style="position: relative; width: 100%; height: 350px; background: #000; margin-bottom: 20px; display: flex; justify-content: center;">
                <video id="video" width="480" height="360" autoplay muted style="position: absolute;"></video>
                <canvas id="overlay" style="position: absolute;"></canvas>
            </div>
            <p id="status-msg" style="color: #666; margin-bottom: 15px;">Chargement des modèles...</p>
            <button id="close-modal-btn" class="btn" style="background: #e74c3c;">Annuler</button>
        </div>
    </div>

    <!-- Face API JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const faceLoginBtn = document.getElementById('face-login-btn');
            const modal = document.getElementById('face-login-modal');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const video = document.getElementById('video');
            const statusMsg = document.getElementById('status-msg');
            let isModelLoaded = false;
            let stream = null;

            const MODEL_URL = 'https://justadudewhohacks.github.io/face-api.js/models'; 

            async function loadModels() {
                try {
                    statusMsg.innerText = "Chargement des modèles AI...";
                    await faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL);
                    await faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL);
                    await faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL);
                    isModelLoaded = true;
                    statusMsg.innerText = "Modèles chargés. Veuillez centrer votre visage.";
                    startVideo();
                } catch (err) {
                    console.error("Erreur chargement modèles:", err);
                    statusMsg.innerText = "Erreur de chargement des modèles. Vérifiez votre connexion.";
                }
            }

            function startVideo() {
                navigator.mediaDevices.getUserMedia({ video: {} })
                    .then(s => {
                        stream = s;
                        video.srcObject = stream;
                    })
                    .catch(err => {
                        console.error("Erreur caméra:", err);
                        statusMsg.innerText = "Impossible d'accéder à la caméra.";
                    });
            }

            function stopVideo() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                video.srcObject = null;
            }

            if (faceLoginBtn) {
                faceLoginBtn.addEventListener('click', () => {
                    modal.style.display = 'flex';
                    if (!isModelLoaded) {
                        loadModels();
                    } else {
                        startVideo();
                    }
                });
            }

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', () => {
                    modal.style.display = 'none';
                    stopVideo();
                });
            }

            video.addEventListener('play', () => {
                const canvas = document.getElementById('overlay');
                const displaySize = { width: 480, height: 360 }; 
                faceapi.matchDimensions(canvas, displaySize);

                const interval = setInterval(async () => {
                    if (modal.style.display === 'none') {
                        clearInterval(interval);
                        return;
                    }

                    const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
                    
                    if (detections.length > 0) {
                        const resizedDetections = faceapi.resizeResults(detections, displaySize);
                        canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
                        faceapi.draw.drawDetections(canvas, resizedDetections);

                        if (detections[0].descriptor) {
                            statusMsg.innerText = "Visage détecté ! Authentification...";
                            
                            const descriptorArray = Array.from(detections[0].descriptor);
                            
                            const formData = new FormData();
                            formData.append('face_login', '1');
                            formData.append('descriptor', JSON.stringify(descriptorArray));

                            fetch('connexion.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    statusMsg.innerText = "Succès ! Redirection...";
                                    statusMsg.style.color = "green";
                                    clearInterval(interval); 
                                    stopVideo();
                                    setTimeout(() => window.location.href = data.redirect, 1000);
                                } else {
                                    console.log("Auth failed:", data.message);
                                }
                            })
                            .catch(err => {
                                console.error(err);
                            });
                        }
                    }
                }, 500); 
            });
        });
    </script>
</body>
</html>
<?php
// AJOUT: Gérer l'annulation de la 2FA (pour compatibilité)
if (isset($_GET['cancel']) && $_GET['cancel'] == 1) {
    session_destroy();
    header('Location: connexion.php');
    exit();
}
?>